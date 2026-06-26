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

namespace SP\DataModel\Dto;

/**
 * Class AccountHistoryCreateDto
 *
 * @package SP\DataModel\Dto
 */
class AccountHistoryCreateDto
{
    private readonly int $accountId;

    private readonly bool $isModify;

    private readonly bool $isDelete;

    private readonly string $masterPassHash;

    /**
     * AccountHistoryCreateDto constructor.
     */
    public function __construct(int $accountId, bool $isModify, bool $isDelete, string $masterPassHash)
    {
        $this->accountId = $accountId;
        $this->isModify = $isModify;
        $this->isDelete = $isDelete;
        $this->masterPassHash = $masterPassHash;
    }

    public function getAccountId(): int
    {
        return $this->accountId;
    }

    public function isModify(): bool
    {
        return $this->isModify;
    }

    public function isDelete(): bool
    {
        return $this->isDelete;
    }

    public function getMasterPassHash(): string
    {
        return $this->masterPassHash;
    }
}
