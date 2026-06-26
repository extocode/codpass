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
use SP\Services\CustomField\CustomFieldService;
use SP\Services\Service;
use SP\Storage\Database\Database;
use SP\Storage\Database\QueryData;

/**
 * Class UpgradeCustomFieldData
 *
 * @package SP\Services\Upgrade
 */
final class UpgradeCustomFieldData extends Service
{
    /**
     * @var Database
     */
    private $db;

    /**
     * upgrade_300_18072902
     *
     * @throws Exception
     */
    public function upgrade_300_18072902(): void
    {
        $this->eventDispatcher->notifyEvent(
            'upgrade.customField.start',
            new Event($this, EventMessage::factory()
                ->addDescription(__u('Custom fields update'))
                ->addDescription(__FUNCTION__))
        );

        try {
            $this->transactionAware(function (): void {
                $customFieldService = $this->dic->get(CustomFieldService::class);

                foreach ($customFieldService->getAll() as $item) {
                    $queryData = new QueryData();
                    $queryData->setQuery('UPDATE CustomFieldData SET moduleId = ? WHERE id = ? LIMIT 1');
                    $queryData->setParams([$this->moduleMapper($item->getModuleId()), $item->getId()]);

                    $this->db->doQuery($queryData);

                    $this->eventDispatcher->notifyEvent(
                        'upgrade.customField.process',
                        new Event($this, EventMessage::factory()
                            ->addDescription(__u('Field updated')))
                    );
                }
            });
        } catch (Exception $e) {
            processException($e);

            $this->eventDispatcher->notifyEvent('exception', new Event($e));

            throw $e;
        }

        $this->eventDispatcher->notifyEvent(
            'upgrade.customField.end',
            new Event($this, EventMessage::factory()
                ->addDescription(__u('Custom fields update'))
                ->addDescription(__FUNCTION__))
        );
    }

    private function moduleMapper(int $moduleId): int
    {
        return match ($moduleId) {
            10 => ActionsInterface::ACCOUNT,
            61 => ActionsInterface::CATEGORY,
            62 => ActionsInterface::CLIENT,
            71 => ActionsInterface::USER,
            72 => ActionsInterface::GROUP,
            default => $moduleId,
        };
    }

    protected function initialize(): void
    {
        $this->db = $this->dic->get(Database::class);
    }
}
