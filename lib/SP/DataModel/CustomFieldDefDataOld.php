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
 * Class CustomFieldDefDataOld
 *
 * @package SP\DataModel
 */
class CustomFieldDefDataOld extends CustomFieldBaseData implements DataModelInterface
{
    /**
     * @var int
     */
    public $customfielddef_module = 0;

    /**
     * @var string
     */
    public $typeName = '';

    /**
     * @var string
     */
    public $moduleName = '';

    /**
     * @var bool
     */
    public $required = false;

    /**
     * @var string
     */
    public $help = '';

    /**
     * @var bool
     */
    public $showInItemsList = false;

    /**
     * @return int
     */
    public function getCustomfielddefModule()
    {
        return $this->customfielddef_module;
    }

    /**
     * @param int $customfielddef_module
     */
    public function setCustomfielddefModule($customfielddef_module): void
    {
        $this->customfielddef_module = $customfielddef_module;
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        return $this->typeName;
    }

    /**
     * @param string $typeName
     */
    public function setTypeName($typeName): void
    {
        $this->typeName = $typeName;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * @param string $moduleName
     */
    public function setModuleName($moduleName): void
    {
        $this->moduleName = $moduleName;
    }

    public function getFormId(): string
    {
        return 'cf_' . strtolower((string) preg_replace('/\W*/', '', $this->name));
    }

    /**
     * @return bool
     */
    public function isShowInItemsList()
    {
        return $this->showInItemsList;
    }

    /**
     * @param bool $showInItemsList
     */
    public function setShowInItemsList($showInItemsList): void
    {
        $this->showInItemsList = $showInItemsList;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param bool $required
     */
    public function setRequired($required): void
    {
        $this->required = $required;
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

    /**
     * @return int
     */
    public function getId()
    {
        return $this->customfielddef_id;
    }
}
