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

use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Exception;
use Psr\Container\ContainerInterface;
use ReflectionObject;
use SP\Core\Context\ContextInterface;
use SP\Core\Exceptions\ConfigException;
use SP\Services\Config\ConfigBackupService;
use SP\Storage\File\FileCacheInterface;
use SP\Storage\File\FileException;
use SP\Storage\File\XmlFileStorageInterface;
use SP\Util\PasswordUtil;

defined('APP_ROOT') || die();

/**
 * Esta clase es responsable de leer y escribir la configuración del archivo config.php
 */
final class Config
{
    /**
     * Cache file name
     */
    public const CONFIG_CACHE_FILE = CACHE_PATH . DIRECTORY_SEPARATOR . 'config.cache';

    /**
     * @var int
     */
    private static $timeUpdated;

    /**
     * @var ContextInterface
     */
    private $context;

    private bool $configLoaded = false;

    /**
     * @var ConfigData
     */
    private $configData;

    private readonly \SP\Storage\File\XmlFileStorageInterface $fileStorage;

    private readonly \SP\Storage\File\FileCacheInterface $fileCache;

    private readonly \Psr\Container\ContainerInterface $dic;

    /**
     * Config constructor.
     *
     *
     * @throws ConfigException
     */
    public function __construct(XmlFileStorageInterface $fileStorage, FileCacheInterface $fileCache, ContainerInterface $dic)
    {
        $this->fileCache = $fileCache;
        $this->fileStorage = $fileStorage;
        $this->context = $dic->get(ContextInterface::class);
        $this->dic = $dic;

        $this->initialize();
    }

    /**
     * @throws ConfigException
     */
    private function initialize(): void
    {
        if (!$this->configLoaded) {
            try {
                if ($this->fileCache->exists()
                    && !$this->isCacheExpired()
                ) {
                    $this->configData = $this->fileCache->load();

                    logger('Config cache loaded');
                } else {
                    if (file_exists($this->fileStorage->getFileHandler()->getFile())) {
                        $this->configData = $this->loadConfigFromFile();
                        $this->fileCache->save($this->configData);
                    } else {
                        $configData = new ConfigData();

                        // Generate a random salt that is used to add more seed to some passwords
                        $configData->setPasswordSalt(PasswordUtil::generateRandomBytes(30));

                        $this->saveConfig($configData, false);

                        logger('Config file created', 'INFO');
                    }

                    logger('Config loaded');
                }

                self::$timeUpdated = $this->configData->getConfigDate();

                $this->configLoaded = true;
            } catch (Exception $e) {
                processException($e);

                throw new ConfigException(
                    $e->getMessage(),
                    ConfigException::CRITICAL,
                    null,
                    $e->getCode(),
                    $e
                );
            }
        }
    }

    /**
     * @return bool
     */
    private function isCacheExpired()
    {
        try {
            return $this->fileCache->isExpiredDate($this->fileStorage->getFileHandler()->getFileTime());
        } catch (FileException) {
            return true;
        }
    }

    /**
     * Cargar el archivo de configuración
     *
     * @throws FileException
     */
    public function loadConfigFromFile(): \SP\Config\ConfigData
    {
        $configData = new ConfigData();

        // Mapear el array de elementos de configuración con las propiedades de la clase configData
        $items = $this->fileStorage->load('config')->getItems();
        $reflectionObject = new ReflectionObject($configData);

        foreach ($reflectionObject->getProperties() as $property) {
            if (isset($items[$property->getName()])) {
                $value = $items[$property->getName()];
                $propertyType = $property->getType();

                // Handle type conversion for typed properties
                if ($propertyType instanceof \ReflectionNamedType && !$propertyType->isBuiltin()) {
                    // Skip complex types - let them be handled as-is
                    $property->setValue($configData, $value);
                } elseif ($propertyType instanceof \ReflectionNamedType) {
                    $typeName = $propertyType->getName();

                    // Convert string values to appropriate types
                    if ($typeName === 'array' && is_string($value)) {
                        // Convert comma-separated string to array, or empty string to empty array
                        $value = $value === '' ? [] : array_map('trim', explode(',', $value));
                    } elseif ($typeName === 'bool' && is_string($value)) {
                        $value = $value === '1' || $value === 'true';
                    } elseif ($typeName === 'int' && is_string($value)) {
                        $value = (int) $value;
                    } elseif ($typeName === 'float' && is_string($value)) {
                        $value = (float) $value;
                    }

                    $property->setValue($configData, $value);
                } else {
                    $property->setValue($configData, $value);
                }
            }
        }

        return $configData;
    }

    /**
     * Guardar la configuración
     *
     * @param bool       $backup
     *
     * @throws FileException
     */
    public function saveConfig(ConfigData $configData, $backup = true): self
    {
        if ($backup) {
            $this->dic->get(ConfigBackupService::class)
                ->backup($configData);
        }

        $configData->setConfigDate(time());
        $configData->setConfigSaver($this->context->getUserData()->getLogin() ?: 'sysPass');
        $configData->setConfigHash();

        $this->fileStorage->save($configData, 'config');
        $this->fileCache->save($configData);

        $this->configData = $configData;

        return $this;
    }

    /**
     * @return int
     */
    public static function getTimeUpdated()
    {
        return self::$timeUpdated;
    }

    /**
     * Commits a config data
     *
     */
    public function updateConfig(ConfigData $configData): self
    {
        $configData->setConfigDate(time());
        $configData->setConfigSaver($this->context->getUserData()->getLogin());
        $configData->setConfigHash();

        $this->configData = $configData;

        self::$timeUpdated = $configData->getConfigDate();

        return $this;
    }

    /**
     * Cargar la configuración desde el contexto
     *
     * @param bool $reload
     *
     * @return ConfigData
     */
    public function loadConfig($reload = false)
    {
        try {
            $configData = $this->fileCache->load();

            if ($reload === true
                || $configData === null
                || $this->isCacheExpired()
            ) {
                $this->configData = $this->loadConfigFromFile();
                $this->fileCache->save($this->configData);

                return $this->configData;
            }

            return $configData;
        } catch (FileException $e) {
            processException($e);
        }

        return $this->configData;
    }

    /**
     * @return ConfigData
     */
    public function getConfigData()
    {
        return clone $this->configData;
    }

    /**
     * @return Config
     * @throws FileException
     * @throws EnvironmentIsBrokenException
     */
    public function generateUpgradeKey()
    {
        if (empty($this->configData->getUpgradeKey())) {
            logger('Generating upgrade key');

            return $this->saveConfig($this->configData->setUpgradeKey(PasswordUtil::generateRandomBytes(16)), false);
        }

        return $this;
    }
}
