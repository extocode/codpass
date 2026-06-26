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

use Exception;
use PDO;

defined('APP_ROOT') || die();

/**
 * Class MySQLHandler
 *
 * Esta clase se encarga de crear las conexiones a la BD
 */
final class MySQLHandler implements DBStorageInterface
{
    public const STATUS_OK = 0;

    public const STATUS_KO = 1;

    public const PDO_OPTS = [
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_FOUND_ROWS => true,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ];

    private ?\PDO $db = null;

    private int $dbStatus = self::STATUS_KO;

    private readonly \SP\Storage\Database\DatabaseConnectionData $connectionData;

    /**
     * MySQLHandler constructor.
     */
    public function __construct(DatabaseConnectionData $connectionData)
    {
        $this->connectionData = $connectionData;
    }

    /**
     * Devuelve el estado de conexión a la BBDD
     *
     * OK -> 0
     * KO -> 1
     *
     * @return int
     */
    public function getDbStatus()
    {
        return $this->dbStatus;
    }

    /**
     * Realizar la conexión con la BBDD.
     * Esta función utiliza PDO para conectar con la base de datos.
     *
     * @return PDO
     * @throws DatabaseException
     */
    public function getConnection()
    {
        if (!$this->db) {
            if (in_array(null, [$this->connectionData->getDbUser(), $this->connectionData->getDbPass(), $this->connectionData->getDbName()], true)
                || (null === $this->connectionData->getDbHost() && null === $this->connectionData->getDbSocket())
            ) {
                throw new DatabaseException(
                    __u('Unable to connect to DB'),
                    DatabaseException::CRITICAL,
                    __u('Please, check the connection parameters')
                );
            }

            try {
                $this->db = new PDO(
                    $this->getConnectionUri(),
                    $this->connectionData->getDbUser(),
                    $this->connectionData->getDbPass(),
                    self::PDO_OPTS
                );

                // Set prepared statement emulation depending on server version
                $serverVersion = $this->db->getAttribute(PDO::ATTR_SERVER_VERSION);
                $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, version_compare($serverVersion, '5.1.17', '<'));

                $this->dbStatus = self::STATUS_OK;
            } catch (Exception $e) {
                throw new DatabaseException(
                    __u('Unable to connect to DB'),
                    DatabaseException::CRITICAL,
                    sprintf('Error %s: %s', $e->getCode(), $e->getMessage()),
                    $e->getCode(),
                    $e
                );
            }
        }

        return $this->db;
    }

    public function getConnectionUri(): string
    {
        $dsn = ['charset=utf8'];

        if (empty($this->connectionData->getDbSocket())) {
            $dsn[] = 'host=' . $this->connectionData->getDbHost();

            if (null !== $this->connectionData->getDbPort()) {
                $dsn[] = 'port=' . $this->connectionData->getDbPort();
            }
        } else {
            $dsn[] = 'unix_socket=' . $this->connectionData->getDbSocket();
        }

        if (!empty($this->connectionData->getDbName())) {
            $dsn[] = 'dbname=' . $this->connectionData->getDbName();
        }

        return 'mysql:' . implode(';', $dsn);
    }

    /**
     * Obtener una conexión PDO sin seleccionar la BD
     *
     * @return PDO
     * @throws DatabaseException
     */
    public function getConnectionSimple()
    {
        if (!$this->db) {
            if (null === $this->connectionData->getDbHost() && null === $this->connectionData->getDbSocket()) {
                throw new DatabaseException(
                    __u('Unable to connect to DB'),
                    DatabaseException::CRITICAL,
                    __u('Please, check the connection parameters')
                );
            }

            try {
                $opts = [PDO::ATTR_EMULATE_PREPARES => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

                $this->db = new PDO($this->getConnectionUri(), $this->connectionData->getDbUser(), $this->connectionData->getDbPass(), $opts);
                $this->dbStatus = self::STATUS_OK;
            } catch (Exception $e) {
                throw new DatabaseException(
                    __u('Unable to connect to DB'),
                    DatabaseException::CRITICAL,
                    sprintf('Error %s: %s', $e->getCode(), $e->getMessage()),
                    $e->getCode(),
                    $e
                );
            }
        }

        return $this->db;
    }

    /**
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->connectionData->getDbName();
    }
}
