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

namespace SP\DataModel;

defined('APP_ROOT') || die();

use JsonSerializable;
use SP\Http\Json;

/**
 * Class AccountData
 *
 * @package SP\Account
 */
class AccountData extends DataModelBase implements JsonSerializable, DataModelInterface
{
    /**
     * @var int Id de la cuenta.
     */
    public $id = 0;

    /**
     * @var int Id del usuario principal de la cuenta.
     */
    public $userId = 0;

    /**
     * @var int Id del grupo principal de la cuenta.
     */
    public $userGroupId = 0;

    /**
     * @var int Id del usuario que editó la cuenta.
     */
    public $userEditId = 0;

    /**
     * @var string El nombre de la cuenta.
     */
    public $name = '';

    /**
     * @var int Id del cliente de la cuenta.
     */
    public $clientId = 0;

    /**
     * @var int Id de la categoría de la cuenta.
     */
    public $categoryId = 0;

    /**
     * @var string El nombre de usuario de la cuenta.
     */
    public $login = '';

    /**
     * @var string La URL de la cuenta.
     */
    public $url = '';

    /**
     * @var string La clave de la cuenta.
     */
    public $pass = '';

    /**
     * @var string La clave de encriptación de la cuenta
     */
    public $key = '';

    /**
     * @var string Las nosta de la cuenta.
     */
    public $notes = '';

    /**
     * @var bool Si se permite la edición por los usuarios secundarios.
     */
    public $otherUserEdit = false;

    /**
     * @var bool Si se permita la edición por los grupos secundarios.
     */
    public $otherUserGroupEdit = false;

    /**
     * @var int
     */
    public $dateAdd = 0;

    /**
     * @var int
     */
    public $dateEdit = 0;

    /**
     * @var int
     */
    public $countView = 0;

    /**
     * @var int
     */
    public $countDecrypt = 0;

    /**
     * @var int
     */
    public $isPrivate = 0;

    /**
     * @var int
     */
    public $isPrivateGroup = 0;

    /**
     * @var int
     */
    public $passDate = 0;

    /**
     * @var int
     */
    public $passDateChange = 0;

    /**
     * @var int
     */
    public $parentId = 0;

    /**
     * AccountData constructor.
     *
     * @param int $accountId
     */
    public function __construct($accountId = 0)
    {
        $this->id = (int) $accountId;
    }

    /**
     * @return int
     */
    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    /**
     * @param int $dateAdd
     */
    public function setDateAdd($dateAdd): void
    {
        $this->dateAdd = $dateAdd;
    }

    /**
     * @return int
     */
    public function getDateEdit()
    {
        return $this->dateEdit;
    }

    /**
     * @param int $dateEdit
     */
    public function setDateEdit($dateEdit): void
    {
        $this->dateEdit = $dateEdit;
    }

    public function getUserEditId(): int
    {
        return (int) $this->userEditId;
    }

    /**
     * @param int $userEditId
     */
    public function setUserEditId($userEditId): void
    {
        $this->userEditId = (int) $userEditId;
    }

    /**
     * @return string
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * @param string $pass
     */
    public function setPass($pass): void
    {
        $this->pass = $pass;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key): void
    {
        $this->key = $key;
    }

    public function getId(): int
    {
        return (int) $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): void
    {
        $this->id = (int) $id;
    }

    public function getUserId(): int
    {
        return (int) $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId): void
    {
        $this->userId = (int) $userId;
    }

    public function getUserGroupId(): int
    {
        return (int) $this->userGroupId;
    }

    /**
     * @param int $userGroupId
     */
    public function setUserGroupId($userGroupId): void
    {
        $this->userGroupId = (int) $userGroupId;
    }

    public function getOtherUserEdit(): int
    {
        return (int) $this->otherUserEdit;
    }

    /**
     * @param bool $otherUserEdit
     */
    public function setOtherUserEdit($otherUserEdit): void
    {
        $this->otherUserEdit = (int) $otherUserEdit;
    }

    public function getOtherUserGroupEdit(): int
    {
        return (int) $this->otherUserGroupEdit;
    }

    /**
     * @param bool $otherUserGroupEdit
     */
    public function setOtherUserGroupEdit($otherUserGroupEdit): void
    {
        $this->otherUserGroupEdit = (int) $otherUserGroupEdit;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getCategoryId(): int
    {
        return (int) $this->categoryId;
    }

    /**
     * @param int $categoryId
     */
    public function setCategoryId($categoryId): void
    {
        $this->categoryId = (int) $categoryId;
    }

    public function getClientId(): int
    {
        return (int) $this->clientId;
    }

    /**
     * @param int $clientId
     */
    public function setClientId($clientId): void
    {
        $this->clientId = (int) $clientId;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     */
    public function setLogin($login): void
    {
        $this->login = $login;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     */
    public function setNotes($notes): void
    {
        $this->notes = $notes;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *        which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize(): mixed
    {
        $data = get_object_vars($this);

        unset($data['accountPass'], $data['accountIV']);

        return Json::safeJson($data);
    }

    public function getCountView(): int
    {
        return (int) $this->countView;
    }

    /**
     * @param int $countView
     */
    public function setCountView($countView): void
    {
        $this->countView = (int) $countView;
    }

    public function getCountDecrypt(): int
    {
        return (int) $this->countDecrypt;
    }

    /**
     * @param int $countDecrypt
     */
    public function setCountDecrypt($countDecrypt): void
    {
        $this->countDecrypt = (int) $countDecrypt;
    }

    public function getIsPrivate(): int
    {
        return (int) $this->isPrivate;
    }

    /**
     * @param int $isPrivate
     */
    public function setIsPrivate($isPrivate): void
    {
        $this->isPrivate = (int) $isPrivate;
    }

    public function getPassDate(): int
    {
        return (int) $this->passDate;
    }

    /**
     * @param int $passDate
     */
    public function setPassDate($passDate): void
    {
        $this->passDate = (int) $passDate;
    }

    public function getPassDateChange(): int
    {
        return (int) $this->passDateChange;
    }

    /**
     * @param int $passDateChange
     */
    public function setPassDateChange($passDateChange): void
    {
        $this->passDateChange = (int) $passDateChange;
    }

    public function getParentId(): int
    {
        return (int) $this->parentId;
    }

    /**
     * @param int $parentId
     */
    public function setParentId($parentId): void
    {
        $this->parentId = (int) $parentId;
    }

    public function getIsPrivateGroup(): int
    {
        return (int) $this->isPrivateGroup;
    }

    /**
     * @param int $isPrivateGroup
     */
    public function setIsPrivateGroup($isPrivateGroup): void
    {
        $this->isPrivateGroup = (int) $isPrivateGroup;
    }
}
