<?php
/**
 * Vite-enabled Main Layout
 *
 * This layout uses Vite for asset loading instead of the legacy ResourceController.
 *
 * @var callable $_getvar
 * @var Template $this
 */

use SP\Mvc\View\Template;
use SP\Html\ViteAssets;

?>
<!DOCTYPE html>
<html lang="<?php echo $_getvar('lang'); ?>">
<head>
    <title><?php printf('%s :: %s', $_getvar('app_name'), $_getvar('app_desc')); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo $_getvar('logo_icon'); ?>">

    <?php
    // Vite HMR client (development mode only)
    echo ViteAssets::viteClient();

    // Vite styles
    echo ViteAssets::style('scss/app.scss');
    ?>

    <?php
    // Keep legacy CSS for now (during migration)
    foreach ($_getvar('cssLinks') as $cssLink): ?>
        <link rel="stylesheet" href="<?php echo $cssLink; ?>"/>
    <?php endforeach; ?>
</head>
<body class="<?php echo $_getvar('page'); ?>">
<div id="wrap">
    <noscript>
        <div id="nojs"><?php echo __('Javascript is needed in order to run correctly'); ?></div>
    </noscript>

    <div id="wrap-loading">
        <div id="loading" class="mdl-spinner mdl-spinner--single-color mdl-js-spinner"></div>
        <div id="taskStatus"></div>
    </div>

    <div id="container" class="<?php echo $_getvar('page'); ?>"
         data-page="<?php echo $_getvar('page'); ?>"
         data-upgraded="<?php echo (int)$this->isUpgraded(); ?>">
        <?php
        if ($_getvar('useFixedHeader')):
            include $this->includePartial('fixed-header');
        elseif ($this->hashContentTemplates()):
            foreach ($this->getContentTemplates() as $template):
                include $template;
            endforeach;
        endif;
        ?>
    </div> <!-- Close container -->

    <?php include $this->includePartial('footer'); ?>
</div> <!-- Close wrap -->

<?php
// Keep legacy JS for now (during migration)
foreach ($_getvar('jsLinks') as $jsLink): ?>
    <script type="text/javascript" src="<?php echo $jsLink; ?>"></script>
<?php endforeach; ?>

<?php
// Vite JavaScript modules
echo ViteAssets::script('js/vendor.js');
echo ViteAssets::script('js/app.js');
echo ViteAssets::script('js/theme.js');
?>

<script type="text/javascript">
    $(document).ready(function (e) {
        "use strict";

        sysPassApp.log.info("ready");
        sysPassApp.sk.set("<?php echo $_getvar('sk'); ?>");

        console.log("Vite assets loaded", {
            isDevelopment: <?php echo ViteAssets::isDevelopment() ? 'true' : 'false'; ?>,
            vendor: window.jQuery ? 'loaded' : 'missing'
        });

        // Copy username from search cards (doesn't trigger card navigation)
        $(document).on('click', '.clip-copy', function (ev) {
            ev.preventDefault();
            ev.stopPropagation();

            var text = this.dataset.clipValue || '';
            if (!text) { return; }

            var ok = function () { sysPassApp.msg.ok('<?php echo __('Name copied to clipboard'); ?>'); };
            var fail = function () { sysPassApp.msg.error('<?php echo __('Could not copy to clipboard'); ?>'); };

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(ok, fail);
            } else {
                var ta = document.createElement('textarea');
                ta.value = text;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                try { document.execCommand('copy'); ok(); } catch (err) { fail(); }
                document.body.removeChild(ta);
            }
        });

        // Brighten the copy icon on hover for affordance
        $(document).on('mouseenter', '.clip-copy', function () { this.style.opacity = '1'; });
        $(document).on('mouseleave', '.clip-copy', function () { this.style.opacity = '.45'; });
    });
</script>

</body>
</html>
