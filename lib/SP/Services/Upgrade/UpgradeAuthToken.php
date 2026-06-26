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

namespace SP\Services\Upgrade;

use Exception;
use SP\Core\Acl\ActionsInterface;
use SP\Core\Events\Event;
use SP\Core\Events\EventMessage;
use SP\Services\AuthToken\AuthTokenService;
use SP\Services\Service;

/**
 * Class UpgradeAuthToken
 *
 * @package SP\Services\Upgrade
 */
final class UpgradeAuthToken extends Service
{
    /**
     * @var AuthTokenService
     */
    private $authtokenService;

    /**
     * upgrade_300_18072901
     *
     * @throws Exception
     */
    public function upgrade_300_18072901(): void
    {
        $this->eventDispatcher->notifyEvent(
            'upgrade.authToken.start',
            new Event($this, EventMessage::factory()
                ->addDescription(__u('API authorizations update'))
                ->addDescription(__FUNCTION__))
        );

        try {
            $this->transactionAware(function (): void {
                foreach ($this->authtokenService->getAllBasic() as $item) {
                    $itemData = clone $item;
                    $itemData->setActionId($this->actionMapper($item->getActionId()));

                    $this->authtokenService->updateRaw($itemData);

                    $this->eventDispatcher->notifyEvent(
                        'upgrade.authToken.process',
                        new Event($this, EventMessage::factory()
                            ->addDescription(__u('Authorization updated'))
                            ->addDetail(__u('Authorization'), $item->getId()))
                    );
                }
            });
        } catch (Exception $e) {
            processException($e);

            $this->eventDispatcher->notifyEvent('exception', new Event($e));

            throw $e;
        }

        $this->eventDispatcher->notifyEvent(
            'upgrade.authToken.end',
            new Event($this, EventMessage::factory()
                ->addDescription(__u('API authorizations update'))
                ->addDescription(__FUNCTION__))
        );
    }

    private function actionMapper(int $moduleId): int
    {
        return match ($moduleId) {
            1 => ActionsInterface::ACCOUNT_SEARCH,
            100 => ActionsInterface::ACCOUNT_VIEW,
            104 => ActionsInterface::ACCOUNT_VIEW_PASS,
            103 => ActionsInterface::ACCOUNT_DELETE,
            101 => ActionsInterface::ACCOUNT_CREATE,
            615 => ActionsInterface::CATEGORY_SEARCH,
            610 => ActionsInterface::CATEGORY_VIEW,
            611 => ActionsInterface::CATEGORY_CREATE,
            612 => ActionsInterface::CATEGORY_EDIT,
            613 => ActionsInterface::CATEGORY_DELETE,
            625 => ActionsInterface::CLIENT_SEARCH,
            620 => ActionsInterface::CLIENT_VIEW,
            621 => ActionsInterface::CLIENT_CREATE,
            622 => ActionsInterface::CLIENT_EDIT,
            623 => ActionsInterface::CLIENT_DELETE,
            685 => ActionsInterface::TAG_SEARCH,
            681 => ActionsInterface::TAG_VIEW,
            680 => ActionsInterface::TAG_CREATE,
            682 => ActionsInterface::TAG_EDIT,
            683 => ActionsInterface::TAG_DELETE,
            1041 => ActionsInterface::CONFIG_BACKUP_RUN,
            1061 => ActionsInterface::CONFIG_EXPORT_RUN,
            default => $moduleId,
        };
    }

    protected function initialize(): void
    {
        $this->authtokenService = $this->dic->get(AuthTokenService::class);
    }
}
