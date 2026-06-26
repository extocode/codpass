<?php
/*
 * sysPass
 *
 * @author nuxsmin
 * @link https://syspass.org
 * @copyright 2012-2022, Rubén Domínguez nuxsmin@$syspass.org
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
 * along with sysPass.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @var ThemeIcons         $icons
 * @var callable           $_getvar
 * @var ConfigData         $configData
 * @var AccountSearchItem  $account
 * @var DataGridTab        $data
 * @var DataGridActionBase $actionMenu
 * @var DataGridActionBase $action
 * @var AccountSearchItem  $accountSearchItem
 */

use SP\Config\ConfigData;
use SP\Core\Acl\ActionsInterface;
use SP\Core\UI\ThemeIcons;
use SP\Html\DataGrid\Action\DataGridActionBase;
use SP\Html\DataGrid\DataGridTab;
use SP\Services\Account\AccountSearchItem;
use SP\Util\DateUtil;

if (!isset($data)) {
    $data = $_getvar('data');
}

$wikiFilter = $_getvar('wikiFilter');
$favoriteRouteOn = $_getvar('favoriteRouteOn');
$favoriteRouteOff = $_getvar('favoriteRouteOff');
?>

<div id="data-search">
    <?php foreach ($data->getData()->getData() as $index => $accountSearchItem):
        if ($index === 'count'): continue; endif;
        $accountSearchData = $accountSearchItem->getAccountSearchVData();
        ?>

        <div class="account-label round shadow btn-action"
             data-action-route="<?php echo $_getvar('viewAccountRoute'); ?>"
             data-item-id="<?php echo $accountSearchData->getId(); ?>"
             data-onclick="account/view">
            <div class="label-left">
                <div class="label-row-up">
                    <div class="field-customer label-field">
                        <div class="field-name-hidden"><?php echo __('Client'); ?></div>
                        <?php
                        $clientColor = $accountSearchItem->getColor();
                        $chipTextColor = '#fff';
                        if ($clientColor !== '') {
                            $hex = ltrim($clientColor, '#');
                            if (strlen($hex) === 3) {
                                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
                            }
                            if (strlen($hex) === 6 && ctype_xdigit($hex)) {
                                $r = hexdec(substr($hex, 0, 2)) / 255;
                                $g = hexdec(substr($hex, 2, 2)) / 255;
                                $b = hexdec(substr($hex, 4, 2)) / 255;
                                $lum = 0.299 * $r + 0.587 * $g + 0.114 * $b;
                                if ($lum > 0.6) {
                                    $chipTextColor = '#1a1a1a';
                                }
                            }
                        }
                        $chipStyle = $clientColor !== ''
                            ? sprintf('background-color: %s !important; color: %s !important;', htmlspecialchars($clientColor, ENT_QUOTES), $chipTextColor)
                            : '';
                        $contactStyle = $clientColor !== ''
                            ? sprintf('background-color: %s; color: %s !important;', htmlspecialchars($clientColor, ENT_QUOTES), $chipTextColor)
                            : '';
                        ?>
                        <?php if (null !== $accountSearchItem->getClientLink()): ?>
                            <a href="<?php echo $accountSearchItem->getClientLink(); ?>"
                               target="blank"
                               title="<?php echo __('Search in Wiki'); ?>">
                                <span class="mdl-chip mdl-chip--contact client-chip" style="<?php echo $chipStyle; ?>">
                                    <span style="<?php echo $contactStyle; ?>"
                                          class="mdl-chip__contact">
                                        <?php echo htmlspecialchars(mb_ucfirst($accountSearchData->getClientName()), ENT_QUOTES); ?>
                                    </span>
                                    <span class="mdl-chip__text" style="color: <?php echo $chipTextColor; ?> !important;"
                                          title="<?php echo htmlspecialchars($accountSearchData->getClientName(), ENT_QUOTES); ?>">
                                        <?php echo htmlspecialchars($accountSearchItem->getShortClientName(), ENT_QUOTES); ?>
                                    </span>
                                </span>
                            </a>
                        <?php else: ?>
                            <span class="mdl-chip mdl-chip--contact client-chip" style="<?php echo $chipStyle; ?>">
                                <span style="<?php echo $contactStyle; ?>"
                                      class="mdl-chip__contact">
                                    <?php echo htmlspecialchars(mb_ucfirst($accountSearchData->getClientName()), ENT_QUOTES); ?>
                                </span>
                                <span class="mdl-chip__text" style="color: <?php echo $chipTextColor; ?> !important;"
                                      title="<?php echo htmlspecialchars($accountSearchData->getClientName(), ENT_QUOTES); ?>">
                                    <?php echo htmlspecialchars($accountSearchItem->getShortClientName(), ENT_QUOTES); ?>
                                </span>
                            </span>
                        <?php endif; ?>
                        <?php
                        $tags = $accountSearchItem->getTags();
                        if (!empty($tags)):
                        ?>
                            <div class="client-tags">
                                <?php foreach ($tags as $tag): ?>
                                    <span class="tag client-tag" data-tag-id="<?php echo $tag->id; ?>" title="<?php echo htmlspecialchars($tag->name, ENT_QUOTES); ?>"><?php echo htmlspecialchars($tag->name, ENT_QUOTES); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="field-account field-text label-field">
                        <div class="field-name"><?php echo __('Name'); ?></div>
                        <div class="field-text name-copy-row" style="display:flex !important;align-items:center;gap:4px;width:100%;min-width:0;overflow:hidden;">
                            <?php if ($accountSearchData->getName() !== ''): ?>
                                <i class="material-icons clip-copy"
                                   data-clip-value="<?php echo htmlspecialchars($accountSearchData->getName(), ENT_QUOTES); ?>"
                                   title="<?php echo __('Copy name'); ?>"
                                   style="flex:0 0 auto;font-size:16px;cursor:pointer;opacity:.45;">content_copy</i>
                            <?php endif; ?>
                            <?php if ($accountSearchItem->isShow()
                                && $accountSearchItem::$accountLink
                                && $accountSearchItem->getLink()): ?>
                                <a title="<?php echo __('Account Details'); ?>"
                                   class="btn-action"
                                   data-action-route="<?php echo $_getvar('viewAccountRoute'); ?>"
                                   data-item-id="<?php echo $accountSearchData->getId(); ?>"
                                   data-onclick="account/view"
                                   style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;min-width:0;"><?php echo htmlspecialchars($accountSearchData->getName(), ENT_QUOTES); ?></a>
                            <?php else: ?>
                                <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;min-width:0;"><?php echo htmlspecialchars($accountSearchData->getName(), ENT_QUOTES); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="field-category field-text label-field">
                        <div class="field-name"><?php echo __('Category'); ?></div>
                        <div class="field-text">
                            <?php echo htmlspecialchars($accountSearchData->getCategoryName(), ENT_QUOTES); ?>
                        </div>
                    </div>

                    <?php if ($accountSearchItem->isShow()): ?>
                        <div class="field-user field-text label-field">
                            <div class="field-name"><?php echo __('User'); ?></div>
                            <div class="field-text">
                                <?php echo htmlspecialchars($accountSearchItem->getShortLogin(), ENT_QUOTES); ?>
                            </div>
                        </div>

                        <div class="field-url field-text label-field">
                            <div class="field-name"><?php echo __('URL / IP'); ?></div>
                            <?php
                            $urlMaxLen = 35;
                            $rawUrl = $accountSearchItem->getShortUrl();
                            $displayUrl = mb_strlen($rawUrl) > $urlMaxLen
                                ? mb_substr($rawUrl, 0, $urlMaxLen) . '…'
                                : $rawUrl;
                            ?>
                            <?php if ($accountSearchItem->isUrlIslink()): ?>
                                <a href="<?php echo $accountSearchItem->getSafeUrl(); ?>"
                                   target="_blank"
                                   title="<?php printf(
                                       __('Open link to: %s'),
                                       htmlspecialchars($accountSearchData->getUrl(), ENT_QUOTES)
                                   ); ?>"><?php echo htmlspecialchars($displayUrl, ENT_QUOTES); ?></a>
                            <?php else: ?>
                                <div class="field-text"><?php echo htmlspecialchars($displayUrl, ENT_QUOTES); ?></div>
                            <?php endif; ?>
                        </div>

                    <?php else: ?>
                        <div class="field-user field-text label-field"></div>
                        <div class="field-url field-text label-field"></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="label-right">
                <div class="account-info">
                    <?php if ($accountSearchItem->isShow()): ?>
                        <?php if ($accountSearchItem->isPasswordExpired()): ?>
                            <i id="icon-expired-<?php echo $accountSearchData->getId(); ?>"
                               class="material-icons <?php echo $icons->getIconWarning()->getClass(); ?>">
                                <?php echo $icons->getIconWarning()->getIcon(); ?></i>
                            <span
                                    for="icon-expired-<?php echo $accountSearchData->getId(); ?>"
                                    class="mdl-tooltip mdl-tooltip--top">
                                <?php echo __('Password Expired'); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($accountSearchData->getIsPrivate() === 1): ?>
                            <i id="icon-private-<?php echo $accountSearchData->getId(); ?>"
                               class="material-icons">lock</i>
                            <span
                                    for="icon-private-<?php echo $accountSearchData->getId(); ?>"
                                    class="mdl-tooltip mdl-tooltip--top">
                                <?php echo __('Private Account'); ?>
                            </span>
                        <?php elseif ($accountSearchData->getIsPrivateGroup() === 1): ?>
                            <i id="icon-private-<?php echo $accountSearchData->getId(); ?>"
                               class="material-icons">lock_open</i>
                            <span
                                    for="icon-private-<?php echo $accountSearchData->getId(); ?>"
                                    class="mdl-tooltip mdl-tooltip--top">
                                <?php echo __('Private Account (Group)'); ?>
                            </span>
                        <?php else: ?>
                            <i id="accesses-<?php echo $accountSearchData->getId(); ?>"
                               class="material-icons btn-action"
                               data-action-route="<?php echo $_getvar('viewAccountRoute'); ?>"
                               data-item-id="<?php echo $accountSearchData->getId(); ?>"
                               data-onclick="account/view"
                               data-action-tab="permission-panel"
                               style="cursor: pointer;">face</i>

                            <div class="mdl-tooltip mdl-tooltip--top"
                                 for="accesses-<?php echo $accountSearchData->getId(); ?>">
                                <div class="tooltip-text">
                                    <p class="tooltip-header"><?php echo __('Permissions'); ?></p>
                                    <p>
                                        <?php echo implode('<br>', $accountSearchItem->getAccesses()); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($accountSearchItem->isFavorite()): ?>
                            <i id="btn-favorite-<?php echo $accountSearchData->getId(); ?>"
                               class="material-icons icon-favorite mdl-color-text--amber-A100"
                               data-action-route-on="<?php echo $favoriteRouteOn; ?>"
                               data-action-route-off="<?php echo $favoriteRouteOff; ?>"
                               data-item-id="<?php echo $accountSearchData->getId(); ?>"
                               data-status="<?php echo 'on'; ?>">star</i>
                            <span
                                    for="btn-favorite-<?php echo $accountSearchData->getId(); ?>"
                                    class="mdl-tooltip mdl-tooltip--top">
                                <?php echo __('Delete Favorite'); ?>
                            </span>
                        <?php else: ?>
                            <i id="btn-favorite-<?php echo $accountSearchData->getId(); ?>"
                               class="material-icons icon-favorite"
                               data-action-route-on="<?php echo $favoriteRouteOn; ?>"
                               data-action-route-off="<?php echo $favoriteRouteOff; ?>"
                               data-item-id="<?php echo $accountSearchData->getId(); ?>"
                               data-status="<?php echo 'off'; ?>">star_border</i>
                            <span
                                    for="btn-favorite-<?php echo $accountSearchData->getId(); ?>"
                                    class="mdl-tooltip mdl-tooltip--top">
                                <?php echo __('Mark as Favorite'); ?>
                            </span>
                        <?php endif; ?>

                        <i id="notes-<?php echo $accountSearchData->getId(); ?>"
                           class="material-icons <?php echo $accountSearchData->getNotes() === '' ? 'notes-empty' : ''; ?>">speaker_notes</i>
                        <div class="mdl-tooltip mdl-tooltip--top"
                             for="notes-<?php echo $accountSearchData->getId(); ?>">
                            <div class="tooltip-text">
                                <?php if ($accountSearchData->getNotes() !== ''): ?>
                                    <p class="tooltip-header"><?php echo __('Notes'); ?></p>
                                    <p><?php echo $accountSearchItem->getShortNotes(); ?></p>
                                <?php else: ?>
                                    <p><?php echo __('No notes'); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($accountSearchItem->getNumFiles() > 0): ?>
                            <i id="attachments-<?php echo $accountSearchData->getId(); ?>"
                               class="material-icons btn-action"
                               data-action-route="<?php echo $_getvar('viewAccountRoute'); ?>"
                               data-item-id="<?php echo $accountSearchData->getId(); ?>"
                               data-onclick="account/view"
                               data-action-tab="file-panel">attach_file</i>
                            <div class="mdl-tooltip mdl-tooltip--top"
                                 for="attachments-<?php echo $accountSearchData->getId(); ?>">
                                <div class="tooltip-text">
                                    <?php printf(
                                            '%s: %d',
                                        __('Attachments'),
                                        $accountSearchItem->getNumFiles()
                                    ); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (null !== $accountSearchItem->getPublicLink()): ?>
                            <i id="publiclink-<?php echo $accountSearchData->getId(); ?>"
                               class="material-icons"><?php echo $icons->getIconPublicLink()->getIcon(); ?></i>
                            <div class="mdl-tooltip mdl-tooltip--top"
                                 for="publiclink-<?php echo $accountSearchData->getId(); ?>">
                                <div class="tooltip-text">
                                    <p class="tooltip-header"><?php echo __('Public Link'); ?></p>
                                    <p>
                                        <?php printf(
                                                '%s: %s',
                                            __('Expiry Date'),
                                            DateUtil::getDateFromUnix($accountSearchData->getPublicLinkDateExpire())
                                        ); ?>
                                        <br>
                                        <?php printf(
                                                '%s: %s',
                                                __('Visits'),
                                                $accountSearchData->getPublicLinkTotalCountViews()
                                        ); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($wikiFilter
                            && $accountSearchItem->isWikiMatch($wikiFilter)): ?>
                            <?php if (AccountSearchItem::$dokuWikiEnabled): ?>
                                <a href="<?php echo $_getvar('wikiPageUrl'), urlencode(
                                    $accountSearchData->getName()
                                ); ?>"
                                   target="_blank">
                                    <i class="material-icons"
                                       title="<?php echo __('Link to Wiki'); ?>">library_books</i>
                                </a>
                                <i class="btn-action material-icons fg-green100"
                                   title="<?php echo __('View at Wiki'); ?>"
                                   data-action-route="<?php echo ActionsInterface::WIKI_VIEW; ?>"
                                   data-pagename="<?php echo htmlspecialchars(
                                       $accountSearchData->getName(),
                                       ENT_QUOTES
                                   ); ?>"
                                   data-onclick="wiki/show">library_books</i>
                            <?php else: ?>
                                <a href="<?php echo $_getvar('wikiPageUrl'), urlencode($accountSearchData->getName()); ?>"
                                   target="_blank">
                                    <i class="material-icons"
                                       title="<?php echo __('Link to Wiki'); ?>">library_books</i>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>


                <?php if ($accountSearchItem->isShow()
                    || $accountSearchItem->isShowRequest()): ?>
                    <div class="account-actions">
                        <?php foreach ($data->getDataActionsFiltered($accountSearchItem) as $action):
                            $actionUid = uniqid('btn-action-', true);
                            ?>
                            <i id="<?php echo $actionUid; ?>"
                               class="btn-action material-icons <?php echo $action->getClassesAsString(), ' ', $action->getIcon()->getClass(); ?>"
                               data-item-id="<?php echo $accountSearchData->getId(); ?>"
                               data-parent-id="<?php echo $accountSearchData->getParentId(); ?>"
                                <?php foreach ($action->getData() as $dataName => $dataValue):
                                    printf('data-%s="%s"', $dataName, $dataValue);
                                endforeach; ?>>
                                <?php echo $action->getIcon()->getIcon(); ?></i>
                            <span
                                    for="<?php echo $actionUid; ?>"
                                    class="mdl-tooltip mdl-tooltip--bottom"><?php echo $action->getTitle(); ?></span>
                        <?php endforeach; ?>

                        <?php if ($data->getDataActionsMenuCount()):
                            $actionsMenu = $data->getDataActionsMenuFiltered($accountSearchItem);
                            if (count($actionsMenu) > 0):
                                $actionMenuUid = uniqid('actions-menu-lower-right-', true);
                                ?>
                                <button id="<?php echo $actionMenuUid; ?>"
                                        class="mdl-button mdl-js-button mdl-button--icon">
                                    <i class="material-icons">more_vert</i>
                                </button>
                                <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect"
                                    for="<?php echo $actionMenuUid; ?>">

                                    <?php foreach ($actionsMenu as $actionMenu): ?>
                                        <li class="btn-action mdl-menu__item <?php echo $actionMenu->getClassesAsString(); ?>"
                                            data-item-id="<?php echo $accountSearchData->getId(); ?>"
                                            data-parent-id="<?php echo $accountSearchData->getParentId(); ?>"
                                            <?php foreach ($actionMenu->getData() as $dataName => $dataValue):
                                                printf('data-%s="%s"', $dataName, $dataValue);
                                            endforeach; ?>>
                                            <i class="material-icons <?php echo $actionMenu->getIcon()->getClass(); ?>"
                                               title="<?php echo $actionMenu->getTitle(); ?>">
                                                <?php echo $actionMenu->getIcon()->getIcon(); ?>
                                            </i>
                                            <?php echo $actionMenu->getName(); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>