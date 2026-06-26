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
    </div>
</div>