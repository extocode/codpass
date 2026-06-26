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
 * Class AccountHistoryData
 *
 * @package SP\DataModel
 */
class AccountHistoryData extends AccountExtData
{
    /**
     * @var bool
     */
    public $isModify = 0;

    /**
     * @var bool
     */
    public $isDeleted = 0;

    /**
     * @var int
     */
    public $accountId;

    public function isIsModify(): int
    {
        return (int) $this->isModify;
    }

    /**
     * @param bool $isModify
     */
    public function setIsModify($isModify): void
    {
        $this->isModify = (int) $isModify;
    }

    public function isIsDeleted(): int
    {
        return (int) $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     */
    public function setIsDeleted($isDeleted): void
    {
        $this->isDeleted = (int) $isDeleted;
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param int $accountId
     */
    public function setAccountId($accountId): void
    {
        $this->accountId = $accountId;
    }
}
