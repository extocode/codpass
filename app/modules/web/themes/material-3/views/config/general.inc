<?php
/**
 * @var ThemeIcons $icons
 * @var ConfigData $configData
 * @var callable   $_getvar
 * @var Template   $this
 */

use SP\Config\ConfigData;
use SP\Core\UI\ThemeIcons;
use SP\Mvc\View\Template;

?>
<form method="post" name="frmConfig" id="frmConfig" class="form-action"
      data-onsubmit="config/save"
      data-action-route="configGeneral/save"
      data-hash="">

    <?php include $this->includeTemplate('general-site'); ?>
    <?php include $this->includeTemplate('general-events'); ?>
    <?php include $this->includeTemplate('general-proxy'); ?>
    <?php include $this->includeTemplate('general-auth'); ?>

    <input type="hidden" name="sk" value="<?php echo $sk; ?>">
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
            <button form="frmConfig"
                    class="mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--colored <?php echo $icons->getIconSave()->getClassButton(); ?>"
                    title="<?php echo $icons->getIconSave()->getTitle(); ?>">
                <i class="material-icons"><?php echo $icons->getIconSave()->getIcon(); ?></i>
            </button>
        </li>
    </ul>
</div>
