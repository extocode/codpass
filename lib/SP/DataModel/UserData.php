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

/**
 * Class UserBasicData
 *
 * @package SP\DataModel
 */
class UserData extends UserPassData implements DataModelInterface
{
    /**
     * @var string
     */
    public $login;

    /**
     * @var string
     */
    public $ssoLogin;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $notes;

    /**
     * @var int
     */
    public $userGroupId = 0;

    /**
     * @var int
     */
    public $userProfileId = 0;

    /**
     * @var bool
     */
    public $isAdminApp = 0;

    /**
     * @var bool
     */
    public $isAdminAcc = 0;

    /**
     * @var bool
     */
    public $isDisabled = 0;

    /**
     * @var bool
     */
    public $isChangePass = 0;

    /**
     * @var bool
     */
    public $isChangedPass = 0;

    /**
     * @var bool
     */
    public $isLdap = 0;

    /**
     * @var int
     */
    public $loginCount = 0;

    /**
     * @var string
     */
    public $lastLogin;

    /**
     * @var string
     */
    public $lastUpdate;

    /**
     * @var bool
     */
    public $isMigrate = 0;

    /**
     * @var string
     */
    public $preferences;

    /**
     * @var string
     */
    public $userGroupName;

    public function getLoginCount(): int
    {
        return (int) $this->loginCount;
    }

    /**
     * @param int $loginCount
     */
    public function setLoginCount($loginCount): void
    {
        $this->loginCount = (int) $loginCount;
    }

    /**
     * @return string
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * @param string $lastLogin
     */
    public function setLastLogin($lastLogin): void
    {
        $this->lastLogin = $lastLogin;
    }

    /**
     * @return string
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * @param string $lastUpdate
     */
    public function setLastUpdate($lastUpdate): void
    {
        $this->lastUpdate = $lastUpdate;
    }

    public function isMigrate(): int
    {
        return (int) $this->isMigrate;
    }

    /**
     * @param bool $isMigrate
     */
    public function setIsMigrate($isMigrate): void
    {
        $this->isMigrate = (int) $isMigrate;
    }

    /**
     * @return string
     */
    public function getPreferences()
    {
        return $this->preferences;
    }

    /**
     * @param string $preferences
     */
    public function setPreferences($preferences): void
    {
        $this->preferences = $preferences;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
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

    public function getUserProfileId(): int
    {
        return (int) $this->userProfileId;
    }

    /**
     * @param int $userProfileId
     */
    public function setUserProfileId($userProfileId): void
    {
        $this->userProfileId = (int) $userProfileId;
    }

    public function isAdminApp(): int
    {
        return (int) $this->isAdminApp;
    }

    /**
     * @param bool $isAdminApp
     */
    public function setIsAdminApp($isAdminApp): void
    {
        $this->isAdminApp = (int) $isAdminApp;
    }

    public function isAdminAcc(): int
    {
        return (int) $this->isAdminAcc;
    }

    /**
     * @param bool $isAdminAcc
     */
    public function setIsAdminAcc($isAdminAcc): void
    {
        $this->isAdminAcc = (int) $isAdminAcc;
    }

    public function isDisabled(): int
    {
        return (int) $this->isDisabled;
    }

    /**
     * @param bool $isDisabled
     */
    public function setIsDisabled($isDisabled): void
    {
        $this->isDisabled = (int) $isDisabled;
    }

    public function isChangePass(): int
    {
        return (int) $this->isChangePass;
    }

    /**
     * @param bool $isChangePass
     */
    public function setIsChangePass($isChangePass): void
    {
        $this->isChangePass = (int) $isChangePass;
    }

    public function isLdap(): int
    {
        return (int) $this->isLdap;
    }

    /**
     * @param bool $isLdap
     */
    public function setIsLdap($isLdap): void
    {
        $this->isLdap = (int) $isLdap;
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

    /**
     * @return string
     */
    public function getUserGroupName()
    {
        return $this->userGroupName;
    }

    /**
     * @param string $userGroupName
     */
    public function setUserGroupName($userGroupName): void
    {
        $this->userGroupName = $userGroupName;
    }

    public function isChangedPass(): int
    {
        return (int) $this->isChangedPass;
    }

    /**
     * @param int $isChangedPass
     */
    public function setIsChangedPass($isChangedPass): void
    {
        $this->isChangedPass = (int) $isChangedPass;
    }

    /**
     * @return string
     */
    public function getSsoLogin()
    {
        return $this->ssoLogin;
    }

    /**
     * @param string $ssoLogin
     */
    public function setSsoLogin($ssoLogin): void
    {
        $this->ssoLogin = $ssoLogin;
    }
}
