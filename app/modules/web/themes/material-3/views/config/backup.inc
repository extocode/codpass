<?php
/**
 * @var ThemeIcons $icons
 * @var ConfigData $configData
 * @var callable   $_getvar
 * @var callable   $_getRoute
 * @var Template   $this
 */

use SP\Config\ConfigData;
use SP\Core\UI\ThemeIcons;
use SP\Mvc\View\Template;

?>

<?php if ($_getvar('pharIsAvailable') !== true): ?>
    <div class="msg-option-unvailable">
        <?php printf(__('The \'%s\' extension is unavailable'), 'phar'); ?>
        <i id="help-phar"
           class="material-icons"><?php echo $icons->getIconHelp()->getIcon(); ?></i>
        <div class="mdl-tooltip mdl-tooltip--large" for="help-phar">
            <p>
                <?php echo __('This extension is needed to build the application\'s backup files'); ?>
            </p>
        </div>
    </div>
<?php endif; ?>

<div id="title" class="titleNormal">
    <i class="material-icons">backup</i>
    <?php echo __('Backup'); ?>
</div>

<table class="data">
    <tr>
        <td class="descField">
            <?php echo __('Result'); ?>
        </td>
        <td class="valField">
            <div class="lowres-title"><?php echo __('Result'); ?></div>

            <?php echo $_getvar('lastBackupTime'); ?>
        </td>
    </tr>
    <?php if ($_getvar('ctx_userIsAdminApp')): ?>
        <tr>
            <td class="descField">
                <?php echo __('Download Current'); ?>
            </td>
            <td class="valField">
                <div class="lowres-title"><?php echo __('Download Current'); ?></div>

                <?php if ($_getvar('hasBackup')): ?>
                    <a href="<?php echo $_getRoute('configBackup/downloadBackupDb'); ?>"
                       class="download btn-download" target="_blank">
                        <button type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored btn-download-action">
                            <i class="material-icons">cloud_download</i>
                            <?php echo __('DB Backup'); ?>
                        </button>
                    </a>

                    <a href="<?php echo $_getRoute('configBackup/downloadBackupApp'); ?>"
                       class="download btn-download" target="_blank">
                        <button type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored btn-download-action">
                            <i class="material-icons">cloud_download</i>
                            <?php echo __('codPass Backup'); ?>
                        </button>
                    </a>
                <?php else: echo __('There aren\'t any backups available'); ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endif; ?>
</table>

<div class="tab-actions">
    <ul>
        <li>
            <button id="help_backup_button" type="button"
                    class="btn-help mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--colored"
                    title="<?php echo $icons->getIconHelp()->getTitle(); ?>"
                    data-help="help-backup">
                <i class="material-icons"><?php echo $icons->getIconHelp()->getIcon(); ?></i>
            </button>

            <div for="help-backup" class="help-box"
                 title="<?php echo __('Backup') ?>">
                <div class="help-container">
                    <p class="help-text"><?php echo __('The backup allows you to save and download codPass database and application files including its configuration.'); ?></p>

                    <p class="help-text"><?php echo __('With this method it\'s possible to either save all codPass data in another place or use it to make the application portable.'); ?></p>
                </div>
            </div>
        </li>
        <li>

            <button type="button"
                    class="btn-action mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--colored <?php echo $icons->getIconPlay()->getClassButton(); ?>"
                    data-onclick="config/backup"
                    data-action-route="configBackup/fileBackup"
                    title="<?php echo $icons->getIconPlay()->getTitle(); ?>">
                <i class="material-icons"><?php echo $icons->getIconPlay()->getIcon(); ?></i>
            </button>
        </li>
    </ul>
</div>

<div id="title" class="titleNormal">
    <i class="material-icons">import_export</i>
    <?php echo __('Export Accounts'); ?>
</div>

<form method="post" name="frmExport" id="frmExport" class="form-action"
      data-onsubmit="config/export"
      data-action-route="configBackup/xmlExport">
    <table class="data">
        <tr>
            <td class="descField">
                <?php echo __('Result'); ?>
            </td>
            <td class="valField">
                <div class="lowres-title"><?php echo __('Result'); ?></div>

                <?php echo $_getvar('lastExportTime'); ?>
            </td>
        </tr>
        <?php if ($_getvar('ctx_userIsAdminApp') || $_getvar('isDemo')): ?>
            <tr>
                <td class="descField">
                    <?php echo __('Download Current'); ?>
                </td>
                <td class="valField">
                    <div class="lowres-title"><?php echo __('Download Current'); ?></div>

                    <?php if ($_getvar('hasExport')): ?>
                        <a href="<?php echo $_getRoute('configBackup/downloadExport'); ?>"
                           class="download btn-download" target="_blank">
                            <button type="button"
                                    class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored btn-download-action">
                                <i class="material-icons">cloud_download</i>
                                XML <?php echo $_getvar('siteName'); ?>
                            </button>
                        </a>
                    <?php else: echo __('No export file found'); ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <td class="descField">
                <?php echo __('Export Password'); ?>
            </td>
            <td class="valField">
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <input id="export-password" name="exportPwd" type="password"
                           class="mdl-textfield__input mdl-color-text--indigo-400 passwordfield__input"
                           maxlength="255"/>
                    <label class="mdl-textfield__label"
                           for="exportPwd"><?php echo __('Export Password'); ?></label>
                </div>
            </td>
        </tr>
        <tr>
            <td class="descField">
                <?php echo __('Export Password (repeat)'); ?>
            </td>
            <td class="valField">
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <input id="export-password_repeat" name="exportPwdR"
                           type="password"
                           class="mdl-textfield__input mdl-color-text--indigo-400"
                           maxlength="255"/>
                    <label class="mdl-textfield__label"
                           for="exportPwdR"><?php echo __('Export Password (repeat)'); ?></label>
                </div>
            </td>
        </tr>
    </table>
</form>

<div class="tab-actions">
    <ul>
        <li>
            <button id="btnBack" type="button"
                    class="btn-back-disabled" style="display:none"
                    title="<?php echo __('Back'); ?>">
                <i class="material-icons">arrow_back</i>
                <span><?php echo __('Back'); ?></span>
            </button>
        </li>
        <li>
            <button id="help_export_button" type="button"
                    class="btn-help mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--colored"
                    title="<?php echo $icons->getIconHelp()->getTitle(); ?>"
                    data-help="help-export">
                <i class="material-icons"><?php echo $icons->getIconHelp()->getIcon(); ?></i>
            </button>

            <div for="help-export" class="help-box"
                 title="<?php echo __('Export Accounts'); ?>">
                <div class="help-container">
                    <p class="help-text"><?php echo __('The accounts export allows you to save the accounts and their data in XML format to be imported in another codPass instance.'); ?></p>

                    <p class="help-text"><?php echo __('Exported items are accounts, clients, categories and tags.'); ?></p>

                    <p class="help-text"><?php echo __('Accounts\' password are exported in an encrypted way for better security.'); ?></p>

                    <p class="help-text"><?php echo __('It\'s possible to set an export password to save all the encrypted data in the XML file.'); ?></p>
                </div>
            </div>
        </li>
        <li>
            <button form="frmExport"
                    class="mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--colored <?php echo $icons->getIconPlay()->getClassButton(); ?>"
                    title="<?php echo $icons->getIconPlay()->getTitle(); ?>">
                <i class="material-icons"><?php echo $icons->getIconPlay()->getIcon(); ?></i>
            </button>
        </li>
    </ul>
</div>
