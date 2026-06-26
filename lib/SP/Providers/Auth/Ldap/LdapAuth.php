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
use SP\DataModel\UserLoginData;
use SP\Providers\Auth\AuthInterface;

/**
 * Class LdapBase
 *
 * @package Auth\Ldap
 */
final class LdapAuth implements AuthInterface
{
    public const ACCOUNT_EXPIRED = 701;

    public const ACCOUNT_NO_GROUPS = 702;

    /**
     * @var string
     */
    protected $userLogin;

    /**
     * @var LdapAuthData
     */
    protected $ldapAuthData;

    /**
     * @var LdapParams
     */
    protected $ldapParams;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var string
     */
    protected $server;

    private readonly \SP\Providers\Auth\Ldap\LdapInterface $ldap;

    /**
     * LdapBase constructor.
     */
    public function __construct(LdapInterface $ldap, EventDispatcher $eventDispatcher)
    {
        $this->ldap = $ldap;
        $this->eventDispatcher = $eventDispatcher;

        $this->ldapAuthData = new LdapAuthData();
    }

    /**
     * @return LdapAuthData
     */
    public function getLdapAuthData()
    {
        return $this->ldapAuthData;
    }

    /**
     * @return string
     */
    public function getUserLogin()
    {
        return $this->userLogin;
    }

    /**
     * @param string $userLogin
     */
    public function setUserLogin($userLogin): void
    {
        $this->userLogin = strtolower($userLogin);
    }

    /**
     * Autentificar al usuario
     *
     * @param UserLoginData $userLoginData Datos del usuario
     */
    public function authenticate(UserLoginData $userLoginData): bool
    {
        $__t = microtime(true);
        $__lap = static function (string $phase) use (&$__t): void {
            $now = microtime(true);
            logger(sprintf('LDAP-TIMING %s: %.1f ms', $phase, ($now - $__t) * 1000), 'INFO');
            $__t = $now;
        };

        try {
            $this->ldapAuthData->setAuthGranted($this->isAuthGranted());
            $this->ldapAuthData->setServer($this->ldap->getServer());

            $this->setUserLogin($userLoginData->getLoginUser());

            $this->ldap->connect();
            $__lap('connect+adminBind');

            $this->getAttributes($userLoginData->getLoginUser());
            $__lap('searchUserDn');

            $this->ldap->bind($this->ldapAuthData->getDn(), $userLoginData->getLoginPass());
            $__lap('userBind');
        } catch (LdapException $e) {
            processException($e);

            $this->ldapAuthData->setStatusCode($e->getCode());

            return false;
        }

        return true;
    }

    /**
     * Indica si es requerida para acceder a la aplicación
     */
    public function isAuthGranted(): bool
    {
        return true;
    }

    /**
     * Obtener los atributos del usuario.
     *
     *
     * @return LdapAuthData con los atributos disponibles y sus valores
     * @throws LdapException
     */
    public function getAttributes(string $userLogin)
    {
        $attributes = $this->ldap->getLdapActions()
            ->getAttributes($this->ldap->getUserDnFilter($userLogin));

        if (!empty($attributes->get('fullname'))) {
            $this->ldapAuthData->setName($attributes->get('fullname'));
        } else {
            $name = trim($attributes->get('name', '')
                . ' '
                . $attributes->get('sn', ''));

            $this->ldapAuthData->setName($name);
        }

        $mail = $attributes->get('mail');

        $this->ldapAuthData->setDn($attributes->get('dn'));
        $this->ldapAuthData->setEmail(is_array($mail) ? $mail[0] : $mail);
        $this->ldapAuthData->setExpire($attributes->get('expire'));

        $this->ldapAuthData->setInGroup(
            $this->ldap->isUserInGroup(
                $attributes['dn'],
                $userLogin,
                (array) $attributes->get('group')
            )
        );

        return $this->ldapAuthData;
    }
}
