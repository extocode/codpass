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

/**
 * Class QueryResult
 *
 * @package SP\Storage\Database
 */
final class QueryResult
{
    private ?array $data = null;

    private int $numRows = 0;

    /**
     * @var int
     */
    private $totalNumRows;

    private ?int $affectedNumRows = null;

    private ?int $statusCode = null;

    private string|int|bool|null $lastId = 0;

    /**
     * QueryResult constructor.
     *
     * @param array $data
     */
    public function __construct(?array $data = null)
    {
        if ($data !== null) {
            $this->data = $data;
            $this->numRows = count($data);
        }
    }

    public static function fromResults(array $data, $totalNumRows = null): self
    {
        $result = new self($data);

        if ($totalNumRows !== null) {
            $result->totalNumRows = $totalNumRows;
        }

        return $result;
    }

    /**
     * @return mixed|null
     */
    public function getData()
    {
        if ($this->numRows === 1) {
            return $this->data[0];
        }
        return null;
    }

    /**
     * Always returns an array
     */
    public function getDataAsArray(): array
    {
        return (array) $this->data;
    }

    public function getNumRows(): int
    {
        return $this->numRows;
    }

    public function getTotalNumRows(): int
    {
        return $this->totalNumRows;
    }

    public function setTotalNumRows(int $totalNumRows): self
    {
        $this->totalNumRows = $totalNumRows;

        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function getAffectedNumRows(): int
    {
        return $this->affectedNumRows;
    }

    public function setAffectedNumRows(int $affectedNumRows): self
    {
        $this->affectedNumRows = $affectedNumRows;

        return $this;
    }

    public function getLastId(): string|int|bool|null
    {
        return $this->lastId;
    }

    public function setLastId(string|int|bool|null $lastId): self
    {
        $this->lastId = $lastId;

        return $this;
    }
}
