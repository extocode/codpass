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
 * Class QueryAssignment
 *
 * @package SP\Mvc\Model
 */
final class QueryAssignment
{
    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $values = [];

    /**
     * @param $field
     * @param $value
     */
    public function addField(string $field, $value): self
    {
        if (!str_contains($field, '=')) {
            $this->fields[] = $field . ' = ?';
            $this->values[] = $value;
        }

        return $this;
    }

    public function setFields(array $fields, array $values): self
    {
        $this->fields = array_map(fn($value) => str_contains((string) $value, '=') ? $value : "{$value} = ?", $fields);

        $this->values = array_merge($this->values, $values);

        return $this;
    }

    public function getAssignments(): ?string
    {
        return $this->hasFields() ? implode(',', $this->fields) : null;
    }

    public function hasFields(): bool
    {
        return count($this->fields) > 0;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }
}
