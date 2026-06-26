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

/**
 * Class UserPreferencesData
 *
 * @package SP\DataModel
 */
class UserPreferencesData
{
    /**
     * @var int
     */
    public $user_id = 0;

    /**
     * Lenguaje del usuario
     *
     * @var string
     */
    public $lang = '';

    /**
     * Tema del usuario
     *
     * @var string
     */
    public $theme = '';

    /**
     * @var int
     */
    public $resultsPerPage = 0;

    /**
     * @var bool
     */
    public $accountLink;

    /**
     * @var bool
     */
    public $sortViews = false;

    /**
     * @var bool
     */
    public $topNavbar = false;

    /**
     * @var bool
     */
    public $optionalActions = false;

    /**
     * @var bool
     */
    public $resultsAsCards = false;

    /**
     * @var bool
     */
    public $checkNotifications = true;

    /**
     * @var bool
     */
    public $showAccountSearchFilters = false;

    /**
     * @var bool
     */
    public $stickySearchBar = false;

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param string $lang
     */
    public function setLang($lang): void
    {
        $this->lang = $lang;
    }

    /**
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param string $theme
     */
    public function setTheme($theme): void
    {
        $this->theme = $theme;
    }

    /**
     * @return int
     */
    public function getResultsPerPage()
    {
        return $this->resultsPerPage;
    }

    /**
     * @param int $resultsPerPage
     */
    public function setResultsPerPage($resultsPerPage): void
    {
        $this->resultsPerPage = $resultsPerPage;
    }

    /**
     * @return bool
     */
    public function isAccountLink()
    {
        return $this->accountLink;
    }

    /**
     * @param bool $accountLink
     */
    public function setAccountLink($accountLink): void
    {
        $this->accountLink = $accountLink;
    }

    /**
     * @return bool
     */
    public function isSortViews()
    {
        return $this->sortViews;
    }

    /**
     * @param bool $sortViews
     */
    public function setSortViews($sortViews): void
    {
        $this->sortViews = $sortViews;
    }

    /**
     * @return bool
     */
    public function isTopNavbar()
    {
        return $this->topNavbar;
    }

    /**
     * @param bool $topNavbar
     */
    public function setTopNavbar($topNavbar): void
    {
        $this->topNavbar = $topNavbar;
    }

    /**
     * @return bool
     */
    public function isOptionalActions()
    {
        return $this->optionalActions;
    }

    /**
     * @param bool $optionalActions
     */
    public function setOptionalActions($optionalActions): void
    {
        $this->optionalActions = $optionalActions;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId($user_id): void
    {
        $this->user_id = $user_id;
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
            if (str_starts_with($name, '_')) {
                $newName = substr($name, 1);
                $this->$newName = $value;

                // Borrar la variable anterior
                unset($this->$name);
            }
        }
    }

    /**
     * @return bool
     */
    public function isResultsAsCards()
    {
        return $this->resultsAsCards;
    }

    /**
     * @param bool $resultsAsCards
     */
    public function setResultsAsCards($resultsAsCards): void
    {
        $this->resultsAsCards = $resultsAsCards;
    }

    public function isCheckNotifications(): bool
    {
        return $this->checkNotifications;
    }

    public function setCheckNotifications(bool $checkNotifications): void
    {
        $this->checkNotifications = $checkNotifications;
    }

    public function isShowAccountSearchFilters(): bool
    {
        return $this->showAccountSearchFilters;
    }

    public function setShowAccountSearchFilters(bool $showAccountSearchFilters): void
    {
        $this->showAccountSearchFilters = $showAccountSearchFilters;
    }

    public function isStickySearchBar(): bool
    {
        return $this->stickySearchBar;
    }

    public function setStickySearchBar(bool $stickySearchBar): void
    {
        $this->stickySearchBar = $stickySearchBar;
    }
}
