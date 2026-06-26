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

/**
 * Interface LdapInterface
 *
 * @package Auth\Ldap
 */
interface LdapConnectionInterface
{
    /**
     * Comprobar la conexión al servidor de LDAP.
     */
    public function checkConnection();

    /**
     * Comprobar si los parámetros necesarios de LDAP están establecidos.
     *
     * @return bool
     */
    public function checkParams();

    /**
     * @return resource
     * @throws LdapException
     */
    public function connectAndBind();

    /**
     * Realizar la conexión al servidor de LDAP.
     *
     * @throws LdapException
     */
    public function connect(): bool;

    /**
     * @param string $bindDn
     * @param string $bindPass
     */
    public function bind(?string $bindDn = null, ?string $bindPass = null): bool;

    public function unbind(): bool;

    public function getLdapParams(): LdapParams;

    public function getServer(): string;

    /**
     * @return LdapConnectionInterface
     */
    public function setServer(string $server);
}
