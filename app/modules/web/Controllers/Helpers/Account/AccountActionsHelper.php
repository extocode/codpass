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

namespace SP\Modules\Web\Controllers\Helpers\Account;

use SP\Core\Acl\Acl;
use SP\Core\Acl\ActionsInterface;
use SP\Core\UI\ThemeIcons;
use SP\Html\DataGrid\Action\DataGridAction;
use SP\Html\DataGrid\Action\DataGridActionType;
use SP\Modules\Web\Controllers\Helpers\HelperBase;
use SP\Services\Account\AccountAcl;
use SP\Services\Account\AccountSearchItem;

/**
 * Class AccountIconsHelper
 *
 * @package SP\Modules\Web\Controllers\Helpers
 */
final class AccountActionsHelper extends HelperBase
{
    /**
     * @var ThemeIcons
     */
    protected $icons;

    /**
     * @var string
     */
    protected $sk;

    public function getViewAction(): \SP\Html\DataGrid\Action\DataGridAction
    {
        $action = new DataGridAction();
        $action->setId(ActionsInterface::ACCOUNT_VIEW);
        $action->setType(DataGridActionType::VIEW_ITEM);
        $action->setName(__('Account Details'));
        $action->setTitle(__('Account Details'));
        $action->addClass('btn-action');
        $action->setIcon($this->icons->getIconView());
        $action->setRuntimeFilter(AccountSearchItem::class, 'isShowView');
        $action->addData('action-route', Acl::getActionRoute(ActionsInterface::ACCOUNT_VIEW));
        $action->addData('action-sk', $this->sk);
        $action->addData('onclick', Acl::getActionRoute(ActionsInterface::ACCOUNT_VIEW));
        $action->addAttribute('type', 'button');

        return $action;
    }

    /**
     * Set icons for view
     *
     *
     * @return DataGridAction[]
     */
    public function getActionsForAccount(AccountAcl $accountAcl, AccountActionsDto $accountActionsDto): array
    {
        $actions = [];

        // Only show back action for history views (to return to current version)
        if ($accountActionsDto->isHistory()) {
            $actionBack = $this->getBackAction();
            $actionBack->addData('item-id', $accountActionsDto->getAccountId());
            $actionBack->setName(__('View Current'));
            $actionBack->setTitle(__('View Current'));
            $actions[] = $actionBack;
        }

        if ($accountAcl->isShowEditPass()) {
            $actions[] = $this->getEditPassAction()->addData('item-id', $accountActionsDto->getAccountId());
        }

        if ($accountAcl->isShowEdit()) {
            $actions[] = $this->getEditAction()->addData('item-id', $accountActionsDto->getAccountId());
        }

        if ($accountAcl->getActionId() === ActionsInterface::ACCOUNT_VIEW
            && !$accountAcl->isShowEdit()
            && $this->configData->isMailRequestsEnabled()
        ) {
            $actions[] = $this->getRequestAction()->addData('item-id', $accountActionsDto->getAccountId());
        }

        if ($accountAcl->isShowRestore()) {
            $actionRestore = $this->getRestoreAction();
            $actionRestore->addData('item-id', $accountActionsDto->getAccountId());
            $actionRestore->addData('history-id', $accountActionsDto->getAccountHistoryId());

            $actions[] = $actionRestore;
        }

        if ($accountAcl->isShowSave()) {
            $actions[] = $this->getSaveAction()->addAttribute('form', 'frmAccount');
        }

        return $actions;
    }

    public function getBackAction(): \SP\Html\DataGrid\Action\DataGridAction
    {
        $action = new DataGridAction();
        $action->setId('btnBack');
        $action->setName(__('Back'));
        $action->setTitle(__('Back'));
        $action->addClass('btn-action');
        $action->setIcon($this->icons->getIconBack());
        $action->addData('action-route', Acl::getActionRoute(ActionsInterface::ACCOUNT_VIEW));
        $action->addData('action-sk', $this->sk);
        $action->addData('onclick', Acl::getActionRoute(ActionsInterface::ACCOUNT_VIEW));
        $action->addAttribute('type', 'button');

        return $action;
    }

    public function getEditPassAction(): \SP\Html\DataGrid\Action\DataGridAction
    {
        $action = new DataGridAction();
        $action->setId(ActionsInterface::ACCOUNT_EDIT_PASS);
        $action->setType(DataGridActionType::VIEW_ITEM);
        $action->setName(__('Edit Account Password'));
        $action->setTitle(__('Edit Account Password'));
        $action->addClass('btn-action');
        $action->setIcon($this->icons->getIconEditPass());
        $action->setRuntimeFilter(AccountSearchItem::class, 'isShowViewPass');
        $action->addData('action-route', Acl::getActionRoute(ActionsInterface::ACCOUNT_EDIT_PASS));
        $action->addData('action-sk', $this->sk);
        $action->addData('onclick', Acl::getActionRoute(ActionsInterface::ACCOUNT_EDIT_PASS));
        $action->addAttribute('type', 'button');

        return $action;
    }

    public function getEditAction(): \SP\Html\DataGrid\Action\DataGridAction
    {
        $action = new DataGridAction();
        $action->setId(ActionsInterface::ACCOUNT_EDIT);
        $action->setType(DataGridActionType::EDIT_ITEM);
        $action->setName(__('Edit Account'));
        $action->setTitle(__('Edit Account'));
        $action->addClass('btn-action');
        $action->setIcon($this->icons->getIconEdit());
        $action->setRuntimeFilter(AccountSearchItem::class, 'isShowEdit');
        $action->addData('action-route', Acl::getActionRoute(ActionsInterface::ACCOUNT_EDIT));
        $action->addData('action-sk', $this->sk);
        $action->addData('onclick', Acl::getActionRoute(ActionsInterface::ACCOUNT_EDIT));
        $action->addAttribute('type', 'button');

        return $action;
    }

    public function getRequestAction(): \SP\Html\DataGrid\Action\DataGridAction
    {
        $action = new DataGridAction();
        $action->setId(ActionsInterface::ACCOUNT_REQUEST);
        $action->setName(__('Request Modification'));
        $action->setTitle(__('Request Modification'));
        $action->addClass('btn-action');
        $action->setIcon($this->icons->getIconEmail());
        $action->setRuntimeFilter(AccountSearchItem::class, 'isShowRequest');
        $action->addData('action-route', Acl::getActionRoute(ActionsInterface::ACCOUNT_REQUEST));
        $action->addData('action-sk', $this->sk);
        $action->addData('onclick', Acl::getActionRoute(ActionsInterface::ACCOUNT_VIEW));
        $action->addAttribute('type', 'submit');

        return $action;
    }

    public function getRestoreAction(): \SP\Html\DataGrid\Action\DataGridAction
    {
        $action = new DataGridAction();
        $action->setId(ActionsInterface::ACCOUNT_EDIT_RESTORE);
        $action->setType(DataGridActionType::VIEW_ITEM);
        $action->setName(__('Restore account from this point'));
        $action->setTitle(__('Restore account from this point'));
        $action->addClass('btn-action');
        $action->setIcon($this->icons->getIconRestore());
        $action->addData('action-route', 'account/saveEditRestore');
        $action->addData('action-sk', $this->sk);
        $action->addData('onclick', 'account/saveEditRestore');
        $action->addAttribute('type', 'button');

        return $action;
    }

    public function getSaveAction(): \SP\Html\DataGrid\Action\DataGridAction
    {
        $action = new DataGridAction();
        $action->setId(ActionsInterface::ACCOUNT);
        $action->setType(DataGridActionType::VIEW_ITEM);
        $action->setName(__('Save'));
        $action->setTitle(__('Save'));
        $action->addClass('btn-action');
        $action->setIcon($this->icons->getIconSave());
        $action->addData('action-route', 'account/save');
        $action->addData('action-sk', $this->sk);
        $action->addData('onclick', 'account/save');
        $action->addAttribute('type', 'submit');

        return $action;
    }

    /**
     * Set icons for view
     *
     *
     * @return DataGridAction[]
     */
    public function getActionsGrouppedForAccount(AccountAcl $accountAcl, AccountActionsDto $accountActionsDto): array
    {
        $userData = $this->context->getUserData();

        $actions = [];

        if ($accountAcl->isShowDelete()) {
            $actions[] = $this->getDeleteAction()->addData('item-id', $accountActionsDto->getAccountId());
        }

        if ($accountActionsDto->isHistory() === false
            && $accountActionsDto->isLinked() === false
            && $this->configData->isPublinksEnabled()
            && $accountAcl->isShowLink()
            && $accountAcl->isShowViewPass()
        ) {
            $itemId = $accountActionsDto->getPublicLinkId();

            if ($itemId) {
                $actionRefresh = $this->getPublicLinkRefreshAction();
                $actionRefresh->addData('item-id', $itemId);
                $actionRefresh->addData('account-id', $accountActionsDto->getAccountId());

                $actions[] = $actionRefresh;

                if ($userData->getIsAdminApp()
                    || $userData->getId() === $accountActionsDto->getPublicLinkCreatorId()
                ) {
                    $actionDelete = $this->getPublicLinkDeleteAction();
                    $actionDelete->addData('item-id', $itemId);
                    $actionDelete->addData('account-id', $accountActionsDto->getAccountId());

                    $actions[] = $actionDelete;
                }
            } else {
                $action = $this->getPublicLinkAction();
                $action->addData('account-id', $accountActionsDto->getAccountId());

                $actions[] = $action;
            }
        }

        if ($accountAcl->isShowViewPass()) {
            if ($accountActionsDto->isHistory()) {
                $actionViewPass = $this->getViewPassHistoryAction()
                    ->addData('item-id', $accountActionsDto->getAccountHistoryId());
                $actionCopy = $this->getCopyPassHistoryAction()
                    ->addData('item-id', $accountActionsDto->getAccountHistoryId());
            } else {
                $actionViewPass = $this->getViewPassAction()
                    ->addData('item-id', $accountActionsDto->getAccountId());
                $actionCopy = $this->getCopyPassAction()
                    ->addData('item-id', $accountActionsDto->getAccountId());
            }

            $actionViewPass->addData('parent-id', $accountActionsDto->getAccountParentId());
            $actionCopy->addData('parent-id', $accountActionsDto->getAccountParentId());

            $actions[] = $actionViewPass;
            $actions[] = $actionCopy;
        }

        if ($accountAcl->isShowCopy()) {
            $actions[] = $this->getCopyAction()->addData('item-id', $accountActionsDto->getAccountId());
        }

        return $actions;
    }

    public function getDeleteAction(): \SP\Html\DataGrid\Action\DataGridAction
    {
        $action = new DataGridAction();
        $action->setId(ActionsInterface::ACCOUNT_DELETE);
        $action->setType(DataGridActionType::DELETE_ITEM);
        $action->setName(__('Remove Account'));
        $action->setTitle(__('Remove Account'));
        $action->addClass('btn-action');
        $action->setIcon($this->icons->getIconDelete());
        $action->setRuntimeFilter(AccountSearchItem::class, 'isShowDelete');
        $action->addData('action-route', Acl::getActionRoute(ActionsInterface::ACCOUNT_DELETE));
        $action->addData('action-sk', $this->sk);
        $action->addData('onclick', Acl::getActionRoute(ActionsInterface::ACCOUNT_DELETE));
        $action->addAttribute('type', 'button');

        return $action;
    }

    public function getPublicLinkRefreshAction(): \SP\Html\DataGrid\Action\DataGridAction
    {
        $action = new DataGridAction();
        $action->setId(ActionsInterface::PUBLICLINK_REFRESH);
        $action->setName(__('Update Public Link'));
        $action->setTitle(__('Update Public Link'));
        $action->addClass('btn-action');
        $action->setIcon($this->icons->getIconPublicLink());
        $action->addData('action-route', Acl::getActionRoute(ActionsInterface::PUBLICLINK_REFRESH));
        $action->addData('action-sk', $this->sk);
        $action->addData('onclick', 'link/refresh');
        $action->addData('action-next', Acl::getActionRoute(ActionsInterface::ACCOUNT_VIEW));
        $action->addAttribute('type', 'button');

        return $action;
    }

    public function getPublicLinkDeleteAction(): \SP\Html\DataGrid\Action\DataGridAction
    {
        $icon = clone $this->icons->getIconPublicLink();
        $icon->setIcon('link_off');

        $action = new DataGridAction();
        $action->setId(ActionsInterface::PUBLICLINK_DELETE);
        $action->setName(__('Delete Public Link'));
        $action->setTitle(__('Delete Public Link'));
        $action->addClass('btn-action');
        $action->setIcon($icon);
        $action->addData('action-route', Acl::getActionRoute(ActionsInterface::PUBLICLINK_DELETE));
        $action->addData('action-sk', $this->sk);
        $action->addData('onclick', 'link/delete');
        $action->addData('action-next', Acl::getActionRoute(ActionsInterface::ACCOUNT_VIEW));
        $action->addAttribute('type', 'button');

        return $action;
    }

    public function getPublicLinkAction(): \SP\Html\DataGrid\Action\DataGridAction
    {
        $action = new DataGridAction();
        $action->setId(ActionsInterface::PUBLICLINK_CREATE);
        $action->setName(__('Create Public Link'));
        $action->setTitle(__('Create Public Link'));
        $action->addClass('btn-action');
        $action->setIcon($this->icons->getIconPublicLink());
        $action->addData('action-route', 'publicLink/saveCreateFromAccount');
        $action->addData('action-sk', $this->sk);
        $action->addData('onclick', 'link/save');
        $action->addData('action-next', Acl::getActionRoute(ActionsInterface::ACCOUNT_VIEW));
        $action->addAttribute('type', 'button');

        return $action;
    }

    public function getViewPassHistoryAction(): \SP\Html\DataGrid\Action\DataGridAction
    {
        $action = new DataGridAction();
        $action->setId(ActionsInterface::ACCOUNT_VIEW_PASS);
        $action->setType(DataGridActionType::VIEW_ITEM);
        $action->setName(__('View password'));
        $action->setTitle(__('View password'));
        $action->addClass('btn-action');
        $action->setIcon($this->icons->getIconViewPass());
        $action->setRuntimeFilter(AccountSearchItem::class, 'isShowViewPass');
        $action->addData('action-route', Acl::getActionRoute(ActionsInterface::ACCOUNT_HISTORY_VIEW_PASS));
        $action->addData('action-full', 1);
        $action->addData('action-sk', $this->sk);
        $action->addData('onclick', Acl::getActionRoute(ActionsInterface::ACCOUNT_HISTORY_VIEW_PASS));
        $action->addAttribute('type', 'button');

        return $action;
    }

    public function getCopyPassHistoryAction(): \SP\Html\DataGrid\Action\DataGridAction
    {
        $action = new DataGridAction();
        $action->setId(ActionsInterface::ACCOUNT_VIEW_PASS);
        $action->setType(DataGridActionType::VIEW_ITEM);
        $action->setName(__('Copy Password to Clipboard'));
        $action->setTitle(__('Copy Password to Clipboard'));
        $action->addClass('btn-action');
        $action->addClass('clip-pass-button');
        $action->setIcon($this->icons->getIconClipboard());
        $action->setRuntimeFilter(AccountSearchItem::class, 'isShowCopyPass');
        $action->addData('action-route', Acl::getActionRoute(ActionsInterface::ACCOUNT_HISTORY_COPY_PASS));
        $action->addData('action-full', 0);
        $action->addData('action-sk', $this->sk);
        $action->addData('useclipboard', '1');
        $action->addAttribute('type', 'button');

        return $action;
    }

    public function getViewPassAction(): \SP\Html\DataGrid\Action\DataGridAction
    {
        $action = new DataGridAction();
        $action->setId(ActionsInterface::ACCOUNT_VIEW_PASS);
        $action->setType(DataGridActionType::VIEW_ITEM);
        $action->setName(__('View password'));
        $action->setTitle(__('View password'));
        $action->addClass('btn-action');
        $action->setIcon($this->icons->getIconViewPass());
        $action->setRuntimeFilter(AccountSearchItem::class, 'isShowViewPass');
        $action->addData('action-route', Acl::getActionRoute(ActionsInterface::ACCOUNT_VIEW_PASS));
        $action->addData('action-full', 1);
        $action->addData('action-sk', $this->sk);
        $action->addData('onclick', Acl::getActionRoute(ActionsInterface::ACCOUNT_VIEW_PASS));
        $action->addAttribute('type', 'button');

        return $action;
    }

    public function getCopyPassAction(): \SP\Html\DataGrid\Action\DataGridAction
    {
        $action = new DataGridAction();
        $action->setId(ActionsInterface::ACCOUNT_VIEW_PASS);
        $action->setType(DataGridActionType::VIEW_ITEM);
        $action->setName(__('Copy Password to Clipboard'));
        $action->setTitle(__('Copy Password to Clipboard'));
        $action->addClass('btn-action');
        $action->addClass('clip-pass-button');
        $action->setIcon($this->icons->getIconClipboard());
        $action->setRuntimeFilter(AccountSearchItem::class, 'isShowCopyPass');
        $action->addData('action-route', Acl::getActionRoute(ActionsInterface::ACCOUNT_COPY_PASS));
        $action->addData('action-full', 0);
        $action->addData('action-sk', $this->sk);
        $action->addData('useclipboard', '1');
        $action->addAttribute('type', 'button');

        return $action;
    }

    public function getCopyAction(): \SP\Html\DataGrid\Action\DataGridAction
    {
        $action = new DataGridAction();
        $action->setId(ActionsInterface::ACCOUNT_COPY);
        $action->setType(DataGridActionType::MENUBAR_ITEM);
        $action->setName(__('Copy Account'));
        $action->setTitle(__('Copy Account'));
        $action->addClass('btn-action');
        $action->setIcon($this->icons->getIconCopy());
        $action->setRuntimeFilter(AccountSearchItem::class, 'isShowCopy');
        $action->addData('action-route', Acl::getActionRoute(ActionsInterface::ACCOUNT_COPY));
        $action->addData('action-sk', $this->sk);
        $action->addData('onclick', Acl::getActionRoute(ActionsInterface::ACCOUNT_COPY));
        $action->addAttribute('type', 'button');

        return $action;
    }

    /**
     * Initialize class
     */
    protected function initialize(): void
    {
        $this->sk = $this->view->get('sk');
        $this->icons = $this->view->getTheme()->getIcons();
    }
}
