<?php
/**
 * @var ThemeIcons        $icons
 * @var callable          $_getvar
 * @var ConfigData        $configData
 * @var AccountSearchItem $account
 * @var DataGrid          $data
 */

use SP\Config\ConfigData;
use SP\Core\UI\ThemeIcons;
use SP\Html\DataGrid\DataGrid;
use SP\Mvc\View\Components\SelectItem;
use SP\Services\Account\AccountSearchItem;

if (!isset($data)) {
    $data = $_getvar('data');
}

$searchFavorites = $_getvar('searchFavorites');
$pager = $data->getPager();
?>
<div id="searchbox" class="round5 shadow"
     data-sticky="<?php echo $_getvar('stickySearchBar') ? '1' : '0'; ?>">
    <form method="post" name="frmSearch" id="frmSearch" class="form-action"
          data-onsubmit="account/search"
          data-action-route="<?php echo $_getvar('searchRoute'); ?>">
        <div class="search-text">
            <div class="mdl-textfield mdl-js-textfield">
                <input class="mdl-textfield__input mdl-color-text--indigo-400"
                       type="text" id="search"
                       name="search"
                       value="<?php echo $_getvar('searchTxt'); ?>"/>
                <label class="mdl-textfield__label"
                       for="search"><?php echo __('Text to search for'); ?></label>
            </div>
        </div>

        <div class="search-filters">
            <select id="client" name="client"
                    class="select-box select-box-deselect" tabindex="3">
                <option value=""><?php echo __('Select Client'); ?></option>
                <?php /** @var SelectItem $client */
                foreach ($_getvar('clients') as $client): ?>
                    <option
                            value="<?php echo $client->getId(); ?>" <?php echo $client->isSelected() ? 'selected' : ''; ?>><?php echo htmlspecialchars($client->getName(), ENT_QUOTES); ?></option>
                <?php endforeach; ?>
            </select>

            <select id="category" name="category"
                    class="select-box select-box-deselect" tabindex="4">
                <option value=""><?php echo __('Select Category'); ?></option>
                <?php /** @var SelectItem $category */
                foreach ($_getvar('categories') as $category): ?>
                    <option
                            value="<?php echo $category->getId(); ?>" <?php echo $category->isSelected() ? 'selected' : ''; ?>><?php echo htmlspecialchars($category->getName(), ENT_QUOTES); ?></option>
                <?php endforeach; ?>
            </select>

            <div class="search-filters-tags">
                <select id="tags" name="tags[]" class="select-box select-box-deselect" multiple
                        tabindex="5">
                    <option value=""><?php echo __('Select Tag'); ?></option>
                    <?php /** @var SelectItem $tag */
                    foreach ($_getvar('tags') as $tag): ?>
                        <option
                                value="<?php echo $tag->getId(); ?>" <?php echo $tag->isSelected() ? 'selected' : ''; ?>><?php echo htmlspecialchars($tag->getName(), ENT_QUOTES); ?></option>
                    <?php endforeach; ?>
                </select>

            </div>

            <?php if ($_getvar('showGlobalSearch') && !$_getvar('isAdmin')): ?>
                <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect mdl-switch--inline"
                       for="globalSearch"
                       title="<?php echo __('Global search'); ?>">
                    <input type="checkbox" id="globalSearch"
                           class="mdl-switch__input mdl-color-text--indigo-400"
                           name="chkgsearch" tabindex="6"
                        <?php echo $_getvar('searchGlobal') ? 'checked="checked"' : ''; ?>/>
                    <span class="mdl-switch__label"
                          title="<?php echo __('Global search'); ?>"></span>
                </label>
                <input type="hidden" name="gsearch"
                       value="<?php echo $_getvar('searchGlobal'); ?>">
            <?php endif; ?>

            <?php if ($searchFavorites === true): ?>
                <button type="button" id="btn-favorite"
                        class="icon-searchfav mdl-button mdl-js-button mdl-button--icon is-active"
                        tabindex="7">
                    <i class="material-icons mdl-color-text--amber-A200"
                       title="<?php echo __('Display All'); ?>">star</i>
                </button>
            <?php else: ?>
                <button type="button" id="btn-favorite"
                        class="icon-searchfav mdl-button mdl-js-button mdl-button--icon"
                        tabindex="7">
                    <i class="material-icons"
                       title="<?php echo __('Show Favorites'); ?>">star_border</i>
                </button>
            <?php endif; ?>

            <button type="reset" id="btn-reset"
                    class="btn-clear mdl-button mdl-js-button mdl-button--icon"
                    title="<?php echo __('Clear Filters'); ?>"
                    tabindex="8">
                <i class="material-icons">close</i>
            </button>

            <div class="filter-buttons filter-slider" title="<?php echo __('Accounts per page'); ?>">
                <span id="rpp-value" class="slider-value"><?php echo $pager->getLimitCount(); ?></span>
                <input id="rpp" name="rpp" class="custom-slider"
                       type="range"
                       min="6" max="99" step="3"
                       value="<?php echo $pager->getLimitCount(); ?>"
                       oninput="document.getElementById('rpp-value').textContent=this.value"
                       tabindex="9"/>
            </div>
        </div>

        <input type="submit" style="visibility: hidden"/>

        <input type="hidden" name="start"
               value="<?php echo $pager->getLimitStart(); ?>">
        <input type="hidden" name="skey"
               value="<?php echo $pager->getSortKey(); ?>"/>
        <input type="hidden" name="sorder"
               value="<?php echo $pager->getSortOrder(); ?>"/>
        <input type="hidden" name="searchfav"
               value="<?php echo $searchFavorites; ?>"/>
        <input type="hidden" name="sk" value="<?php echo $_getvar('sk'); ?>">
        <input type="hidden" name="isAjax" value="1">
    </form>
</div>