<?php
/**
 * @var callable $_getvar
 * @var Template $this
 */

use SP\Mvc\View\Template;

?>
<button class="btn-back">
    <i class="material-icons">arrow_back</i>
    <span><?php echo __('Back'); ?></span>
</button>

<div id="actions" align="center">
    <?php include $this->includePartial('error-list'); ?>

    <div class="error-actions" style="margin-top: 20px;">
        <button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored btn-request-access"
                id="btn-request-permission"
                title="<?php echo __('Request Access'); ?>">
            <i class="material-icons">mail_outline</i>
            <?php echo __('Request Access'); ?>
        </button>
    </div>

    <script>
    document.getElementById('btn-request-permission').addEventListener('click', function() {
        sysPassApp.msg.ok('<?php echo __('Please contact the administrator to request access to this resource.'); ?>');
    });
    </script>
</div>
