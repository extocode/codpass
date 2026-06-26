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

namespace SP\Services\Import;

/**
 * Class ImportParams
 *
 * @package SP\Services\Import
 */
final class ImportParams
{
    /**
     * @var string
     */
    protected $importPwd;

    /**
     * @var string
     */
    protected $importMasterPwd;

    /**
     * @var int
     */
    protected $defaultUser = 0;

    /**
     * @var int
     */
    protected $defaultGroup = 0;

    /**
     * @var string
     */
    protected $csvDelimiter = ';';

    /**
     * @return string
     */
    public function getImportPwd()
    {
        return $this->importPwd;
    }

    /**
     * @param string $importPwd
     */
    public function setImportPwd($importPwd): void
    {
        $this->importPwd = $importPwd;
    }

    /**
     * @return int
     */
    public function getDefaultGroup()
    {
        return $this->defaultGroup;
    }

    /**
     * @param int $defaultGroup
     */
    public function setDefaultGroup($defaultGroup): void
    {
        $this->defaultGroup = (int) $defaultGroup;
    }

    /**
     * @return string
     */
    public function getCsvDelimiter()
    {
        return $this->csvDelimiter;
    }

    /**
     * @param string $csvDelimiter
     */
    public function setCsvDelimiter($csvDelimiter): void
    {
        $this->csvDelimiter = $csvDelimiter;
    }

    /**
     * @return string
     */
    public function getImportMasterPwd()
    {
        return $this->importMasterPwd;
    }

    /**
     * @param string $importMasterPwd
     */
    public function setImportMasterPwd($importMasterPwd): void
    {
        $this->importMasterPwd = $importMasterPwd;
    }

    /**
     * @return int
     */
    public function getDefaultUser()
    {
        return $this->defaultUser;
    }

    /**
     * @param int $defaultUser
     */
    public function setDefaultUser($defaultUser): void
    {
        $this->defaultUser = (int) $defaultUser;
    }
}
