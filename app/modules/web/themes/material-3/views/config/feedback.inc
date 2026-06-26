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

<div id="title" class="titleNormal">
    <i class="material-icons">rate_review</i>
    <?php echo __('Feedback'); ?>
</div>

<form method="post" name="frmFeedback" id="frmFeedback" class="form-action"
      data-onsubmit="config/save"
      data-action-route="configFeedback/save"
      data-hash="">
    <table id="tblFeedback" class="data tblConfig">
        <tr>
            <td class="descField"></td>
            <td class="valField">
                <ul class="config-mail-list-action mdl-list">
                    <li class="mdl-list__item mdl-list__item--two-line">
                        <div class="mdl-switch__box">
                            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect"
                                   for="feedback_enabled">
                                <input type="checkbox" id="feedback_enabled"
                                       class="mdl-switch__input"
                                       name="feedback_enabled" <?php echo $configData->isFeedbackEnabled() ? 'checked' : ''; ?>/>
                            </label>
                        </div>

                        <span class="mdl-list__item-primary-content">
                            <span><?php echo __('Enable Feedback email'); ?></span>
                            <span class="mdl-list__item-sub-title"><?php echo __('Send feedback submissions over SMTP using the settings below. If disabled, feedback is only written to the log.'); ?></span>
                        </span>
                    </li>
                </ul>
            </td>
        </tr>
        <tr>
            <td class="descField">
                <?php echo __('Server'); ?>
            </td>
            <td class="valField">
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <input id="feedback_server" name="feedback_server" type="text"
                           class="mdl-textfield__input mdl-color-text--indigo-400"
                           maxlength="128"
                           value="<?php echo htmlspecialchars($configData->getFeedbackServer(), ENT_QUOTES); ?>"/>
                    <label class="mdl-textfield__label"
                           for="feedback_server"><?php echo __('Server'); ?></label>
                </div>
            </td>
        </tr>
        <tr>
            <td class="descField">
                <?php echo __('Port'); ?>
            </td>
            <td class="valField">
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <input id="feedback_port" name="feedback_port" type="number"
                           pattern="[0-9]{1,5}"
                           class="mdl-textfield__input mdl-color-text--indigo-400"
                           maxlength="5"
                           value="<?php echo $configData->getFeedbackPort(); ?>"/>
                    <label class="mdl-textfield__label"
                           for="feedback_port"><?php echo __('Port'); ?></label>
                </div>
            </td>
        </tr>
        <tr>
            <td class="descField"></td>
            <td class="valField">
                <ul class="config-mail-list-action mdl-list">
                    <li class="mdl-list__item mdl-list__item--two-line">
                        <div class="mdl-switch__box">
                            <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect"
                                   for="feedback_auth_enabled">
                                <input type="checkbox" id="feedback_auth_enabled"
                                       class="mdl-switch__input"
                                       name="feedback_auth_enabled" <?php echo $configData->isFeedbackAuthenabled() ? 'checked' : ''; ?>/>
                            </label>
                        </div>

                        <span class="mdl-list__item-primary-content">
                            <span><?php echo __('Enable Authentication'); ?></span>
                            <span class="mdl-list__item-sub-title"></span>
                        </span>
                    </li>
                </ul>
            </td>
        </tr>
        <tr>
            <td class="descField">
                <?php echo __('User'); ?>
            </td>
            <td class="valField">
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <input id="feedback_user" name="feedback_user" type="text"
                           class="mdl-textfield__input mdl-color-text--indigo-400"
                           maxlength="50"
                           value="<?php echo htmlspecialchars($configData->getFeedbackUser(), ENT_QUOTES); ?>"/>
                    <label class="mdl-textfield__label"
                           for="feedback_user"><?php echo __('User'); ?></label>
                </div>
            </td>
        </tr>
        <tr>
            <td class="descField">
                <?php echo __('Password'); ?>
            </td>
            <td class="valField">
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <input id="feedback_pass" name="feedback_pass" type="password"
                           class="mdl-textfield__input mdl-color-text--indigo-400 passwordfield__input"
                           maxlength="128"
                           value="<?php echo $configData->getFeedbackPass() ? '***' : ''; ?>"/>
                    <label class="mdl-textfield__label"
                           for="feedback_pass"><?php echo __('Password'); ?></label>
                </div>
            </td>
        </tr>
        <tr>
            <td class="descField">
                <?php echo __('Security'); ?>
            </td>
            <td class="valField">
                <div class="lowres-title"><?php echo __('Security'); ?></div>

                <select name="feedback_security" id="sel-feedbacksecurity" size="1"
                        class="select-box select-box-deselect">
                    <option value=""><?php echo __('Disabled'); ?></option>
                    <?php foreach (['SSL', 'TLS'] as $security): ?>
                        <option
                                value="<?php echo $security; ?>" <?php echo ($configData->getFeedbackSecurity() === $security) ? 'selected' : ''; ?>><?php echo $security; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td class="descField">
                <?php echo __('Sender email address'); ?>
            </td>
            <td class="valField">
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <input id="feedback_from" name="feedback_from" type="email"
                           class="mdl-textfield__input mdl-color-text--indigo-400"
                           maxlength="128"
                           value="<?php echo htmlspecialchars($configData->getFeedbackFrom(), ENT_QUOTES); ?>"/>
                    <label class="mdl-textfield__label"
                           for="feedback_from"><?php echo __('Sender email address'); ?></label>
                </div>
            </td>
        </tr>
        <tr>
            <td class="descField">
                <?php echo __('Feedback recipient'); ?>
            </td>
            <td class="valField">
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <input id="mail_feedback" name="mail_feedback" type="email"
                           class="mdl-textfield__input mdl-color-text--indigo-400"
                           maxlength="128"
                           value="<?php echo htmlspecialchars($configData->getMailFeedback(), ENT_QUOTES); ?>"/>
                    <label class="mdl-textfield__label"
                           for="mail_feedback"><?php echo __('Feedback recipient email'); ?></label>
                </div>
                <div class="mdl-tooltip mdl-tooltip--large" for="mail_feedback">
                    <?php echo __('Address that receives messages submitted via the Feedback button.'); ?>
                </div>
            </td>
        </tr>
    </table>
</form>

<div class="tab-actions">
    <ul>
        <li>
            <button type="button"
                    class="btn-action mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--colored <?php echo $icons->getIconCheck()->getClassButton(); ?>"
                    data-onclick="config/mailCheck"
                    data-action-route="configFeedback/check"
                    data-src="#frmFeedback"
                    title="<?php echo $icons->getIconCheck()->getTitle(); ?>">
                <i class="material-icons"><?php echo $icons->getIconCheck()->getIcon(); ?></i>
            </button>
        </li>
        <li>
            <button form="frmFeedback"
                    class="mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--colored <?php echo $icons->getIconSave()->getClassButton(); ?>"
                    title="<?php echo $icons->getIconSave()->getTitle(); ?>">
                <i class="material-icons"><?php echo $icons->getIconSave()->getIcon(); ?></i>
            </button>
        </li>
    </ul>
</div>
