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

namespace SP\Mvc\Model;

/**
 * Class QueryJoin
 *
 * @package SP\Mvc\Model
 */
final class QueryJoin
{
    /**
     * @var array
     */
    protected $join = [];

    /**
     * @var array
     */
    protected $param = [];

    /**
     * @param string $join
     * @param array  $params
     */
    public function addJoin($join, ?array $params = null): self
    {
        $this->join[] = $join;

        if ($params !== null) {
            $this->param = array_merge($this->param, $params);
        }

        return $this;
    }

    public function getJoins(): ?string
    {
        return $this->hasJoins() ? implode(PHP_EOL, $this->join) : null;
    }

    public function hasJoins(): bool
    {
        return !empty($this->join);
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->param;
    }

    public function getJoinsCount(): int
    {
        return count($this->join);
    }
}
