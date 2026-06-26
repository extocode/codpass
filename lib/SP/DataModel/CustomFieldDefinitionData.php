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
 * Class CustomFieldDefData
 *
 * @package SP\DataModel
 */
class CustomFieldDefinitionData
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $moduleId;

    /**
     * @var string
     */
    public $field;

    /**
     * @var int
     */
    public $required;

    /**
     * @var string
     */
    public $help;

    /**
     * @var int
     */
    public $showInList;

    /**
     * @var int
     */
    public $typeId;

    /**
     * @var int
     */
    public $isEncrypted = 1;

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

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
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

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $field
     */
    public function setField($field): void
    {
        $this->field = $field;
    }

    public function getRequired(): int
    {
        return (int) $this->required;
    }

    /**
     * @param int $required
     */
    public function setRequired($required): void
    {
        $this->required = (int) $required;
    }

    /**
     * @return string
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * @param string $help
     */
    public function setHelp($help): void
    {
        $this->help = $help;
    }

    public function getShowInList(): int
    {
        return (int) $this->showInList;
    }

    /**
     * @param int $showInList
     */
    public function setShowInList($showInList): void
    {
        $this->showInList = (int) $showInList;
    }

    public function getTypeId(): int
    {
        return (int) $this->typeId;
    }

    /**
     * @param int $typeId
     */
    public function setTypeId($typeId): void
    {
        $this->typeId = (int) $typeId;
    }

    public function getisEncrypted(): int
    {
        return (int) $this->isEncrypted;
    }

    public function setIsEncrypted(int $isEncrypted): void
    {
        $this->isEncrypted = $isEncrypted;
    }
}
