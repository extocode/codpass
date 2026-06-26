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

namespace SP\Config;

use SP\Storage\File\FileCacheInterface;
use SP\Storage\File\FileException;

/**
 * Class ConfigCache
 *
 * @package SP\Config
 */
final readonly class ConfigCache
{
    /**
     * Cache file name
     */
    public const CONFIG_CACHE_FILE = CACHE_PATH . DIRECTORY_SEPARATOR . 'config.cache';

    private \SP\Storage\File\FileCacheInterface $fileCache;

    /**
     * ConfigCache constructor.
     */
    public function __construct(FileCacheInterface $fileCache)
    {
        $this->fileCache = $fileCache;
    }

    /**
     * Saves config into the cache file
     */
    public function saveConfigToCache(ConfigData $configData): void
    {
        try {
            $this->fileCache->save($configData);

            logger('Saved config cache');
        } catch (FileException $e) {
            processException($e);
        }
    }

    /**
     * Loads config from the cache file
     *
     * @return ConfigData
     */
    public function loadConfigFromCache(): ?\SP\Config\ConfigData
    {
        try {
            $configData = $this->fileCache->load();

            if ($configData instanceof ConfigData) {
                logger('Loaded config cache');

                return $configData;
            }
        } catch (FileException $e) {
            processException($e);
        }

        return null;
    }
}
