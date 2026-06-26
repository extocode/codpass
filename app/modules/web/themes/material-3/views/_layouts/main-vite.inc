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

    <!-- Material Icons for MDL components -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Material Symbols Rounded for MD3 theme -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" rel="stylesheet">

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
<body class="md3-theme <?php echo $_getvar('page'); ?>">
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

        // Theme toggle functionality
        var savedTheme = localStorage.getItem('syspass-theme');
        if (savedTheme === 'light') {
            document.body.classList.add('light-mode');
        }

        $(document).on('click', '#theme-toggle, #theme-toggle-drawer', function(e) {
            e.preventDefault();
            document.body.classList.toggle('light-mode');
            var isLight = document.body.classList.contains('light-mode');
            localStorage.setItem('syspass-theme', isLight ? 'light' : 'dark');
        });

        // Feedback dialog
        $(document).on('click', '#feedback-btn', function(e) {
            e.preventDefault();

            var sk = sysPassApp.sk.get();
            var feedbackFiles = [];
            var FEEDBACK_MAX_FILES = 3;
            var FEEDBACK_MAX_FILE_SIZE = 5 * 1024 * 1024;

            function renderFeedbackFiles() {
                var $preview = $('#feedback-attach-preview').empty();

                if (!feedbackFiles.length) {
                    $preview.hide();
                    return;
                }

                $preview.show();

                feedbackFiles.forEach(function (file, idx) {
                    var name = file.name || 'screenshot.png';
                    var sizeKb = Math.round((file.size || 0) / 1024);
                    var $row = $('<div/>', {css: {display: 'flex', alignItems: 'center', marginBottom: '6px'}});

                    if (file.type && file.type.indexOf('image/') === 0 && window.URL && URL.createObjectURL) {
                        var thumbUrl = URL.createObjectURL(file);
                        var $thumb = $('<img/>', {
                            src: thumbUrl,
                            title: '<?php echo __('Click to enlarge'); ?>',
                            css: {maxWidth: '56px', maxHeight: '56px', border: '1px solid #ddd', borderRadius: '4px', marginRight: '8px', cursor: 'zoom-in'}
                        });
                        $thumb.on('click', function () { showFeedbackLightbox(thumbUrl); });
                        $row.append($thumb);
                    } else {
                        $row.append($('<i/>', {'class': 'material-icons', text: 'insert_drive_file', css: {color: '#888', marginRight: '8px'}}));
                    }

                    $row.append($('<span/>', {
                        text: name + ' (' + sizeKb + ' KB)',
                        css: {flex: '1', fontSize: '12px', color: '#888', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap'}
                    }));

                    var $rm = $('<button type="button" class="mdl-button mdl-js-button mdl-button--icon" title="<?php echo __('Remove'); ?>"><i class="material-icons">close</i></button>');
                    $rm.on('click', function () {
                        feedbackFiles.splice(idx, 1);
                        renderFeedbackFiles();
                    });
                    $row.append($rm);

                    $preview.append($row);
                });
            }

            function addFeedbackFiles(fileList) {
                for (var i = 0; i < fileList.length; i++) {
                    if (feedbackFiles.length >= FEEDBACK_MAX_FILES) {
                        sysPassApp.msg.error('<?php echo __('You can attach up to 3 files'); ?>');
                        break;
                    }
                    if (fileList[i].size > FEEDBACK_MAX_FILE_SIZE) {
                        sysPassApp.msg.error('<?php echo __('File too large (max 5 MB)'); ?>');
                        continue;
                    }
                    feedbackFiles.push(fileList[i]);
                }
                renderFeedbackFiles();
            }

            function showFeedbackLightbox(src) {
                var $overlay = $('<div/>', {
                    css: {position: 'fixed', top: 0, left: 0, right: 0, bottom: 0, background: 'rgba(0,0,0,.8)', zIndex: 100000, display: 'flex', alignItems: 'center', justifyContent: 'center', cursor: 'zoom-out'}
                });
                $overlay.append($('<img/>', {
                    src: src,
                    css: {maxWidth: '90vw', maxHeight: '90vh', borderRadius: '4px', boxShadow: '0 4px 24px rgba(0,0,0,.5)'}
                }));
                $overlay.on('click', function () { $overlay.remove(); });
                $('body').append($overlay);
            }

            var content =
                '<div id="box-feedback" style="padding: 8px 16px 0;">' +
                '  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100%;">' +
                '    <input class="mdl-textfield__input" type="text" id="feedback-title" name="feedback-title" maxlength="120" required/>' +
                '    <label class="mdl-textfield__label" for="feedback-title"><?php echo __('Title'); ?></label>' +
                '  </div>' +
                '  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="width:100%; margin-top: 12px;">' +
                '    <textarea class="mdl-textfield__input" rows="8" id="feedback-message" name="feedback-message" maxlength="4000" required style="resize:vertical; min-height:180px;"></textarea>' +
                '    <label class="mdl-textfield__label" for="feedback-message"><?php echo __('Describe the bug or feature request'); ?></label>' +
                '  </div>' +
                '  <div style="margin-top: 16px;">' +
                '    <input type="file" id="feedback-attach-input" accept="image/*,application/pdf,.txt,.log" multiple style="display:none;"/>' +
                '    <button type="button" id="feedback-attach-btn" class="mdl-button mdl-js-button mdl-button--raised">' +
                '      <i class="material-icons" style="vertical-align:middle;margin-right:4px;">attach_file</i><?php echo __('Attach screenshots or files'); ?>' +
                '    </button>' +
                '    <span style="display:block;margin-top:6px;color:#888;font-size:12px;"><?php echo __('Up to 3 files. You can also paste a screenshot (Ctrl+V)'); ?></span>' +
                '    <div id="feedback-attach-preview" style="display:none;margin-top:10px;"></div>' +
                '  </div>' +
                '</div>';

            mdlDialog().show({
                title: '<?php echo __('Send Feedback'); ?>',
                text: content,
                negative: {
                    title: '<?php echo __('Cancel'); ?>'
                },
                positive: {
                    title: '<?php echo __('Send'); ?>',
                    onClick: function (ev) {
                        ev.preventDefault();

                        var title = $.trim($('#feedback-title').val());
                        var message = $.trim($('#feedback-message').val());

                        if (title === '' || message === '') {
                            sysPassApp.msg.error('<?php echo __('Title and description required'); ?>');
                            return true;
                        }

                        var fd = new FormData();
                        fd.append('sk', sk);
                        fd.append('title', title);
                        fd.append('message', message);
                        feedbackFiles.forEach(function (f) {
                            fd.append('attachment[]', f, f.name || 'screenshot.png');
                        });

                        $.ajax({
                            url: 'index.php?r=feedback/save',
                            type: 'POST',
                            dataType: 'json',
                            data: fd,
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                if (response && response.status === 0) {
                                    sysPassApp.msg.ok(response.description || '<?php echo __('Feedback sent'); ?>');
                                    if (response.csrf) {
                                        sysPassApp.sk.set(response.csrf);
                                    }
                                } else {
                                    sysPassApp.msg.error((response && response.description) || '<?php echo __('Error sending feedback'); ?>');
                                }
                            },
                            error: function () {
                                sysPassApp.msg.error('<?php echo __('Error sending feedback'); ?>');
                            }
                        });
                    }
                },
                cancelable: true,
                contentStyle: {"max-width": "560px", "width": "90vw"},
                onLoaded: function () {
                    if (typeof componentHandler !== 'undefined') {
                        componentHandler.upgradeDom();
                    }

                    $('#feedback-attach-btn').on('click', function () {
                        $('#feedback-attach-input').trigger('click');
                    });

                    $('#feedback-attach-input').on('change', function () {
                        if (this.files && this.files.length) {
                            addFeedbackFiles(this.files);
                        }
                        this.value = '';
                    });

                    $('#box-feedback').on('paste', function (pe) {
                        var cd = (pe.originalEvent || pe).clipboardData;
                        if (!cd || !cd.items) { return; }
                        var pasted = [];
                        for (var i = 0; i < cd.items.length; i++) {
                            if (cd.items[i].kind === 'file') {
                                var f = cd.items[i].getAsFile();
                                if (f) { pasted.push(f); }
                            }
                        }
                        if (pasted.length) {
                            addFeedbackFiles(pasted);
                            pe.preventDefault();
                        }
                    });

                    setTimeout(function () { $('#feedback-title').focus(); }, 50);
                }
            });
        });

        // Copy username from search cards (doesn't trigger card navigation)
        $(document).on('click', '.clip-copy', function (e) {
            e.preventDefault();
            e.stopPropagation();

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
