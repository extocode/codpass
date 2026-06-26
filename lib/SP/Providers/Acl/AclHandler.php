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

namespace SP\Providers\Acl;

use DI\Container;
use Exception;
use Psr\Container\ContainerInterface;
use SP\Core\Events\Event;
use SP\Core\Events\EventReceiver;
use SP\Providers\EventsTrait;
use SP\Providers\Provider;
use SP\Repositories\Account\AccountToUserRepository;
use SP\Services\Account\AccountAclService;
use SP\Services\UserGroup\UserGroupService;
use SP\Services\UserProfile\UserProfileService;
use SplSubject;

/**
 * Class AclHandler
 *
 * @package SP\Providers\Acl
 */
final class AclHandler extends Provider implements EventReceiver
{
    use EventsTrait;

    public const EVENTS = [
        'edit.userProfile',
        'edit.user',
        'edit.userGroup',
        'edit.account',
        'edit.account.bulk',
        'delete.user',
        'delete.user.selection',
    ];

    /**
     * @var string
     */
    private $events;

    private ?\DI\Container $dic = null;

    /**
     * Devuelve los eventos que implementa el observador
     */
    public function getEvents(): array
    {
        return self::EVENTS;
    }

    /**
     * Devuelve los eventos que implementa el observador en formato cadena
     *
     * @return string
     */
    public function getEventsString()
    {
        return $this->events;
    }

    /**
     * Receive update from subject
     *
     * @link  https://php.net/manual/en/splobserver.update.php
     *
     * @param SplSubject $subject <p>
     *                            The <b>SplSubject</b> notifying the observer of an update.
     *                            </p>
     *
     * @since 5.1.0
     */
    public function update(SplSubject $subject): void
    {
        $this->updateEvent('update', new Event($subject));
    }

    /**
     * Evento de actualización
     *
     * @param string $eventType Nombre del evento
     * @param Event  $event     Objeto del evento
     */
    public function updateEvent($eventType, Event $event): void
    {
        switch ($eventType) {
            case 'edit.userProfile':
                $this->processUserProfile($event);
                break;
            case 'edit.user':
            case 'delete.user':
            case 'delete.user.selection':
                $this->processUser($event);
                break;
            case 'edit.userGroup':
                $this->processUserGroup($event);
                break;
            case 'edit.account':
            case 'edit.account.bulk':
                $this->processAccount($event);
                break;
        }
    }

    private function processUserProfile(Event $event): void
    {
        try {
            $eventMessage = $event->getEventMessage();
            $extra = $eventMessage->getExtra();

            if (isset($extra['userProfileId'])) {
                $userProfileService = $this->dic->get(UserProfileService::class);

                foreach ($userProfileService->getUsersForProfile($extra['userProfileId'][0]) as $user) {
                    AccountAclService::clearAcl((string)$user->id);
                }
            }
        } catch (Exception $e) {
            processException($e);
        }
    }

    private function processUser(Event $event): void
    {
        $eventMessage = $event->getEventMessage();
        $extra = $eventMessage->getExtra();

        if (isset($extra['userId'])) {
            foreach ($extra['userId'] as $id) {
                AccountAclService::clearAcl((string)$id);
            }
        }
    }

    private function processUserGroup(Event $event): void
    {
        try {
            $eventMessage = $event->getEventMessage();
            $extra = $eventMessage->getExtra();

            if (isset($extra['userGroupId'])) {
                $userGroupService = $this->dic->get(UserGroupService::class);

                foreach ($userGroupService->getUsageByUsers($extra['userGroupId'][0]) as $user) {
                    AccountAclService::clearAcl((string)$user->id);
                }
            }
        } catch (Exception $e) {
            processException($e);
        }
    }

    private function processAccount(Event $event): void
    {
        try {
            $eventMessage = $event->getEventMessage();
            $extra = $eventMessage->getExtra();

            $accountToUserRepository = $this->dic->get(AccountToUserRepository::class);

            // Handle single account edit
            if (isset($extra['accountId'])) {
                foreach ($accountToUserRepository->getUsersByAccountId($extra['accountId']) as $user) {
                    AccountAclService::clearAcl((string)$user->id);
                }
            }

            // Handle bulk account edit
            if (isset($extra['accountIds'])) {
                foreach ($extra['accountIds'] as $accountId) {
                    foreach ($accountToUserRepository->getUsersByAccountId($accountId) as $user) {
                        AccountAclService::clearAcl((string)$user->id);
                    }
                }
            }
        } catch (Exception $e) {
            processException($e);
        }
    }

    protected function initialize(Container $dic): void
    {
        $this->dic = $dic;
        $this->events = $this->parseEventsToRegex(self::EVENTS);
    }
}
