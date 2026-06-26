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
 * Class PublicLinkData
 *
 * @package SP\DataModel
 */
class PublicLinkData extends DataModelBase implements DataModelInterface
{
    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $itemId = 0;

    /**
     * @var string
     */
    public $hash = '';

    /**
     * @var int
     */
    public $userId = 0;

    /**
     * @var int
     */
    public $typeId = 0;

    /**
     * @var bool
     */
    public $notify = false;

    /**
     * @var int
     */
    public $dateAdd = 0;

    /**
     * @var int
     */
    public $dateUpdate = 0;

    /**
     * @var int
     */
    public $dateExpire = 0;

    /**
     * @var int
     */
    public $countViews = 0;

    /**
     * @var int
     */
    public $totalCountViews = 0;

    /**
     * @var int
     */
    public $maxCountViews = 0;

    /**
     * @var string
     */
    public $useInfo;

    /**
     * @var string
     */
    public $data;

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData($data): void
    {
        $this->data = $data;
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

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash): void
    {
        $this->hash = $hash;
    }

    public function getItemId(): int
    {
        return (int) $this->itemId;
    }

    /**
     * @param int $itemId
     */
    public function setItemId($itemId): void
    {
        $this->itemId = (int) $itemId;
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

    public function getTypeId(): int
    {
        return (int) $this->typeId;
    }

    /**
     * @param int $typeId
     */
    public function setTypeId($typeId): void
    {
        $this->typeId = (int) $typeId;
    }

    public function isNotify(): bool
    {
        return (bool) $this->notify;
    }

    /**
     * @param bool $notify
     */
    public function setNotify($notify): void
    {
        $this->notify = (bool) $notify;
    }

    public function getDateAdd(): int
    {
        return (int) $this->dateAdd;
    }

    /**
     * @param int $dateAdd
     */
    public function setDateAdd($dateAdd): void
    {
        $this->dateAdd = (int) $dateAdd;
    }

    public function getDateExpire(): int
    {
        return (int) $this->dateExpire;
    }

    /**
     * @param int $dateExpire
     */
    public function setDateExpire($dateExpire): void
    {
        $this->dateExpire = (int) $dateExpire;
    }

    public function getCountViews(): int
    {
        return (int) $this->countViews;
    }

    /**
     * @param int $countViews
     */
    public function setCountViews($countViews): void
    {
        $this->countViews = (int) $countViews;
    }

    /**
     * @return int
     */
    public function addCountViews()
    {
        return $this->countViews++;
    }

    public function getMaxCountViews(): int
    {
        return (int) $this->maxCountViews;
    }

    /**
     * @param int $maxCountViews
     */
    public function setMaxCountViews($maxCountViews): void
    {
        $this->maxCountViews = (int) $maxCountViews;
    }

    /**
     * @return string
     */
    public function getUseInfo()
    {
        return $this->useInfo;
    }

    public function setUseInfo(array $useInfo): void
    {
        $this->useInfo = serialize($useInfo);
    }

    public function getTotalCountViews(): int
    {
        return (int) $this->totalCountViews;
    }

    /**
     * @return int
     */
    public function addTotalCountViews()
    {
        return $this->totalCountViews++;
    }

    public function getName(): string
    {
        return '';
    }

    public function getDateUpdate(): int
    {
        return (int) $this->dateUpdate;
    }

    public function setDateUpdate(int $dateUpdate): void
    {
        $this->dateUpdate = $dateUpdate;
    }
}
