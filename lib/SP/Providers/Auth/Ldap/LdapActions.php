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

/** @noinspection PhpComposerExtensionStubsInspection */

namespace SP\Providers\Auth\Ldap;

use SP\Core\Events\Event;
use SP\Core\Events\EventDispatcher;
use SP\Core\Events\EventMessage;

/**
 * Class LdapActions
 *
 * @package SP\Providers\Auth\Ldap
 */
final class LdapActions
{
    /**
     * Atributos de búsqueda
     */
    public const USER_ATTRIBUTES = [
        'dn',
        'displayname',
        'samaccountname',
        'mail',
        'memberof',
        'lockouttime',
        'fullname',
        'groupmembership',
        'uid',
        'givenname',
        'sn',
        'userprincipalname',
        'cn',
    ];

    public const ATTRIBUTES_MAPPING = [
        'dn' => 'dn',
        'groupmembership' => 'group',
        'memberof' => 'group',
        'displayname' => 'fullname',
        'fullname' => 'fullname',
        'givenname' => 'name',
        'sn' => 'sn',
        'mail' => 'mail',
        'lockouttime' => 'expire',
    ];

    private readonly \SP\Providers\Auth\Ldap\LdapParams $ldapParams;

    /**
     * @var resource
     */
    private $ldapHandler;

    private readonly \SP\Core\Events\EventDispatcher $eventDispatcher;

    /**
     * LdapActions constructor.
     *
     *
     * @throws LdapException
     */
    public function __construct(LdapConnectionInterface $ldapConnection, EventDispatcher $eventDispatcher)
    {
        $this->ldapHandler = $ldapConnection->connectAndBind();
        $this->ldapParams = $ldapConnection->getLdapParams();
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Obtener el RDN del grupo.
     *
     *
     * @return array Groups' DN
     * @throws LdapException
     */
    public function searchGroupsDn(string $groupFilter): array
    {
        $filter = '(&(cn='
            . ldap_escape($this->getGroupFromParams(), '', LDAP_ESCAPE_FILTER)
            . ')'
            . $groupFilter
            . ')';

        $searchResults = $this->getResults($filter, ['dn']);

        if ((int) $searchResults['count'] === 0) {
            $this->eventDispatcher->notifyEvent(
                'ldap.search.group',
                new Event($this, EventMessage::factory()
                    ->addDescription(__u('Error while searching the group RDN'))
                    ->addDetail(__u('Group'), $this->getGroupFromParams())
                    ->addDetail('LDAP ERROR', LdapConnection::getLdapErrorMessage($this->ldapHandler))
                    ->addDetail('LDAP FILTER', $filter))
            );

            throw new LdapException(
                __u('Error while searching the group RDN'),
                LdapException::ERROR,
                null,
                LdapCode::NO_SUCH_OBJECT
            );
        }

        return array_filter(array_map(function ($value) {
            if (is_array($value)) {
                return $value['dn'];
            }

            return null;
        }, $searchResults));
    }

    protected function getGroupFromParams(): string
    {
        if (stripos($this->ldapParams->getGroup(), 'cn') === 0) {
            return LdapUtil::getGroupName($this->ldapParams->getGroup());
        }

        return $this->ldapParams->getGroup();
    }

    /**
     * Devolver los resultados de una paginación
     *
     * @param string $filter     Filtro a utilizar
     * @param array  $attributes Atributos a devolver
     * @param string $searchBase
     */
    protected function getResults($filter, ?array $attributes = null, $searchBase = null): false|array
    {
        $cookie = '';
        $results = [];

        if (empty($searchBase)) {
            $searchBase = $this->ldapParams->getSearchBase();
        }

        do {
            // PHP 8.0 removed ldap_control_paged_result(); paging is now done
            // via the $controls argument of ldap_search() and the response
            // cookie is read back from ldap_parse_result().
            $controls = [
                [
                    'oid' => LDAP_CONTROL_PAGEDRESULTS,
                    'value' => ['size' => LdapInterface::PAGE_SIZE, 'cookie' => $cookie],
                ],
            ];

            $searchRes = @ldap_search(
                $this->ldapHandler,
                $searchBase,
                $filter,
                $attributes ?? [],
                0,
                -1,
                -1,
                LDAP_DEREF_NEVER,
                $controls
            );

            if (!$searchRes) {
                return false;
            }

            $entries = @ldap_get_entries($this->ldapHandler, $searchRes);

            if (!$entries) {
                return false;
            }

            $results = array_merge($results, $entries);

            $responseControls = [];

            if (@ldap_parse_result(
                $this->ldapHandler,
                $searchRes,
                $errcode,
                $matcheddn,
                $errmsg,
                $referrals,
                $responseControls
            )) {
                $cookie = $responseControls[LDAP_CONTROL_PAGEDRESULTS]['value']['cookie'] ?? '';
            } else {
                $cookie = '';
            }
        } while ($cookie !== '' && $cookie !== '0' && $entries['count'] > 0);

        return $results;
    }

    /**
     * Obtener los atributos del usuario.
     *
     *
     * @throws LdapException
     */
    public function getAttributes(string $filter): \SP\Providers\Auth\Ldap\AttributeCollection
    {
        $searchResults = $this->getObjects($filter);

        if ((int) $searchResults['count'] === 0) {
            $this->eventDispatcher->notifyEvent(
                'ldap.getAttributes',
                new Event($this, EventMessage::factory()
                    ->addDescription(__u('Error while searching the user on LDAP'))
                    ->addDetail('LDAP FILTER', $filter))
            );

            throw new LdapException(
                __u('Error while searching the user on LDAP'),
                LdapException::ERROR,
                null,
                LdapCode::NO_SUCH_OBJECT
            );
        }

        // Normalize keys for comparing
        $results = array_change_key_case($searchResults[0], CASE_LOWER);

        $attributeCollection = new AttributeCollection();

        foreach (self::ATTRIBUTES_MAPPING as $attribute => $map) {
            if (isset($results[$attribute])) {
                if (is_array($results[$attribute])) {
                    if ((int) $results[$attribute]['count'] > 1) {
                        unset($results[$attribute]['count']);

                        // Store the whole array
                        $attributeCollection->set($map, $results[$attribute]);
                    } else {
                        // Store first value
                        $attributeCollection->set($map, trim((string) $results[$attribute][0]));
                    }
                } else {
                    $attributeCollection->set($map, trim((string) $results[$attribute]));
                }
            }
        }

        return $attributeCollection;
    }

    /**
     * Obtener los objetos según el filtro indicado
     *
     * @param string $filter
     * @param string $searchBase
     *
     * @return array
     * @throws LdapException
     */
    public function getObjects($filter, array $attributes = self::USER_ATTRIBUTES, $searchBase = null)
    {
        $searchResults = $this->getResults($filter, $attributes, $searchBase);

        if ($searchResults === false) {
            $this->eventDispatcher->notifyEvent(
                'ldap.search',
                new Event($this, EventMessage::factory()
                    ->addDescription(__u('Error while searching objects in base DN'))
                    ->addDetail('LDAP ERROR', LdapConnection::getLdapErrorMessage($this->ldapHandler))
                    ->addDetail('LDAP FILTER', $filter))
            );

            throw new LdapException(
                __u('Error while searching objects in base DN'),
                LdapException::ERROR,
                null,
                LdapCode::OPERATIONS_ERROR
            );
        }

        return $searchResults;
    }
}
