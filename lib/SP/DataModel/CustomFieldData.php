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
 * Class CustomFieldData
 *
 * @package SP\DataModel
 */
class CustomFieldData
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $moduleId;

    /**
     * @var int
     */
    public $itemId;

    /**
     * @var int
     */
    public $definitionId;

    /**
     * @var string
     */
    public $data;

    /**
     * @var string
     */
    public $key;

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

    public function getModuleId(): int
    {
        return (int) $this->moduleId;
    }

    /**
     * @param int $moduleId
     */
    public function setModuleId($moduleId): void
    {
        $this->moduleId = (int) $moduleId;
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

    public function getDefinitionId(): int
    {
        return (int) $this->definitionId;
    }

    /**
     * @param int $definitionId
     */
    public function setDefinitionId($definitionId): void
    {
        $this->definitionId = (int) $definitionId;
    }

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

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key): void
    {
        $this->key = $key;
    }
}
