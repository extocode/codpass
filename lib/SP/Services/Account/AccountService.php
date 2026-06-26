<?php

declare(strict_types=1);
/**
 * sysPass
 *
 * @author    nuxsmin
 * @link      https://syspass.org
 * @copyright 2012-2019, Rubén Domínguez nuxsmin@$syspass.org
 *
 * This file is part of sysPass.
 *
 * sysPass is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * sysPass is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 *  along with sysPass.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SP\Services\Account;

use Defuse\Crypto\Exception\CryptoException;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SP\Core\Crypt\Crypt;
use SP\Core\Exceptions\ConstraintException;
use SP\Core\Exceptions\NoSuchPropertyException;
use SP\Core\Exceptions\QueryException;
use SP\Core\Exceptions\SPException;
use SP\DataModel\AccountData;
use SP\DataModel\AccountExtData;
use SP\DataModel\AccountHistoryData;
use SP\DataModel\AccountPassData;
use SP\DataModel\Dto\AccountDetailsResponse;
use SP\DataModel\Dto\AccountHistoryCreateDto;
use SP\DataModel\ItemPreset\AccountPermission;
use SP\DataModel\ItemPreset\AccountPrivate;
use SP\DataModel\ItemSearchData;
use SP\Repositories\Account\AccountRepository;
use SP\Repositories\Account\AccountToTagRepository;
use SP\Repositories\Account\AccountToUserGroupRepository;
use SP\Repositories\Account\AccountToUserRepository;
use SP\Repositories\NoSuchItemException;
use SP\Services\Config\ConfigService;
use SP\Services\ItemPreset\ItemPresetInterface;
use SP\Services\ItemPreset\ItemPresetService;
use SP\Services\Service;
use SP\Services\ServiceException;
use SP\Services\ServiceItemTrait;
use SP\Storage\Database\QueryResult;
use stdClass;

/**
 * Class AccountService
 *
 * @package SP\Services\Account
 */
final class AccountService extends Service implements AccountServiceInterface
{
    use ServiceItemTrait;

    /**
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * @var AccountToUserGroupRepository
     */
    protected $accountToUserGroupRepository;

    /**
     * @var AccountToUserRepository
     */
    protected $accountToUserRepository;

    /**
     * @var AccountToTagRepository
     */
    protected $accountToTagRepository;

    /**
     * @var ItemPresetService
     */
    protected $itemPresetService;

    /**
     *
     * @throws QueryException
     * @throws ConstraintException
     */
    public function withUsersById(AccountDetailsResponse $accountDetailsResponse): self
    {
        $accountDetailsResponse->setUsers($this->accountToUserRepository->getUsersByAccountId($accountDetailsResponse->getId())->getDataAsArray());

        return $this;
    }

    /**
     *
     * @throws QueryException
     * @throws ConstraintException
     */
    public function withUserGroupsById(AccountDetailsResponse $accountDetailsResponse): self
    {
        $accountDetailsResponse->setUserGroups($this->accountToUserGroupRepository->getUserGroupsByAccountId($accountDetailsResponse->getId())->getDataAsArray());

        return $this;
    }

    /**
     *
     * @throws QueryException
     * @throws ConstraintException
     */
    public function withTagsById(AccountDetailsResponse $accountDetailsResponse): self
    {
        $accountDetailsResponse->setTags($this->accountToTagRepository->getTagsByAccountId($accountDetailsResponse->getId())->getDataAsArray());

        return $this;
    }

    /**
     * @param $id
     *
     * @return bool
     * @throws QueryException
     * @throws ConstraintException
     */
    public function incrementViewCounter($id)
    {
        return $this->accountRepository->incrementViewCounter($id);
    }

    /**
     * @param $id
     *
     * @return bool
     * @throws QueryException
     * @throws ConstraintException
     */
    public function incrementDecryptCounter($id)
    {
        return $this->accountRepository->incrementDecryptCounter($id);
    }

    /**
     * @param $id
     *
     * @return AccountPassData
     * @throws QueryException
     * @throws ConstraintException
     * @throws NoSuchItemException
     */
    public function getPasswordForId($id)
    {
        $queryFilter = $this->dic->get(AccountFilterUser::class)->getFilter();

        $result = $this->accountRepository->getPasswordForId($id, $queryFilter);

        if ($result->getNumRows() === 0) {
            throw new NoSuchItemException(__u('Account not found'));
        }

        return $result->getData();
    }

    /**
     *
     * @return int
     * @throws QueryException
     * @throws SPException
     * @throws ConstraintException
     * @throws NoSuchPropertyException
     */
    public function create(AccountRequest $accountRequest)
    {
        $userData = $this->context->getUserData();

        $accountRequest->changePermissions = AccountAclService::getShowPermission(
            $userData,
            $this->context->getUserProfile()
        );

        if (empty($accountRequest->userGroupId)
            || !$accountRequest->changePermissions
        ) {
            $accountRequest->userGroupId = $userData->getUserGroupId();
        }

        if (empty($accountRequest->userId)
            || !$accountRequest->changePermissions
        ) {
            $accountRequest->userId = $userData->getId();
        }

        if (empty($accountRequest->key)) {
            $pass = $this->getPasswordEncrypted($accountRequest->pass);

            $accountRequest->pass = $pass['pass'];
            $accountRequest->key = $pass['key'];
        }

        $this->setPresetPrivate($accountRequest);

        $accountRequest->id = $this->accountRepository->create($accountRequest);

        $this->addItems($accountRequest);

        $this->addPresetPermissions($accountRequest->id);

        return $accountRequest->id;
    }

    /**
     * Devolver los datos de la clave encriptados
     *
     * @param string $pass
     * @param string $masterPass Clave maestra a utilizar
     *
     * @return array
     * @throws ServiceException
     */
    public function getPasswordEncrypted($pass, $masterPass = null)
    {
        try {
            if ($masterPass === null) {
                $masterPass = $this->getMasterKeyFromContext();
            }

            if (empty($masterPass)) {
                throw new ServiceException(__u('Master password not set'));
            }

            $out['key'] = Crypt::makeSecuredKey($masterPass);
            $out['pass'] = Crypt::encrypt($pass, $out['key'], $masterPass);

            if (strlen($out['pass']) > 1000 || strlen($out['key']) > 1000) {
                throw new ServiceException(__u('Internal error'));
            }

            return $out;
        } catch (CryptoException) {
            throw new ServiceException(__u('Internal error'));
        }
    }

    /**
     *
     * @throws QueryException
     * @throws ConstraintException
     * @throws NoSuchPropertyException
     * @throws NoSuchItemException
     */
    private function setPresetPrivate(AccountRequest $accountRequest): void
    {
        $userData = $this->context->getUserData();
        $itemPreset = $this->itemPresetService->getForCurrentUser(ItemPresetInterface::ITEM_TYPE_ACCOUNT_PRIVATE);

        if ($itemPreset !== null
            && $itemPreset->getFixed()
        ) {
            $accountPrivate = $itemPreset->hydrate(AccountPrivate::class);

            $userId = $accountRequest->userId;

            if ($userId === null && $accountRequest->id > 0) {
                $userId = $this->getById($accountRequest->id)->getAccountVData()->getUserId();
            }

            if ($userData->getId() === $userId) {
                $accountRequest->isPrivate = (int) $accountPrivate->isPrivateUser();
            }

            if ($userData->getUserGroupId() === $accountRequest->userGroupId) {
                $accountRequest->isPrivateGroup = (int) $accountPrivate->isPrivateGroup();
            }
        }
    }

    /**
     * @param int $id
     *
     * @throws QueryException
     * @throws NoSuchItemException
     * @throws ConstraintException
     */
    public function getById($id): \SP\DataModel\Dto\AccountDetailsResponse
    {
        $result = $this->accountRepository->getById($id);

        if ($result->getNumRows() === 0) {
            throw new NoSuchItemException(__u('The account doesn\'t exist'));
        }

        return new AccountDetailsResponse($id, $result->getData());
    }

    /**
     * Adds external items to the account
     */
    private function addItems(AccountRequest $accountRequest): void
    {
        try {
            if ($accountRequest->changePermissions) {
                if (is_array($accountRequest->userGroupsView)
                    && ($accountRequest->userGroupsView !== null && $accountRequest->userGroupsView !== [])
                ) {
                    $this->accountToUserGroupRepository->addByType($accountRequest, false);
                }

                if (is_array($accountRequest->userGroupsEdit)
                    && ($accountRequest->userGroupsEdit !== null && $accountRequest->userGroupsEdit !== [])
                ) {
                    $this->accountToUserGroupRepository->addByType($accountRequest, true);
                }

                if (is_array($accountRequest->usersView)
                    && ($accountRequest->usersView !== null && $accountRequest->usersView !== [])
                ) {
                    $this->accountToUserRepository->addByType($accountRequest, false);
                }

                if (is_array($accountRequest->usersEdit)
                    && ($accountRequest->usersEdit !== null && $accountRequest->usersEdit !== [])
                ) {
                    $this->accountToUserRepository->addByType($accountRequest, true);
                }
            }

            if (is_array($accountRequest->tags)
                && ($accountRequest->tags !== null && $accountRequest->tags !== [])
            ) {
                $this->accountToTagRepository->add($accountRequest);
            }
        } catch (SPException $e) {
            logger($e->getMessage());
        }
    }

    /**
     *
     * @throws QueryException
     * @throws ConstraintException
     * @throws NoSuchPropertyException
     */
    private function addPresetPermissions(int $accountId): void
    {
        $itemPresetData = $this->itemPresetService->getForCurrentUser(ItemPresetInterface::ITEM_TYPE_ACCOUNT_PERMISSION);

        if ($itemPresetData !== null
            && $itemPresetData->getFixed()
        ) {
            $userData = $this->context->getUserData();
            $accountPermission = $itemPresetData->hydrate(AccountPermission::class);

            $accountRequest = new AccountRequest();
            $accountRequest->id = $accountId;
            $accountRequest->usersView = array_diff($accountPermission->getUsersView(), [$userData->getId()]);
            $accountRequest->usersEdit = array_diff($accountPermission->getUsersEdit(), [$userData->getId()]);
            $accountRequest->userGroupsView = array_diff($accountPermission->getUserGroupsView(), [$userData->getUserGroupId()]);
            $accountRequest->userGroupsEdit = array_diff($accountPermission->getUserGroupsEdit(), [$userData->getUserGroupId()]);

            if ($accountRequest->usersView !== null && $accountRequest->usersView !== []) {
                $this->accountToUserRepository->addByType($accountRequest, false);
            }

            if ($accountRequest->usersEdit !== null && $accountRequest->usersEdit !== []) {
                $this->accountToUserRepository->addByType($accountRequest, true);
            }

            if ($accountRequest->userGroupsView !== null && $accountRequest->userGroupsView !== []) {
                $this->accountToUserGroupRepository->addByType($accountRequest, false);
            }

            if ($accountRequest->userGroupsEdit !== null && $accountRequest->userGroupsEdit !== []) {
                $this->accountToUserGroupRepository->addByType($accountRequest, true);
            }
        }
    }

    /**
     *
     * @return int
     * @throws QueryException
     * @throws ConstraintException
     */
    public function createFromHistory(AccountHistoryData $data)
    {
        $accountRequest = new AccountRequest();
        $accountRequest->name = $data->getName();
        $accountRequest->categoryId = $data->getCategoryId();
        $accountRequest->clientId = $data->getClientId();
        $accountRequest->url = $data->getUrl();
        $accountRequest->login = $data->getLogin();
        $accountRequest->pass = $data->getPass();
        $accountRequest->key = $data->getKey();
        $accountRequest->notes = $data->getNotes();
        $accountRequest->userId = $data->getUserId();
        $accountRequest->userGroupId = $data->getUserGroupId();
        $accountRequest->passDateChange = $data->getPassDateChange();
        $accountRequest->parentId = $data->getParentId();
        $accountRequest->isPrivate = $data->getIsPrivate();
        $accountRequest->isPrivateGroup = $data->getIsPrivateGroup();

        return $this->accountRepository->create($accountRequest);
    }

    /**
     * Updates external items for the account
     *
     *
     * @throws Exception
     */
    public function update(AccountRequest $accountRequest): void
    {
        $this->transactionAware(function () use ($accountRequest): void {
            $userData = $this->context->getUserData();
            $userProfile = $this->context->getUserProfile();

            $accountRequest->changePermissions =
                AccountAclService::getShowPermission($userData, $userProfile);

            if ($accountRequest->changePermissions) {
                $account = $this->getById($accountRequest->id)->getAccountVData();

                $accountRequest->changeOwner = $accountRequest->userId > 0
                    && ($userData->getIsAdminApp()
                        || $userData->getIsAdminAcc()
                        || ($userProfile->isAccPermission()
                            && $userData->getId() === $account->getUserId()));

                $accountRequest->changeUserGroup = $accountRequest->userGroupId > 0
                    && ($userData->getIsAdminApp()
                        || $userData->getIsAdminAcc()
                        || ($userProfile->isAccPermission()
                            && ($userData->getUserGroupId() === $account->getUserGroupId())
                            || $userData->getId() === $account->getUserId()));
            }

            $this->addHistory($accountRequest->id);

            $this->setPresetPrivate($accountRequest);

            $this->accountRepository->update($accountRequest);

            $this->updateItems($accountRequest);

            $this->addPresetPermissions($accountRequest->id);
        });
    }

    /**
     * @param int  $accountId
     * @param bool $isDelete
     *
     * @return bool
     * @throws NoSuchItemException
     * @throws QueryException
     * @throws ServiceException
     * @throws ConstraintException
     */
    private function addHistory($accountId, $isDelete = false)
    {
        $accountHistoryRepository = $this->dic->get(AccountHistoryService::class);
        $configService = $this->dic->get(ConfigService::class);

        return $accountHistoryRepository->create(
            new AccountHistoryCreateDto(
                $accountId,
                !$isDelete,
                $isDelete,
                $configService->getByParam('masterPwd')
            )
        );
    }

    /**
     * Updates external items for the account
     *
     *
     * @throws QueryException
     * @throws ConstraintException
     */
    private function updateItems(AccountRequest $accountRequest): void
    {
        if ($accountRequest->changePermissions) {
            if ($accountRequest->userGroupsView !== null) {
                if (count($accountRequest->userGroupsView) > 0) {
                    $this->accountToUserGroupRepository->updateByType($accountRequest, false);
                } else {
                    $this->accountToUserGroupRepository->deleteTypeByAccountId($accountRequest->id, false);
                }
            }

            if ($accountRequest->userGroupsEdit !== null) {
                if (count($accountRequest->userGroupsEdit) > 0) {
                    $this->accountToUserGroupRepository->updateByType($accountRequest, true);
                } else {
                    $this->accountToUserGroupRepository->deleteTypeByAccountId($accountRequest->id, true);
                }
            }

            if ($accountRequest->usersView !== null) {
                if (count($accountRequest->usersView) > 0) {
                    $this->accountToUserRepository->updateByType($accountRequest, false);
                } else {
                    $this->accountToUserRepository->deleteTypeByAccountId($accountRequest->id, false);
                }
            }

            if ($accountRequest->usersEdit !== null) {
                if (count($accountRequest->usersEdit) > 0) {
                    $this->accountToUserRepository->updateByType($accountRequest, true);
                } else {
                    $this->accountToUserRepository->deleteTypeByAccountId($accountRequest->id, true);
                }
            }
        }

        if ($accountRequest->tags !== null) {
            if (count($accountRequest->tags) > 0) {
                $this->accountToTagRepository->update($accountRequest);
            } else {
                $this->accountToTagRepository->deleteByAccountId($accountRequest->id);
            }
        }
    }

    /**
     * Update accounts in bulk mode
     *
     *
     * @throws ServiceException
     */
    public function updateBulk(AccountBulkRequest $request): void
    {
        $this->transactionAware(function () use ($request): void {
            foreach ($request->getItemsId() as $itemId) {
                $accountRequest = $request->getAccountRequestForId($itemId);

                $this->addHistory($accountRequest->id);

                $this->accountRepository->updateBulk($accountRequest);

                $this->updateItems($accountRequest);
            }
        });
    }

    /**
     * @throws Exception
     */
    public function editPassword(AccountRequest $accountRequest): void
    {
        $this->transactionAware(function () use ($accountRequest): void {
            $this->addHistory($accountRequest->id);

            $pass = $this->getPasswordEncrypted($accountRequest->pass);

            $accountRequest->pass = $pass['pass'];
            $accountRequest->key = $pass['key'];

            $this->accountRepository->editPassword($accountRequest);
        });
    }

    /**
     * Updates an already encrypted password data from a master password changing action
     *
     *
     * @return bool
     * @throws ConstraintException
     * @throws QueryException
     */
    public function updatePasswordMasterPass(AccountPasswordRequest $accountRequest)
    {
        return $this->accountRepository->updatePassword($accountRequest);
    }

    /**
     * @param $historyId
     * @param $accountId
     *
     * @throws Exception
     */
    public function editRestore($historyId, $accountId): void
    {
        $this->transactionAware(function () use ($historyId, $accountId): void {
            $this->addHistory($accountId);

            if (!$this->accountRepository->editRestore($historyId, $this->context->getUserData()->getId())) {
                throw new ServiceException(__u('Error on restoring the account'));
            }
        });
    }

    /**
     * @param $id
     *
     * @throws ServiceException
     */
    public function delete($id): self
    {
        $this->transactionAware(function () use ($id): void {
            $this->addHistory($id, true);

            if ($this->accountRepository->delete($id) === 0) {
                throw new NoSuchItemException(__u('Account not found'));
            }
        });

        return $this;
    }

    /**
     *
     * @throws SPException
     * @throws ServiceException
     */
    public function deleteByIdBatch(array $ids): self
    {
        if ($this->accountRepository->deleteByIdBatch($ids) === 0) {
            throw new ServiceException(__u('Error while deleting the accounts'));
        }

        return $this;
    }

    /**
     * @param $accountId
     *
     * @throws QueryException
     * @throws ConstraintException
     */
    public function getForUser($accountId = null): array
    {
        $queryFilter = $this->dic->get(AccountFilterUser::class)->getFilter();

        if (null !== $accountId) {
            $queryFilter->addFilter('Account.id <> ? AND (Account.parentId = 0 OR Account.parentId IS NULL)', [$accountId]);
        }

        return $this->accountRepository->getForUser($queryFilter)->getDataAsArray();
    }

    /**
     * @param $accountId
     *
     * @throws QueryException
     * @throws ConstraintException
     */
    public function getLinked($accountId): array
    {
        $queryFilter = $this->dic->get(AccountFilterUser::class)->getFilter();

        $queryFilter->addFilter('Account.parentId = ?', [$accountId]);

        return $this->accountRepository->getLinked($queryFilter)->getDataAsArray();
    }

    /**
     * @param $id
     *
     * @return AccountPassData
     * @throws QueryException
     * @throws ConstraintException
     * @throws NoSuchItemException
     */
    public function getPasswordHistoryForId($id)
    {
        $queryFilter = $this->dic->get(AccountFilterUser::class)->getFilterHistory();
        $queryFilter->addFilter('AccountHistory.id = ?', [$id]);

        $result = $this->accountRepository->getPasswordHistoryForId($queryFilter);

        if ($result->getNumRows() === 0) {
            throw new NoSuchItemException(__u('The account doesn\'t exist'));
        }

        return $result->getData();
    }

    /**
     * @return AccountData[]
     * @throws QueryException
     * @throws ConstraintException
     */
    public function getAllBasic(): array
    {
        return $this->accountRepository->getAll()->getDataAsArray();
    }

    /**
     *
     * @return QueryResult
     * @throws QueryException
     * @throws ConstraintException
     */
    public function search(ItemSearchData $itemSearchData)
    {
        return $this->accountRepository->search($itemSearchData);
    }

    /**
     * Devolver el número total de cuentas
     *
     * @return stdClass
     * @throws QueryException
     * @throws ConstraintException
     */
    public function getTotalNumAccounts()
    {
        return $this->accountRepository->getTotalNumAccounts()->num;
    }

    /**
     * Obtener los datos de una cuenta.
     *
     * @param $id
     *
     * @return AccountExtData
     * @throws QueryException
     * @throws NoSuchItemException
     * @throws ConstraintException
     */
    public function getDataForLink($id)
    {
        $result = $this->accountRepository->getDataForLink($id);

        if ($result->getNumRows() === 0) {
            throw new NoSuchItemException(__u('The account doesn\'t exist'));
        }

        return $result->getData();
    }

    /**
     * Obtener los datos relativos a la clave de todas las cuentas.
     *
     * @return array Con los datos de la clave
     * @throws QueryException
     * @throws ConstraintException
     */
    public function getAccountsPassData()
    {
        return $this->accountRepository->getAccountsPassData();
    }

    /**
     * Obtener las cuentas de una búsqueda.
     *
     *
     * @return QueryResult
     * @throws QueryException
     * @throws SPException
     * @throws ConstraintException
     */
    public function getByFilter(AccountSearchFilter $accountSearchFilter)
    {
        $accountFilterUser = $this->dic->get(AccountFilterUser::class);

        return $this->accountRepository->getByFilter(
            $accountSearchFilter,
            $accountFilterUser->getFilter($accountSearchFilter->getGlobalSearch())
        );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function initialize(): void
    {
        $this->accountRepository = $this->dic->get(AccountRepository::class);
        $this->accountToUserRepository = $this->dic->get(AccountToUserRepository::class);
        $this->accountToUserGroupRepository = $this->dic->get(AccountToUserGroupRepository::class);
        $this->accountToTagRepository = $this->dic->get(AccountToTagRepository::class);
        $this->itemPresetService = $this->dic->get(ItemPresetService::class);
    }
}
