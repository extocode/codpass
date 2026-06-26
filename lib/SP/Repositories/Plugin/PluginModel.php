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

namespace SP\Repositories\Plugin;

use SP\DataModel\DataModelBase;
use SP\DataModel\DataModelInterface;

/**
 * Class PluginData
 *
 * @package SP\DataModel
 */
class PluginModel extends DataModelBase implements DataModelInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $data;

    /**
     * @var int
     */
    protected $enabled = 0;

    /**
     * @var int
     */
    protected $available = 1;

    /**
     * @var string
     */
    protected $versionLevel;

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

    public function getEnabled(): int
    {
        return (int) $this->enabled;
    }

    /**
     * @param int $enabled
     */
    public function setEnabled($enabled): void
    {
        $this->enabled = (int) $enabled;
    }

    public function getAvailable(): int
    {
        return (int) $this->available;
    }

    /**
     * @param int $available
     */
    public function setAvailable($available): void
    {
        $this->available = (int) $available;
    }

    /**
     * @return string
     */
    public function getVersionLevel()
    {
        return $this->versionLevel;
    }

    public function setVersionLevel(string $versionLevel): void
    {
        $this->versionLevel = $versionLevel;
    }
}
