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

namespace SP\Storage\Database;

use SP\Config\ConfigData;

/**
 * Class DatabaseConnectionData
 *
 * @package SP\Storage
 */
final class DatabaseConnectionData
{
    /**
     * @var string
     */
    private $dbHost;

    /**
     * @var string
     */
    private $dbSocket;

    /**
     * @var int
     */
    private $dbPort;

    /**
     * @var string
     */
    private $dbName;

    /**
     * @var string
     */
    private $dbUser;

    /**
     * @var string
     */
    private $dbPass;

    /**
     * @return mixed
     */
    public static function getFromConfig(ConfigData $configData)
    {
        return (new self())
            ->setDbHost($configData->getDbHost())
            ->setDbName($configData->getDbName())
            ->setDbUser($configData->getDbUser())
            ->setDbPass($configData->getDbPass())
            ->setDbPort($configData->getDbPort())
            ->setDbSocket($configData->getDbSocket());
    }

    /**
     * @return DatabaseConnectionData
     */
    public function refreshFromConfig(ConfigData $configData)
    {
        logger('Refresh DB connection data');

        return $this->setDbHost($configData->getDbHost())
            ->setDbName($configData->getDbName())
            ->setDbUser($configData->getDbUser())
            ->setDbPass($configData->getDbPass())
            ->setDbPort($configData->getDbPort())
            ->setDbSocket($configData->getDbSocket());
    }

    /**
     * @return string
     */
    public function getDbHost()
    {
        return $this->dbHost;
    }

    /**
     * @param string $dbHost
     */
    public function setDbHost($dbHost): self
    {
        $this->dbHost = $dbHost;
        return $this;
    }

    /**
     * @return string
     */
    public function getDbSocket()
    {
        return $this->dbSocket;
    }

    /**
     * @param string $dbSocket
     */
    public function setDbSocket($dbSocket): self
    {
        $this->dbSocket = $dbSocket;
        return $this;
    }

    /**
     * @return int
     */
    public function getDbPort()
    {
        return $this->dbPort;
    }

    /**
     * @param int $dbPort
     */
    public function setDbPort($dbPort): self
    {
        $this->dbPort = $dbPort;
        return $this;
    }

    /**
     * @return string
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * @param string $dbName
     */
    public function setDbName($dbName): self
    {
        $this->dbName = $dbName;
        return $this;
    }

    /**
     * @return string
     */
    public function getDbUser()
    {
        return $this->dbUser;
    }

    /**
     * @param string $dbUser
     */
    public function setDbUser($dbUser): self
    {
        $this->dbUser = $dbUser;
        return $this;
    }

    /**
     * @return string
     */
    public function getDbPass()
    {
        return $this->dbPass;
    }

    /**
     * @param string $dbPass
     */
    public function setDbPass($dbPass): self
    {
        $this->dbPass = $dbPass;
        return $this;
    }
}
