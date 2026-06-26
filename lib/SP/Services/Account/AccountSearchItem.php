<?php

declare(strict_types=1);
/*
 * sysPass
 *
 * @author nuxsmin
 * @link https://syspass.org
 * @copyright 2012-2022, Rubén Domínguez nuxsmin@$syspass.org
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
 * along with sysPass.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SP\Services\Account;

defined('APP_ROOT') || die();

use SP\Bootstrap;
use SP\Config\ConfigData;
use SP\DataModel\AccountSearchVData;
use SP\DataModel\ItemData;
use SP\Html\Html;
use SP\Services\PublicLink\PublicLinkService;

/**
 * Class AccountSearchItem para contener los datos de cada cuenta en la búsqueda
 *
 * @package SP\Controller
 */
final class AccountSearchItem
{
    /**
     * @var bool
     */
    public static $accountLink = false;

    /**
     * @var bool
     */
    public static $topNavbar = false;

    /**
     * @var bool
     */
    public static $optionalActions = false;

    /**
     * @var bool
     */
    public static $showTags = false;

    /**
     * @var bool
     */
    public static $requestEnabled = true;

    /**
     * @var bool
     */
    public static $wikiEnabled = false;

    /**
     * @var bool
     */
    public static $dokuWikiEnabled = false;

    /**
     * @var bool
     */
    public static $publicLinkEnabled = false;

    /**
     * @var bool
     */
    public static $isDemoMode = false;

    /**
     * @var AccountSearchVData
     */
    protected $accountSearchVData;

    /**
     * @var string
     */
    protected $color;

    /**
     * @var string
     */
    protected $link;

    /**
     * @var bool
     */
    protected $url_islink = false;

    /**
     * @var  string
     */
    protected $numFiles;

    /**
     * @var bool
     */
    protected $favorite = false;

    /**
     * @var int
     */
    protected $textMaxLength = 60;

    /**
     * @var ItemData[]
     */
    protected $users;

    /**
     * @var ItemData[]
     */
    protected $tags;

    /**
     * @var ItemData[]
     */
    protected $userGroups;

    private readonly \SP\Config\ConfigData $configData;

    private readonly \SP\Services\Account\AccountAcl $accountAcl;

    /**
     * AccountsSearchItem constructor.
     */
    public function __construct(AccountSearchVData $accountSearchVData, AccountAcl $accountAcl, ConfigData $configData)
    {
        $this->accountSearchVData = $accountSearchVData;
        $this->accountAcl = $accountAcl;
        $this->configData = $configData;
    }

    /**
     * @return bool
     */
    public function isFavorite()
    {
        return $this->favorite;
    }

    /**
     * @param bool $favorite
     */
    public function setFavorite($favorite): void
    {
        $this->favorite = $favorite;
    }

    public function isShowRequest(): bool
    {
        return (!$this->accountAcl->isShow() && self::$requestEnabled);
    }

    public function isShowCopyPass(): bool
    {
        return ($this->accountAcl->isShowViewPass() && !$this->configData->isAccountPassToImage());
    }

    public function isShowViewPass(): bool
    {
        return $this->accountAcl->isShowViewPass();
    }

    public function isShowOptional(): bool
    {
        return ($this->accountAcl->isShow() && !self::$optionalActions);
    }

    /**
     * @param int $textMaxLength
     */
    public function setTextMaxLength($textMaxLength): void
    {
        $this->textMaxLength = $textMaxLength;
    }

    /**
     * @return string
     */
    public function getShortUrl()
    {
        return Html::truncate($this->getSafeUrl(), $this->textMaxLength);
    }

    public function isUrlIslink(): int|false
    {
        $url = $this->accountSearchVData->getUrl();
        // Match URLs with protocol (http://, https://, etc.) or starting with www.
        return preg_match('#^(\w+://|www\.)#i', $url);
    }

    public function getSafeUrl(): string
    {
        $url = $this->accountSearchVData->getUrl();
        // Add https:// if URL starts with www. but has no protocol
        if (preg_match('#^www\.#i', $url) && !preg_match('#^\w+://#', $url)) {
            $url = 'https://' . $url;
        }
        return Html::getSafeUrl($url);
    }

    /**
     * @return string
     */
    public function getShortLogin()
    {
        return Html::truncate($this->accountSearchVData->getLogin(), $this->textMaxLength);
    }

    /**
     * @return string
     */
    public function getShortClientName()
    {
        return Html::truncate($this->accountSearchVData->getClientName(), $this->textMaxLength / 3);
    }

    /**
     * @return string
     */
    public function getClientLink(): ?string
    {
        return self::$wikiEnabled ? $this->configData->getWikiSearchurl() . $this->accountSearchVData->getClientName() : null;
    }

    /**
     * @return string
     */
    public function getPublicLink(): ?string
    {
        if (self::$publicLinkEnabled
            && $this->accountSearchVData->getPublicLinkHash() !== null
        ) {
            $baseUrl = ($this->configData->getApplicationUrl() ?: Bootstrap::$WEBURI) . Bootstrap::$SUBURI;

            return PublicLinkService::getLinkForHash($baseUrl, $this->accountSearchVData->getPublicLinkHash());
        }

        return null;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor($color): void
    {
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink($link): void
    {
        $this->link = $link;
    }

    public function getAccesses(): array
    {
        $accesses = [
            '(G*) <em>' . $this->accountSearchVData->getUserGroupName() . '</em>',
            '(U*) <em>' . $this->accountSearchVData->getUserLogin() . '</em>',
        ];

        $userLabel = $this->accountSearchVData->getOtherUserEdit() === 1 ? 'U+' : 'U';
        $userGroupLabel = $this->accountSearchVData->getOtherUserGroupEdit() === 1 ? 'G+' : 'G';

        foreach ($this->userGroups as $group) {
            $accesses[] = sprintf('(%s) <em>%s</em>', $userGroupLabel, $group->getName());
        }

        foreach ($this->users as $user) {
            $accesses[] = sprintf('(%s) <em>%s</em>', $userLabel, $user->login);
        }

        return $accesses;
    }

    /**
     * @return string
     */
    public function getNumFiles()
    {
        return $this->configData->isFilesEnabled() ? $this->accountSearchVData->getNumFiles() : 0;
    }

    /**
     * @param string $numFiles
     */
    public function setNumFiles($numFiles): void
    {
        $this->numFiles = $numFiles;
    }

    public function isShow(): bool
    {
        return $this->accountAcl->isShow();
    }

    public function isShowView(): bool
    {
        return $this->accountAcl->isShowView();
    }

    public function isShowEdit(): bool
    {
        return $this->accountAcl->isShowEdit();
    }

    public function isShowCopy(): bool
    {
        return $this->accountAcl->isShowCopy();
    }

    public function isShowDelete(): bool
    {
        return $this->accountAcl->isShowDelete();
    }

    /**
     * @return AccountSearchVData
     */
    public function getAccountSearchVData()
    {
        return $this->accountSearchVData;
    }

    public function getShortNotes(): string
    {
        if ($this->accountSearchVData->getNotes()) {
            return nl2br(htmlspecialchars(Html::truncate($this->accountSearchVData->getNotes(), 300), ENT_QUOTES));
        }

        return '';
    }

    /**
     * Develve si la clave ha caducado
     */
    public function isPasswordExpired(): bool
    {
        return $this->configData->isAccountExpireEnabled()
            && $this->accountSearchVData->getPassDateChange() > 0
            && time() > $this->accountSearchVData->getPassDateChange();
    }

    /**
     * @param ItemData[] $userGroups
     */
    public function setUserGroups(array $userGroups): void
    {
        $this->userGroups = $userGroups;
    }

    /**
     * @param ItemData[] $users
     */
    public function setUsers(array $users): void
    {
        $this->users = $users;
    }

    /**
     * @return ItemData[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param ItemData[] $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @param $wikiFilter
     */
    public function isWikiMatch(string $wikiFilter): bool
    {
        return preg_match('/^' . $wikiFilter . '/i', $this->accountSearchVData->getName()) === 1;
    }
}
