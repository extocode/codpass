<?php
/**
 * @var callable $_getvar
 * @var Template $this
 */

use SP\Html\Assets\IconInterface;
use SP\Mvc\View\Template;

?>
<footer>
    <div id="footer-left" class="footer-parts">
        <?php if ($_getvar('loadApp') === true && $_getvar('ctx_userName')): ?>
            <div id="session">
                <span id="user-info">
                    <?php
                    /** @var IconInterface $ctx_userType */
                    $ctx_userType = $_getvar('ctx_userType');

                    if ($ctx_userType): ?>
                        <i id="user-type-footer"
                           class="material-icons"><?php echo $ctx_userType->getIcon(); ?></i>
                        <span for="user-type-footer"
                              class="mdl-tooltip mdl-tooltip--top"><?php echo $ctx_userType->getTitle(); ?></span>
                    <?php else: ?>
                        <i class="material-icons">face</i>
                    <?php endif; ?>
                    <span id="user-name-footer"><?php echo $_getvar('ctx_userName'); ?></span>
                    <span for="user-name-footer"
                          class="mdl-tooltip mdl-tooltip--top"><?php printf('%s : %s', __('Group'), $_getvar('ctx_userGroup')); ?></span>
                </span>
            </div>
        <?php endif; ?>
    </div>

    <div id="footer-right" class="footer-parts">
        <div id="status">
            <?php if ($_getvar('loggedIn')): ?>
                <button id="feedback-btn"
                        class="mdl-button mdl-js-button mdl-button--icon"
                        title="<?php echo __('Send Feedback'); ?>">
                    <i class="material-icons">rate_review</i>
                </button>
                <span for="feedback-btn"
                      class="mdl-tooltip mdl-tooltip--top"><?php echo __('Send Feedback'); ?></span>
            <?php endif; ?>

            <?php if (DEBUG): ?>
                <i class="material-icons mdl-color-text--red-900"
                   title="Debug ON">bug_report</i>
            <?php endif; ?>

            <?php if ($_getvar('isDemo')): ?>
                <span class="status-info">
                    <i class="material-icons mdl-color-text--teal-500"
                       title="<?php echo __('Demo'); ?>">play_circle_outline</i>
                </span>
            <?php endif; ?>

        </div>

        <div id="project">
            <span id="app-info"><?php echo $_getvar('app_name'), ' ', $_getvar('app_version'); ?></span>
            &nbsp;::&nbsp;
            <a id="app-project" href="https://extoco.de"
               target="_blank">extoco.de</a>
        </div>
        <!-- Close Project -->
    </div>
</footer> <!-- Close footer -->
