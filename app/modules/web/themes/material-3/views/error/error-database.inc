<?php
/**
 * @var ThemeIcons $icons
 * @var Template   $this
 */

use SP\Core\UI\ThemeIcons;
use SP\Mvc\View\Template;

?>
<div id="actions" align="center">
    <?php include $this->includePartial('error-list'); ?>

    <div class="buttons">
        <button id="btnBack" class="btn-back">
            <i class="material-icons">arrow_back</i>
            <span><?php echo __('Back'); ?></span>
        </button>
        <a class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored"
           href="index.php?r=install">
            <?php echo __('Install'); ?>
            <i class="material-icons"
               title="<?php echo __('Install'); ?>"><?php echo $icons->getIconPlay()->getIcon(); ?></i>
        </a>
    </div>
</div>