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

use SP\Core\Acl\Acl;

/**
 * Class AccountAcl
 *
 * @package SP\Account
 */
final class AccountAcl
{
    /**
     * @var bool
     */
    private $userInGroups = false;

    /**
     * @var bool
     */
    private $userInUsers = false;

    /**
     * @var bool
     */
    private $resultView = false;

    /**
     * @var bool
     */
    private $resultEdit = false;

    /**
     * @var bool
     */
    private $modified = false;

    /**
     * @var bool
     */
    private $showView = false;

    /**
     * @var bool
     */
    private $showHistory = false;

    /**
     * @var bool
     */
    private $showDetails = false;

    /**
     * @var bool
     */
    private $showPass = false;

    /**
     * @var bool
     */
    private $showFiles = false;

    /**
     * @var bool
     */
    private $showViewPass = false;

    /**
     * @var bool
     */
    private $showSave = false;

    /**
     * @var bool
     */
    private $showEdit = false;

    /**
     * @var bool
     */
    private $showEditPass = false;

    /**
     * @var bool
     */
    private $showDelete = false;

    /**
     * @var bool
     */
    private $showRestore = false;

    /**
     * @var bool
     */
    private $showLink = false;

    /**
     * @var bool
     */
    private $showCopy = false;

    /**
     * @var bool
     */
    private $showPermission = false;

    /**
     * @var bool
     */
    private $compiledAccountAccess = false;

    /**
     * @var bool
     */
    private $compiledShowAccess = false;

    /**
     * @var int
     */
    private $accountId;

    /**
     * @var int
     */
    private $actionId;

    /**
     * @var int
     */
    private $time = 0;

    /**
     * @var bool
     */
    private $isHistory;

    /**
     * AccountAcl constructor.
     *
     * @param int  $actionId
     * @param bool $isHistory
     */
    public function __construct($actionId, $isHistory = false)
    {
        $this->actionId = (int) $actionId;
        $this->isHistory = $isHistory;
    }

    public function isUserInGroups(): bool
    {
        return $this->userInGroups;
    }

    public function setUserInGroups(bool $userInGroups): self
    {
        $this->userInGroups = $userInGroups;

        return $this;
    }

    public function isUserInUsers(): bool
    {
        return $this->userInUsers;
    }

    public function setUserInUsers(bool $userInUsers): self
    {
        $this->userInUsers = $userInUsers;

        return $this;
    }

    public function isResultView(): bool
    {
        return $this->resultView;
    }

    public function setResultView(bool $resultView): self
    {
        $this->resultView = $resultView;

        return $this;
    }

    public function isResultEdit(): bool
    {
        return $this->resultEdit;
    }

    public function setResultEdit(bool $resultEdit): self
    {
        $this->resultEdit = $resultEdit;

        return $this;
    }

    public function isShowDetails(): bool
    {
        return $this->resultView && (in_array($this->actionId, [Acl::ACCOUNT_VIEW, Acl::ACCOUNT_HISTORY_VIEW, Acl::ACCOUNT_DELETE], true));
    }

    public function setShowDetails(bool $showDetails): self
    {
        $this->showDetails = $showDetails;

        return $this;
    }

    public function isShowPass(): bool
    {
        return ($this->actionId === Acl::ACCOUNT_CREATE
            || $this->actionId === Acl::ACCOUNT_COPY);
    }

    public function setShowPass(bool $showPass): self
    {
        $this->showPass = $showPass;

        return $this;
    }

    public function isShowFiles(): bool
    {
        return $this->showFiles
            && (in_array($this->actionId, [Acl::ACCOUNT_EDIT, Acl::ACCOUNT_VIEW, Acl::ACCOUNT_HISTORY_VIEW], true));
    }

    public function setShowFiles(bool $showFiles): self
    {
        $this->showFiles = $this->resultView && $showFiles;

        return $this;
    }

    public function isShowViewPass(): bool
    {
        return $this->showViewPass
            && (in_array($this->actionId, [Acl::ACCOUNT_SEARCH, Acl::ACCOUNT_VIEW, Acl::ACCOUNT_VIEW_PASS, Acl::ACCOUNT_HISTORY_VIEW, Acl::ACCOUNT_EDIT], true));
    }

    public function setShowViewPass(bool $showViewPass): self
    {
        $this->showViewPass = $this->resultView && $showViewPass;

        return $this;
    }

    public function isShowSave(): bool
    {
        return in_array($this->actionId, [Acl::ACCOUNT_EDIT, Acl::ACCOUNT_CREATE, Acl::ACCOUNT_COPY], true);
    }

    public function setShowSave(bool $showSave): self
    {
        $this->showSave = $showSave;

        return $this;
    }

    public function isShowEdit(): bool
    {
        return $this->showEdit
            && ($this->actionId === Acl::ACCOUNT_SEARCH
                || $this->actionId === Acl::ACCOUNT_VIEW);
    }

    public function setShowEdit(bool $showEdit): self
    {
        $this->showEdit = $this->resultEdit && $showEdit && !$this->isHistory;

        return $this;
    }

    public function isShowEditPass(): bool
    {
        return $this->showEditPass
            && ($this->actionId === Acl::ACCOUNT_EDIT
                || $this->actionId === Acl::ACCOUNT_VIEW);
    }

    public function setShowEditPass(bool $showEditPass): self
    {
        $this->showEditPass = $this->resultEdit && $showEditPass && !$this->isHistory;

        return $this;
    }

    public function isShowDelete(): bool
    {
        return $this->showDelete
            && (in_array($this->actionId, [Acl::ACCOUNT_SEARCH, Acl::ACCOUNT_DELETE, Acl::ACCOUNT_EDIT], true));
    }

    public function setShowDelete(bool $showDelete): self
    {
        $this->showDelete = $this->resultEdit && $showDelete;

        return $this;
    }

    public function isShowRestore(): bool
    {
        return $this->actionId === Acl::ACCOUNT_HISTORY_VIEW && $this->showRestore;
    }

    public function setShowRestore(bool $showRestore): self
    {
        $this->showRestore = $this->resultEdit && $showRestore;

        return $this;
    }

    public function isShowLink(): bool
    {
        return $this->showLink;
    }

    public function setShowLink(bool $showLink): self
    {
        $this->showLink = $showLink;

        return $this;
    }

    public function isShowHistory(): bool
    {
        return $this->showHistory
            && ($this->actionId === Acl::ACCOUNT_VIEW
                || $this->actionId === Acl::ACCOUNT_HISTORY_VIEW);
    }

    public function setShowHistory(bool $showHistory): self
    {
        $this->showHistory = $showHistory;

        return $this;
    }

    public function isShow(): bool
    {
        return ($this->showView || $this->showEdit || $this->showViewPass || $this->showCopy || $this->showDelete);
    }

    public function getActionId(): int
    {
        return $this->actionId;
    }

    /**
     * @param int $actionId
     */
    public function setActionId($actionId): self
    {
        $this->actionId = (int) $actionId;

        return $this;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    /**
     * @param int $time
     */
    public function setTime($time): self
    {
        $this->time = (int) $time;

        return $this;
    }

    /**
     * Comprueba los permisos de acceso a una cuenta.
     *
     *
     * @return bool
     */
    public function checkAccountAccess($actionId)
    {
        if ($this->compiledAccountAccess === false) {
            return false;
        }

        return match ($actionId) {
            Acl::ACCOUNT_VIEW, Acl::ACCOUNT_SEARCH, Acl::ACCOUNT_VIEW_PASS, Acl::ACCOUNT_HISTORY_VIEW, Acl::ACCOUNT_COPY => $this->resultView,
            Acl::ACCOUNT_EDIT, Acl::ACCOUNT_DELETE, Acl::ACCOUNT_EDIT_PASS, Acl::ACCOUNT_EDIT_RESTORE => $this->resultEdit,
            default => false,
        };
    }

    public function isModified(): bool
    {
        return $this->modified;
    }

    /**
     * @param bool $modified
     */
    public function setModified($modified): self
    {
        $this->modified = $modified;

        return $this;
    }

    public function isShowView(): bool
    {
        return $this->showView;
    }

    public function setShowView(bool $showView): self
    {
        $this->showView = $this->resultView && $showView;

        return $this;
    }

    public function isShowCopy(): bool
    {
        return $this->showCopy
            && (in_array($this->actionId, [Acl::ACCOUNT_SEARCH, Acl::ACCOUNT_VIEW, Acl::ACCOUNT_EDIT], true));
    }

    public function setShowCopy(bool $showCopy): self
    {
        $this->showCopy = $this->resultView && $showCopy;

        return $this;
    }

    public function isShowPermission(): bool
    {
        return $this->showPermission;
    }

    public function setShowPermission(bool $showPermission): self
    {
        $this->showPermission = $showPermission;

        return $this;
    }

    public function getAccountId(): int
    {
        return $this->accountId;
    }

    /**
     * @param int $accountId
     */
    public function setAccountId($accountId): self
    {
        $this->accountId = (int) $accountId;

        return $this;
    }

    public function isHistory(): bool
    {
        return $this->isHistory;
    }

    public function setIsHistory(bool $isHistory): self
    {
        $this->isHistory = $isHistory;

        return $this;
    }

    public function isCompiledShowAccess(): bool
    {
        return $this->compiledShowAccess;
    }

    /**
     * @param bool $compiledShowAccess
     */
    public function setCompiledShowAccess($compiledShowAccess): self
    {
        $this->compiledShowAccess = (bool) $compiledShowAccess;

        return $this;
    }

    public function isCompiledAccountAccess(): bool
    {
        return $this->compiledAccountAccess;
    }

    /**
     * @param bool $compiledAccountAccess
     */
    public function setCompiledAccountAccess($compiledAccountAccess): self
    {
        $this->compiledAccountAccess = (bool) $compiledAccountAccess;

        return $this;
    }

    public function reset(): void
    {
        foreach ($this as $property => $value) {
            if (str_starts_with($property, 'show')) {
                $this->{$property} = false;
            }
        }
    }
}
