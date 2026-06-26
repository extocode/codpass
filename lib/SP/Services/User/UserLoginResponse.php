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

namespace SP\Services\User;

use SP\DataModel\UserPreferencesData;

/**
 * Class UserLoginResponse
 *
 * @package SP\Services\User
 */
final class UserLoginResponse
{
    private ?int $id = null;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $ssoLogin;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $email;

    private int $userGroupId = 0;

    /**
     * @var string
     */
    private $userGroupName;

    private int $userProfileId = 0;

    /**
     * @var string
     */
    private $userProfileName;

    private int $isAdminApp = 0;

    private int $isAdminAcc = 0;

    private int $isDisabled = 0;

    private int $isChangePass = 0;

    private int $isChangedPass = 0;

    private int $isLdap = 0;

    private int $isMigrate = 0;

    private ?\SP\DataModel\UserPreferencesData $preferences = null;

    /**
     * @var string
     */
    private $pass;

    /**
     * @var string
     */
    private $hashSalt;

    /**
     * @var string
     */
    private $mPass;

    /**
     * @var string
     */
    private $mKey;

    private int $lastUpdateMPass = 0;

    private ?int $lastUpdate = null;

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
    public function setLogin($login): self
    {
        $this->login = $login;
        return $this;
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
    public function setSsoLogin($ssoLogin): self
    {
        $this->ssoLogin = $ssoLogin;
        return $this;
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
    public function setName($name): self
    {
        $this->name = $name;
        return $this;
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
    public function setEmail($email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserGroupId()
    {
        return $this->userGroupId;
    }

    /**
     * @param int $userGroupId
     */
    public function setUserGroupId($userGroupId): self
    {
        $this->userGroupId = (int) $userGroupId;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserProfileId()
    {
        return $this->userProfileId;
    }

    /**
     * @param int $userProfileId
     */
    public function setUserProfileId($userProfileId): self
    {
        $this->userProfileId = (int) $userProfileId;
        return $this;
    }

    /**
     * @return int
     */
    public function getIsAdminApp()
    {
        return $this->isAdminApp;
    }

    /**
     * @param int $isAdminApp
     */
    public function setIsAdminApp($isAdminApp): self
    {
        $this->isAdminApp = (int) $isAdminApp;
        return $this;
    }

    /**
     * @return int
     */
    public function getIsAdminAcc()
    {
        return $this->isAdminAcc;
    }

    /**
     * @param int $isAdminAcc
     */
    public function setIsAdminAcc($isAdminAcc): self
    {
        $this->isAdminAcc = (int) $isAdminAcc;
        return $this;
    }

    /**
     * @return int
     */
    public function getIsDisabled()
    {
        return $this->isDisabled;
    }

    /**
     * @param int $isDisabled
     */
    public function setIsDisabled($isDisabled): self
    {
        $this->isDisabled = (int) $isDisabled;
        return $this;
    }

    /**
     * @return int
     */
    public function getIsChangePass()
    {
        return $this->isChangePass;
    }

    /**
     * @param int $isChangePass
     */
    public function setIsChangePass($isChangePass): self
    {
        $this->isChangePass = (int) $isChangePass;
        return $this;
    }

    /**
     * @return int
     */
    public function getIsChangedPass()
    {
        return $this->isChangedPass;
    }

    /**
     * @param int $isChangedPass
     */
    public function setIsChangedPass($isChangedPass): self
    {
        $this->isChangedPass = (int) $isChangedPass;
        return $this;
    }

    /**
     * @return int
     */
    public function getIsLdap()
    {
        return $this->isLdap;
    }

    /**
     * @param int $isLdap
     */
    public function setIsLdap($isLdap): self
    {
        $this->isLdap = (int) $isLdap;
        return $this;
    }

    /**
     * @return int
     */
    public function getIsMigrate()
    {
        return $this->isMigrate;
    }

    /**
     * @param int $isMigrate
     */
    public function setIsMigrate($isMigrate): self
    {
        $this->isMigrate = (int) $isMigrate;
        return $this;
    }

    /**
     * @return UserPreferencesData
     */
    public function getPreferences()
    {
        return $this->preferences;
    }

    /**
     * @param mixed $preferences
     */
    public function setPreferences(UserPreferencesData $preferences): self
    {
        $this->preferences = $preferences;
        return $this;
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
    public function setPass($pass): self
    {
        $this->pass = $pass;
        return $this;
    }

    /**
     * @return string
     */
    public function getMPass()
    {
        return $this->mPass;
    }

    /**
     * @param string $mPass
     */
    public function setMPass($mPass): self
    {
        $this->mPass = $mPass;
        return $this;
    }

    /**
     * @return string
     */
    public function getMKey()
    {
        return $this->mKey;
    }

    /**
     * @param string $mKey
     */
    public function setMKey($mKey): self
    {
        $this->mKey = $mKey;
        return $this;
    }

    /**
     * @return int
     */
    public function getLastUpdateMPass()
    {
        return $this->lastUpdateMPass;
    }

    /**
     * @param int $lastUpdateMPass
     */
    public function setLastUpdateMPass($lastUpdateMPass): self
    {
        $this->lastUpdateMPass = (int) $lastUpdateMPass;
        return $this;
    }

    /**
     * @return string
     */
    public function getHashSalt()
    {
        return $this->hashSalt;
    }

    /**
     * @param string $hashSalt
     */
    public function setHashSalt($hashSalt): self
    {
        $this->hashSalt = $hashSalt;
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): self
    {
        $this->id = (int) $id;
        return $this;
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
    public function setUserGroupName($userGroupName): self
    {
        $this->userGroupName = $userGroupName;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserProfileName()
    {
        return $this->userProfileName;
    }

    /**
     * @param string $userProfileName
     */
    public function setUserProfileName($userProfileName): self
    {
        $this->userProfileName = $userProfileName;
        return $this;
    }

    public function getLastUpdate(): int
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(int $lastUpdate): self
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }
}
