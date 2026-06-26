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

use SP\Core\Exceptions\InvalidArgumentException;
use SP\Http\Address;

/**
 * Class TrackData
 *
 * @package SP\DataModel
 */
class TrackData extends DataModelBase
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var string
     */
    public $source;

    /**
     * @var int
     */
    public $time = 0;

    /**
     * @var int
     */
    public $timeUnlock = 0;

    /**
     * @var string
     */
    public $ipv4;

    /**
     * @var string
     */
    public $ipv6;

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
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource($source): void
    {
        $this->source = $source;
    }

    public function getTime(): int
    {
        return (int) $this->time;
    }

    /**
     * @param int $time
     */
    public function setTime($time): void
    {
        $this->time = (int) $time;
    }

    /**
     * @return string|null
     * @throws InvalidArgumentException
     */
    public function getIpv4()
    {
        if (!empty($this->ipv4)) {
            return Address::fromBinary($this->ipv4);
        }

        return null;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setIpv4(string $ipv4): void
    {
        $this->ipv4 = Address::toBinary($ipv4);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setTrackIp(string $track_ip): void
    {
        $ip = Address::toBinary($track_ip);

        if (strlen((string) $ip) === 4) {
            $this->ipv4 = $ip;
        } elseif (strlen((string) $ip) > 4) {
            $this->ipv6 = $ip;
        }
    }

    /**
     * @return string
     */
    public function getTrackIpv4Bin()
    {
        return $this->ipv4;
    }

    /**
     * @return string|null
     * @throws InvalidArgumentException
     */
    public function getIpv6()
    {
        if (!empty($this->ipv6)) {
            return Address::fromBinary($this->ipv6);
        }

        return null;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setIpv6(string $ipv6): void
    {
        $this->ipv6 = Address::toBinary($ipv6);
    }

    /**
     * @return string
     */
    public function getTrackIpv6Bin()
    {
        return $this->ipv6;
    }

    public function getTimeUnlock(): int
    {
        return $this->timeUnlock;
    }

    public function setTimeUnlock(int $timeUnlock): void
    {
        $this->timeUnlock = $timeUnlock;
    }
}
