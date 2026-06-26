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
 * Class ProfileData
 *
 * @package SP\DataModel
 */
class ProfileData
{
    /**
     * @var bool
     */
    protected $accView = false;

    /**
     * @var bool
     */
    protected $accViewPass = false;

    /**
     * @var bool
     */
    protected $accViewHistory = false;

    /**
     * @var bool
     */
    protected $accEdit = false;

    /**
     * @var bool
     */
    protected $accEditPass = false;

    /**
     * @var bool
     */
    protected $accAdd = false;

    /**
     * @var bool
     */
    protected $accDelete = false;

    /**
     * @var bool
     */
    protected $accFiles = false;

    /**
     * @var bool
     */
    protected $accPrivate = false;

    /**
     * @var bool
     */
    protected $accPrivateGroup = false;

    /**
     * @var bool
     */
    protected $accPermission = false;

    /**
     * @var bool
     */
    protected $accPublicLinks = false;

    /**
     * @var bool
     */
    protected $accGlobalSearch = false;

    /**
     * @var bool
     */
    protected $configGeneral = false;

    /**
     * @var bool
     */
    protected $configEncryption = false;

    /**
     * @var bool
     */
    protected $configBackup = false;

    /**
     * @var bool
     */
    protected $configImport = false;

    /**
     * @var bool
     */
    protected $mgmUsers = false;

    /**
     * @var bool
     */
    protected $mgmGroups = false;

    /**
     * @var bool
     */
    protected $mgmProfiles = false;

    /**
     * @var bool
     */
    protected $mgmCategories = false;

    /**
     * @var bool
     */
    protected $mgmCustomers = false;

    /**
     * @var bool
     */
    protected $mgmApiTokens = false;

    /**
     * @var bool
     */
    protected $mgmPublicLinks = false;

    /**
     * @var bool
     */
    protected $mgmAccounts = false;

    /**
     * @var bool
     */
    protected $mgmTags = false;

    /**
     * @var bool
     */
    protected $mgmFiles = false;

    /**
     * @var bool
     */
    protected $mgmItemsPreset = false;

    /**
     * @var bool
     */
    protected $evl = false;

    /**
     * @var bool
     */
    protected $mgmCustomFields = false;

    /**
     * @return bool
     */
    public function isAccView()
    {
        return $this->accView;
    }

    /**
     * @param bool $accView
     */
    public function setAccView($accView): static
    {
        $this->accView = $accView;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAccViewPass()
    {
        return $this->accViewPass;
    }

    /**
     * @param bool $accViewPass
     */
    public function setAccViewPass($accViewPass): static
    {
        $this->accViewPass = $accViewPass;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAccViewHistory()
    {
        return $this->accViewHistory;
    }

    /**
     * @param bool $accViewHistory
     */
    public function setAccViewHistory($accViewHistory): static
    {
        $this->accViewHistory = $accViewHistory;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAccEdit()
    {
        return $this->accEdit;
    }

    /**
     * @param bool $accEdit
     */
    public function setAccEdit($accEdit): static
    {
        $this->accEdit = $accEdit;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAccEditPass()
    {
        return $this->accEditPass;
    }

    /**
     * @param bool $accEditPass
     */
    public function setAccEditPass($accEditPass): static
    {
        $this->accEditPass = $accEditPass;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAccAdd()
    {
        return $this->accAdd;
    }

    /**
     * @param bool $accAdd
     */
    public function setAccAdd($accAdd): static
    {
        $this->accAdd = $accAdd;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAccDelete()
    {
        return $this->accDelete;
    }

    /**
     * @param bool $accDelete
     */
    public function setAccDelete($accDelete): static
    {
        $this->accDelete = $accDelete;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAccFiles()
    {
        return $this->accFiles;
    }

    /**
     * @param bool $accFiles
     */
    public function setAccFiles($accFiles): static
    {
        $this->accFiles = $accFiles;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAccPublicLinks()
    {
        return $this->accPublicLinks;
    }

    /**
     * @param bool $accPublicLinks
     */
    public function setAccPublicLinks($accPublicLinks): static
    {
        $this->accPublicLinks = $accPublicLinks;

        return $this;
    }

    /**
     * @return bool
     */
    public function isConfigGeneral()
    {
        return $this->configGeneral;
    }

    /**
     * @param bool $configGeneral
     */
    public function setConfigGeneral($configGeneral): static
    {
        $this->configGeneral = $configGeneral;

        return $this;
    }

    /**
     * @return bool
     */
    public function isConfigEncryption()
    {
        return $this->configEncryption;
    }

    /**
     * @param bool $configEncryption
     */
    public function setConfigEncryption($configEncryption): static
    {
        $this->configEncryption = $configEncryption;

        return $this;
    }

    /**
     * @return bool
     */
    public function isConfigBackup()
    {
        return $this->configBackup;
    }

    /**
     * @param bool $configBackup
     */
    public function setConfigBackup($configBackup): static
    {
        $this->configBackup = $configBackup;

        return $this;
    }

    /**
     * @return bool
     */
    public function isConfigImport()
    {
        return $this->configImport;
    }

    /**
     * @param bool $configImport
     */
    public function setConfigImport($configImport): static
    {
        $this->configImport = $configImport;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMgmUsers()
    {
        return $this->mgmUsers;
    }

    /**
     * @param bool $mgmUsers
     */
    public function setMgmUsers($mgmUsers): static
    {
        $this->mgmUsers = $mgmUsers;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMgmGroups()
    {
        return $this->mgmGroups;
    }

    /**
     * @param bool $mgmGroups
     */
    public function setMgmGroups($mgmGroups): static
    {
        $this->mgmGroups = $mgmGroups;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMgmProfiles()
    {
        return $this->mgmProfiles;
    }

    /**
     * @param bool $mgmProfiles
     */
    public function setMgmProfiles($mgmProfiles): static
    {
        $this->mgmProfiles = $mgmProfiles;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMgmCategories()
    {
        return $this->mgmCategories;
    }

    /**
     * @param bool $mgmCategories
     */
    public function setMgmCategories($mgmCategories): static
    {
        $this->mgmCategories = $mgmCategories;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMgmCustomers()
    {
        return $this->mgmCustomers;
    }

    /**
     * @param bool $mgmCustomers
     */
    public function setMgmCustomers($mgmCustomers): static
    {
        $this->mgmCustomers = $mgmCustomers;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMgmApiTokens()
    {
        return $this->mgmApiTokens;
    }

    /**
     * @param bool $mgmApiTokens
     */
    public function setMgmApiTokens($mgmApiTokens): static
    {
        $this->mgmApiTokens = $mgmApiTokens;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMgmPublicLinks()
    {
        return $this->mgmPublicLinks;
    }

    /**
     * @param bool $mgmPublicLinks
     */
    public function setMgmPublicLinks($mgmPublicLinks): static
    {
        $this->mgmPublicLinks = $mgmPublicLinks;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEvl()
    {
        return $this->evl;
    }

    /**
     * @param bool $evl
     */
    public function setEvl($evl): static
    {
        $this->evl = $evl;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMgmCustomFields()
    {
        return $this->mgmCustomFields;
    }

    /**
     * @param bool $mgmCustomFields
     */
    public function setMgmCustomFields($mgmCustomFields): static
    {
        $this->mgmCustomFields = $mgmCustomFields;

        return $this;
    }

    /**
     * unserialize() checks for the presence of a function with the magic name __wakeup.
     * If present, this function can reconstruct any resources that the object may have.
     * The intended use of __wakeup is to reestablish any database connections that may have been lost during
     * serialization and perform other reinitialization tasks.
     *
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.sleep
     */
    public function __wakeup()
    {
        // Para realizar la conversión de nombre de propiedades que empiezan por _
        foreach (get_object_vars($this) as $name => $value) {
            if ($name[0] === '_') {
                $newName = substr($name, 1);
                $this->$newName = $value;
            }
        }
    }

    /**
     * @return bool
     */
    public function isAccPrivate()
    {
        return $this->accPrivate;
    }

    /**
     * @param bool $accPrivate
     */
    public function setAccPrivate($accPrivate): static
    {
        $this->accPrivate = $accPrivate;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAccPermission()
    {
        return $this->accPermission;
    }

    /**
     * @param bool $accPermission
     */
    public function setAccPermission($accPermission): static
    {
        $this->accPermission = $accPermission;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMgmAccounts()
    {
        return $this->mgmAccounts;
    }

    /**
     * @param bool $mgmAccounts
     */
    public function setMgmAccounts($mgmAccounts): static
    {
        $this->mgmAccounts = $mgmAccounts;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMgmTags()
    {
        return $this->mgmTags;
    }

    /**
     * @param bool $mgmTags
     */
    public function setMgmTags($mgmTags): static
    {
        $this->mgmTags = $mgmTags;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMgmFiles()
    {
        return $this->mgmFiles;
    }

    /**
     * @param bool $mgmFiles
     */
    public function setMgmFiles($mgmFiles): static
    {
        $this->mgmFiles = $mgmFiles;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAccGlobalSearch()
    {
        return $this->accGlobalSearch;
    }

    /**
     * @param bool $accGlobalSearch
     */
    public function setAccGlobalSearch($accGlobalSearch): static
    {
        $this->accGlobalSearch = $accGlobalSearch;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAccPrivateGroup()
    {
        return $this->accPrivateGroup;
    }

    /**
     * @param bool $accPrivateGroup
     */
    public function setAccPrivateGroup($accPrivateGroup): static
    {
        $this->accPrivateGroup = $accPrivateGroup;

        return $this;
    }

    /**
     * @return $this
     */
    public function reset(): static
    {
        foreach ($this as $property => $value) {
            $this->{$property} = false;
        }

        return $this;
    }

    public function isMgmItemsPreset(): bool
    {
        return $this->mgmItemsPreset;
    }

    public function setMgmItemsPreset(bool $mgmItemsPreset): static
    {
        $this->mgmItemsPreset = $mgmItemsPreset;

        return $this;
    }
}
