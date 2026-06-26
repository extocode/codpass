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

use SP\Mvc\Model\QueryCondition;

/**
 * Class AccountSearchFilter
 *
 * @package SP\Account
 */
final class AccountSearchFilter
{
    /**
     * Constantes de ordenación
     */
    public const SORT_DIR_ASC = 0;

    public const SORT_DIR_DESC = 1;

    public const SORT_LOGIN = 3;

    public const SORT_URL = 4;

    public const SORT_CATEGORY = 2;

    public const SORT_CLIENT = 5;

    public const SORT_NAME = 1;

    public const SORT_DEFAULT = 0;

    /**
     * @var int El número de registros de la última consulta
     */
    public static $queryNumRows;

    /**
     * @var bool
     */
    private $globalSearch = false;

    /**
     * @var string
     */
    private $txtSearch;

    /**
     * @var string Search string without special filters
     */
    private $cleanTxtSearch;

    /**
     * @var int
     */
    private $clientId;

    /**
     * @var int
     */
    private $categoryId;

    private ?array $tagsId = null;

    /**
     * @var int
     */
    private $sortOrder = self::SORT_DEFAULT;

    /**
     * @var int
     */
    private $sortKey = self::SORT_DIR_ASC;

    /**
     * @var int
     */
    private $limitStart = 0;

    /**
     * @var int
     */
    private $limitCount;

    /**
     * @var bool
     */
    private $sortViews;

    private bool $searchFavorites = false;

    private ?\SP\Mvc\Model\QueryCondition $stringFilters = null;

    /**
     * @var string
     */
    private $filterOperator;

    /**
     * @return bool
     */
    public function isSearchFavorites()
    {
        return $this->searchFavorites;
    }

    /**
     * @param bool $searchFavorites
     *
     * @return $this
     */
    public function setSearchFavorites($searchFavorites): self
    {
        $this->searchFavorites = (bool) $searchFavorites;

        return $this;
    }

    /**
     * @return int
     */
    public function getGlobalSearch()
    {
        return $this->globalSearch;
    }

    /**
     * @param int $globalSearch
     *
     * @return $this
     */
    public function setGlobalSearch($globalSearch): self
    {
        $this->globalSearch = $globalSearch;

        return $this;
    }

    /**
     * @return string
     */
    public function getTxtSearch()
    {
        return $this->txtSearch;
    }

    /**
     * @param string $txtSearch
     *
     * @return $this
     */
    public function setTxtSearch($txtSearch): self
    {
        $this->txtSearch = $txtSearch;

        return $this;
    }

    /**
     * @return int
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param int $clientId
     *
     * @return $this
     */
    public function setClientId($clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @param int $categoryId
     *
     * @return $this
     */
    public function setCategoryId($categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @param int $sortOrder
     *
     * @return $this
     */
    public function setSortOrder($sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimitStart()
    {
        return $this->limitStart;
    }

    /**
     * @param int $limitStart
     *
     * @return $this
     */
    public function setLimitStart($limitStart): self
    {
        $this->limitStart = $limitStart;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimitCount()
    {
        return $this->limitCount;
    }

    /**
     * @param int $limitCount
     *
     * @return $this
     */
    public function setLimitCount($limitCount): self
    {
        $this->limitCount = $limitCount;

        return $this;
    }

    /**
     * @return array
     */
    public function getTagsId()
    {
        return $this->tagsId ?: [];
    }

    /**
     * @param array $tagsId
     *
     * @return $this
     */
    public function setTagsId($tagsId): self
    {
        if (is_array($tagsId)) {
            $this->tagsId = $tagsId;
        }

        return $this;
    }

    public function hasTags(): bool
    {
        return !empty($this->tagsId);
    }

    /**
     * @return QueryCondition
     */
    public function getStringFilters()
    {
        return $this->stringFilters ?: new QueryCondition();
    }

    public function setStringFilters(QueryCondition $stringFilters): void
    {
        $this->stringFilters = $stringFilters;
    }

    /**
     * Devuelve la cadena de ordenación de la consulta
     */
    public function getOrderString(): string
    {
        $orderKey[] = match ($this->sortKey) {
            self::SORT_NAME => 'Account.name',
            self::SORT_CATEGORY => 'Account.categoryName',
            self::SORT_LOGIN => 'Account.login',
            self::SORT_URL => 'Account.url',
            self::SORT_CLIENT => 'Account.clientName',
            default => 'Account.clientName, Account.name',
        };

        if ($this->isSortViews() && !$this->getSortKey()) {
            array_unshift($orderKey, 'Account.countView DESC');
            $this->setSortOrder(self::SORT_DIR_DESC);
        }

        $orderDir = ($this->sortOrder === self::SORT_DIR_ASC) ? 'ASC' : 'DESC';
        return sprintf('%s %s', implode(',', $orderKey), $orderDir);
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
     *
     * @return $this
     */
    public function setSortViews($sortViews): self
    {
        $this->sortViews = $sortViews;

        return $this;
    }

    /**
     * @return int
     */
    public function getSortKey()
    {
        return $this->sortKey;
    }

    /**
     * @param int $sortKey
     *
     * @return $this
     */
    public function setSortKey($sortKey): self
    {
        $this->sortKey = $sortKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getCleanTxtSearch()
    {
        return $this->cleanTxtSearch;
    }

    /**
     * @param string $cleanTxtSearch
     */
    public function setCleanTxtSearch($cleanTxtSearch): void
    {
        $this->cleanTxtSearch = $cleanTxtSearch;
    }

    /**
     * @return string
     */
    public function getFilterOperator()
    {
        return $this->filterOperator ?: QueryCondition::CONDITION_AND;
    }

    /**
     * @param string $filterOperator
     */
    public function setFilterOperator($filterOperator): void
    {
        $this->filterOperator = $filterOperator;
    }

    /**
     * Resets internal variables
     */
    public function reset(): void
    {
        self::$queryNumRows = null;
        $this->categoryId = null;
        $this->clientId = null;
        $this->filterOperator = null;
        $this->globalSearch = false;
        $this->txtSearch = null;
        $this->cleanTxtSearch = null;
        $this->tagsId = null;
        $this->limitCount = null;
        $this->sortViews = null;
        $this->searchFavorites = false;
        $this->sortOrder = self::SORT_DEFAULT;
        $this->sortKey = self::SORT_DIR_ASC;
    }
}
