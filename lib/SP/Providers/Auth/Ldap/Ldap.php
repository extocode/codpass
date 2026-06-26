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

namespace SP\Providers\Auth\Ldap;

use SP\Core\Events\EventDispatcher;

/**
 * Class Ldap
 *
 * @package SP\Providers\Auth\Ldap
 */
abstract class Ldap implements LdapInterface
{
    /**
     * @var LdapParams
     */
    protected $ldapParams;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var LdapActions
     */
    protected $ldapActions;

    /**
     * @var LdapConnectionInterface
     */
    protected $ldapConnection;

    /**
     * @var string
     */
    private $server;

    /**
     * LdapBase constructor.
     *
     *
     * @throws LdapException
     */
    public function __construct(LdapConnectionInterface $ldapConnection, EventDispatcher $eventDispatcher)
    {
        $this->ldapConnection = $ldapConnection;

        $this->ldapParams = $ldapConnection->getLdapParams();
        $this->server = $this->pickServer();

        $this->ldapConnection->setServer($this->server);

        $this->eventDispatcher = $eventDispatcher;
        $this->ldapActions = new LdapActions($ldapConnection, $eventDispatcher);
    }

    /**
     * Obtener el servidor de LDAP a utilizar
     *
     * @return mixed
     */
    abstract protected function pickServer();

    /**
     *
     * @return LdapInterface
     * @throws LdapException
     */
    public static function factory(LdapParams $ldapParams, EventDispatcher $eventDispatcher, bool $debug)
    {
        $ldapConnection = new LdapConnection($ldapParams, $eventDispatcher, $debug);
        $ldapConnection->checkConnection();
        return match ($ldapParams->getType()) {
            LdapTypeInterface::LDAP_STD => new LdapStd($ldapConnection, $eventDispatcher),
            LdapTypeInterface::LDAP_ADS => new LdapMsAds($ldapConnection, $eventDispatcher),
            LdapTypeInterface::LDAP_AZURE => new LdapMsAzureAd($ldapConnection, $eventDispatcher),
            default => throw new LdapException(__u('LDAP type not set')),
        };
    }

    public function getLdapActions(): LdapActions
    {
        return $this->ldapActions;
    }

    /**
     * Realizar la conexión al servidor de LDAP.
     *
     * @return resource
     * @throws LdapException
     */
    public function connect()
    {
        return $this->ldapConnection->connectAndBind();
    }

    /**
     * @param string $bindDn
     * @param string $bindPass
     */
    public function bind(?string $bindDn = null, ?string $bindPass = null): bool
    {
        return $this->ldapConnection->bind($bindDn, $bindPass);
    }

    public function getServer(): string
    {
        return $this->server;
    }

    protected function getGroupFromParams(): string
    {
        if (stripos($this->ldapParams->getGroup(), 'cn') === 0) {
            return LdapUtil::getGroupName($this->ldapParams->getGroup());
        }

        return $this->ldapParams->getGroup();
    }

    /**
     * @throws LdapException
     */
    protected function getGroupDn(): string
    {
        if (stripos($this->ldapParams->getGroup(), 'cn') === 0) {
            return $this->ldapParams->getGroup();
        }

        return $this->ldapActions->searchGroupsDn($this->getGroupObjectFilter())[0];
    }
}
