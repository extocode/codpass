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
 * Class UserPassRecoverData
 *
 * @package SP\DataModel
 */
class UserPassRecoverData extends DataModelBase
{
    /**
     * @var int
     */
    public $userId = 0;

    /**
     * @var string
     */
    public $hash = '';

    /**
     * @var int
     */
    public $date = 0;

    /**
     * @var bool
     */
    public $used = 0;

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

    /**
     * @return int
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param int $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }

    public function isUsed(): int
    {
        return (int) $this->used;
    }

    /**
     * @param bool $used
     */
    public function setUsed($used): void
    {
        $this->used = (int) $used;
    }
}
