<?php
/**
 * @var SP\Html\DataGrid\DataGridTab $data
 * @var ThemeIcons                   $icons
 * @var ConfigData                   $configData
 * @var callable                     $_getvar
 * @var Template                     $this
 */

use SP\Config\ConfigData;
use SP\Core\UI\ThemeIcons;
use SP\Mvc\View\Template;

$tabs = $_getvar('tabs');
?>
<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
    <?php if (!empty($tabs)): ?>
        <div id="tabsHeader" class="mdl-tabs__tab-bar"></div>
    <?php endif; ?>

    <?php if (empty($tabs)): ?>
        <div class="empty-state" style="text-align: center; padding: 60px 20px; color: #eeeeee; background: #1a1a1a;">
            <i class="material-icons" style="font-size: 64px; margin-bottom: 16px; opacity: 0.5;">security</i>
            <p style="font-size: 16px; margin: 0;"><?php echo __('No data available'); ?></p>
            <p style="font-size: 14px; margin-top: 8px; opacity: 0.7;"><?php echo __('Logging may be disabled or you may not have permission to view this section.'); ?></p>
        </div>
    <?php else: ?>
        <?php foreach ($tabs as $index => $data): ?>
            <div id="tabs-<?php echo $index; ?>"
                 class="mdl-tabs__panel"
                 data-tab-route="<?php echo $_getvar('tabsRoute'); ?>"
                 data-tab-index="<?php echo $index; ?>">

                <?php include __DIR__ . DIRECTORY_SEPARATOR . 'datagrid-grid.inc'; ?>
            </div>

            <script type="text/javascript">
                sysPassApp.theme.html.tabs.add("#tabsHeader", <?php echo $index; ?>, "<?php echo $data->getTitle(); ?>", <?php echo $index === $_getvar('activeTab') ? 1 : 0; ?>);
            </script>
        <?php endforeach; ?>
    <?php endif; ?>
</div>