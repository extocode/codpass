<?php

declare(strict_types=1);

/**
 * sysPass - PHP-DI Container Definitions
 *
 * This file configures the PHP-DI dependency injection container.
 * PHP-DI 7 supports PHP 8 attributes for autowiring. You can use these attributes
 * in service classes. Then you do not have to define everything here.
 *
 * Autowiring (default behavior):
 * - PHP-DI automatically resolves constructor dependencies by type
 * - Simple services do not need configuration
 * - Use the #[Inject] attribute for property/method injection
 *
 * Explicit definitions (this file):
 * - Use them for interfaces that need concrete implementations
 * - Use them for services that need special construction
 * - Use them for scalar/non-typed parameters
 *
 * @see https://php-di.org/doc/php-definitions.html
 * @see https://php-di.org/doc/attributes.html
 *
 * @author nuxsmin
 * @link https://syspass.org
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

use Monolog\Logger;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Container\ContainerInterface;
use SP\Config\Config;
use SP\Config\ConfigData;
use SP\Core\Acl\Acl;
use SP\Core\Acl\Actions;
use SP\Core\Context\ContextInterface;
use SP\Core\Context\SessionContext;
use SP\Core\Context\StatelessContext;
use SP\Core\Events\EventDispatcherInterface;
use SP\Core\Events\Psr14\Psr14EventDispatcherAdapter;
use SP\Core\MimeTypes;
use SP\Core\UI\Theme;
use SP\Core\UI\ThemeInterface;
use SP\Http\Client;
use SP\Http\Request;
use SP\Services\Account\AccountAclService;
use SP\Storage\Database\DatabaseConnectionData;
use SP\Storage\Database\DBStorageInterface;
use SP\Storage\Database\MySQLHandler;
use SP\Storage\File\FileCache;
use SP\Storage\File\FileHandler;
use SP\Storage\File\XmlHandler;
use function DI\autowire;
use function DI\create;
use function DI\factory;
use function DI\get;

/**
 * Container definitions
 *
 * PHP-DI auto-wires the services that are not in this list.
 * Define only the services that:
 * 1. Implement an interface (bind the interface to the implementation)
 * 2. Need special construction parameters
 * 3. Need scalar values that PHP-DI cannot auto-wire
 */
return [
    /*
     * -------------------------------------------------------------------------
     * HTTP Layer
     * -------------------------------------------------------------------------
     */
    Request::class => create(Request::class)
        ->constructor(\Klein\Request::createFromGlobals()),

    /*
     * -------------------------------------------------------------------------
     * Context (Session/Stateless) - Module-dependent
     * -------------------------------------------------------------------------
     */
    ContextInterface::class => static function (ContainerInterface $c): ContextInterface {
        return match (APP_MODULE) {
            'web' => $c->get(SessionContext::class),
            default => $c->get(StatelessContext::class),
        };
    },

    /*
     * -------------------------------------------------------------------------
     * Configuration
     * -------------------------------------------------------------------------
     */
    Config::class => static function (ContainerInterface $c): Config {
        return new Config(
            new XmlHandler(new FileHandler(CONFIG_FILE)),
            new FileCache(Config::CONFIG_CACHE_FILE),
            $c
        );
    },

    ConfigData::class => static function (Config $config): ConfigData {
        return $config->getConfigData();
    },

    /*
     * -------------------------------------------------------------------------
     * Database Layer
     * -------------------------------------------------------------------------
     */
    DBStorageInterface::class => create(MySQLHandler::class)
        ->constructor(factory([DatabaseConnectionData::class, 'getFromConfig'])),

    /*
     * -------------------------------------------------------------------------
     * Core Services (require file-based configuration)
     * -------------------------------------------------------------------------
     */
    Actions::class => static fn(): Actions => new Actions(
        new FileCache(Actions::ACTIONS_CACHE_FILE),
        new XmlHandler(new FileHandler(ACTIONS_FILE))
    ),

    MimeTypes::class => static fn(): MimeTypes => new MimeTypes(
        new FileCache(MimeTypes::MIME_CACHE_FILE),
        new XmlHandler(new FileHandler(MIMETYPES_FILE))
    ),

    /*
     * -------------------------------------------------------------------------
     * Event System - PSR-14 Adapter
     * -------------------------------------------------------------------------
     */
    EventDispatcherInterface::class => autowire(Psr14EventDispatcherAdapter::class),

    /*
     * -------------------------------------------------------------------------
     * ACL & Security
     * -------------------------------------------------------------------------
     */
    Acl::class => autowire(Acl::class)
        ->constructorParameter('action', get(Actions::class)),

    /*
     * -------------------------------------------------------------------------
     * UI Layer
     * -------------------------------------------------------------------------
     */
    ThemeInterface::class => autowire(Theme::class)
        ->constructorParameter('module', APP_MODULE)
        ->constructorParameter('fileCache', new FileCache(Theme::ICONS_CACHE_FILE)),

    /*
     * -------------------------------------------------------------------------
     * External Services
     * -------------------------------------------------------------------------
     */
    PHPMailer::class => create(PHPMailer::class)
        ->constructor(true),

    Logger::class => create(Logger::class)
        ->constructor('syspass'),

    \GuzzleHttp\Client::class => create(\GuzzleHttp\Client::class)
        ->constructor(factory([Client::class, 'getOptions'])),

    /*
     * -------------------------------------------------------------------------
     * Auto-wired Services (explicit for IDE support)
     *
     * PHP-DI auto-wires these services. This list shows them for documentation
     * and for IDE autocomplete when you use the container.
     * -------------------------------------------------------------------------
     */
    AccountAclService::class => autowire(AccountAclService::class),
];