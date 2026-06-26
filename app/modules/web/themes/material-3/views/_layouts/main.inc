<?php
/**
 * @var callable $_getvar
 * @var Template $this
 */

use SP\Mvc\View\Template;

?>
<!DOCTYPE html>
<html lang="<?php echo $_getvar('lang'); ?>">
<head>
    <title><?php printf('%s :: %s', $_getvar('app_name'), $_getvar('app_desc')); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png"
          href="<?php echo $_getvar('logo_icon'); ?>">
    <?php foreach ($_getvar('cssLinks') as $cssLink): ?>
        <link rel="stylesheet" href="<?php echo $cssLink; ?>"/>
    <?php endforeach; ?>
</head>
<body class="<?php echo $_getvar('page'); ?>">
<div id="wrap">
    <noscript>
        <div id="nojs"><?php echo __('Javascript is needed in order to run correctly'); ?></div>
    </noscript>

    <div id="wrap-loading">
        <div id="loading"
             class="mdl-spinner mdl-spinner--single-color mdl-js-spinner"></div>
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

<?php foreach ($_getvar('jsLinks') as $jsLink): ?>
    <script type="text/javascript" src="<?php echo $jsLink; ?>"></script>
<?php endforeach; ?>

<script type="text/javascript">
    $(document).ready(function (e) {
        "use strict";

        sysPassApp.log.info("ready");

        sysPassApp.sk.set("<?php echo $_getvar('sk'); ?>");

        // Theme toggle (persisted in localStorage)
        var savedTheme = localStorage.getItem('syspass-theme');
        if (savedTheme === 'light') {
            document.body.classList.add('light-mode');
        }

        $(document).on('click', '#theme-toggle, #theme-toggle-drawer', function (ev) {
            ev.preventDefault();
            document.body.classList.toggle('light-mode');
            var isLight = document.body.classList.contains('light-mode');
            localStorage.setItem('syspass-theme', isLight ? 'light' : 'dark');
        });

        // Feedback dialog
        $(document).on('click', '#feedback-btn', function (ev) {
            ev.preventDefault();

            var sk = $(this).data('sk') || sysPassApp.sk.get();
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
                    onClick: function (e2) {
                        e2.preventDefault();

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

        /* ------------------------------------------------------------------
         * Sticky search / filter bar
         * Native position:sticky (see material-3.min.css) pins #searchbox
         * under the fixed header. This toggles .is-stuck the instant it pins
         * so the bar animates out to the full content width, and measures the
         * width / shift / padding into CSS vars so the inner controls stay
         * anchored with no layout shift or horizontal overflow.
         * Re-initialises itself when the search view is reloaded over AJAX.
         * ------------------------------------------------------------------ */
        (function () {
            "use strict";

            // Nearest scrolling ancestor == the scrollport position:sticky uses.
            function getScroller(el) {
                var node = el ? el.parentElement : null;
                while (node && node !== document.documentElement) {
                    var oy = window.getComputedStyle(node).overflowY;
                    if (oy === 'auto' || oy === 'scroll' || oy === 'overlay') {
                        return node;
                    }
                    node = node.parentElement;
                }
                return null; // falls back to the viewport / document scroll
            }

            function setup(sb) {
                if (!sb || sb.dataset.stickyInit === '1') { return; }
                sb.dataset.stickyInit = '1';

                // Opt-in: only stick when enabled in user preferences. The stylesheet
                // applies position:sticky unconditionally, so when disabled we override
                // it inline and skip all observers. Gating lives here (not in the
                // app-version-cached CSS) so it always takes effect.
                if (sb.getAttribute('data-sticky') !== '1') {
                    sb.style.position = 'relative';
                    return;
                }

                var scroller = getScroller(sb);
                var viewportScroll = !scroller;
                var content = document.getElementById('content') || sb.parentNode;
                var basePadL = null;

                // Zero-height marker sitting exactly at the bar's resting top edge.
                var sentinel = document.createElement('div');
                sentinel.className = 'searchbox-sentinel';
                sentinel.setAttribute('aria-hidden', 'true');
                sentinel.style.cssText = 'height:0;margin:0;padding:0;border:0;';
                sb.parentNode.insertBefore(sentinel, sb);

                function measure() {
                    if (!sb.isConnected) { return; }

                    // Lock the natural (unstuck) height so expanding the bar can
                    // never change its height and shift the rows below it.
                    if (!sb.classList.contains('is-stuck')) {
                        sb.style.setProperty('--sb-min-h', '0px');
                        sb.style.setProperty('--sb-min-h', sb.offsetHeight + 'px');
                    }

                    var clientLeft;
                    if (viewportScroll) {
                        clientLeft = 0;
                    } else {
                        clientLeft = scroller.getBoundingClientRect().left + scroller.clientLeft;
                    }

                    var contentRect = content.getBoundingClientRect();
                    var leftGap = contentRect.left - clientLeft;
                    if (leftGap < 0) { leftGap = 0; }

                    // #content is centred (margin: auto) => right gap == left gap.
                    // Expand symmetrically by both gaps so the bar reaches both edges.
                    // Derived from the same leftGap that drives the shift, so width and
                    // position can never disagree (independent of clientWidth quirks).
                    var stuckWidth = contentRect.width + (2 * leftGap);

                    if (basePadL === null && !sb.classList.contains('is-stuck')) {
                        basePadL = parseFloat(window.getComputedStyle(sb).paddingLeft) || 0;
                    }
                    var bpl = (basePadL === null) ? 0 : basePadL;

                    sb.style.setProperty('--sb-stuck-width', Math.round(stuckWidth) + 'px');
                    sb.style.setProperty('--sb-stuck-shift', (-Math.round(leftGap)) + 'px');
                    sb.style.setProperty('--sb-stuck-padl', Math.round(bpl + leftGap) + 'px');

                    if (window.SB_DEBUG) {
                        console.log('[sticky] leftGap=%o contentW=%o stuckWidth=%o scroller=%o',
                            leftGap, contentRect.width, stuckWidth,
                            viewportScroll ? 'window' : scroller);
                    }
                }

                measure();

                // Toggle .is-stuck the moment the bar pins to (or leaves) the top.
                if ('IntersectionObserver' in window) {
                    var io = new IntersectionObserver(function (entries) {
                        var e = entries[0];
                        var rootTop = e.rootBounds ? e.rootBounds.top : 0;
                        var stuck = !e.isIntersecting && e.boundingClientRect.top <= rootTop;
                        sb.classList.toggle('is-stuck', stuck);
                    }, { root: viewportScroll ? null : scroller, threshold: [0] });
                    io.observe(sentinel);
                }

                // Re-measure on resize / layout changes without animating the change.
                var raf = 0;
                function onResize() {
                    if (raf) { return; }
                    raf = window.requestAnimationFrame(function () {
                        raf = 0;
                        if (!sb.isConnected) { return; }
                        sb.classList.add('sb-no-anim');
                        measure();
                        void sb.offsetWidth; // flush, then restore transitions
                        sb.classList.remove('sb-no-anim');
                    });
                }
                window.addEventListener('resize', onResize);
                if ('ResizeObserver' in window && scroller) {
                    new ResizeObserver(onResize).observe(scroller);
                }
            }

            function init() {
                setup(document.getElementById('searchbox'));
            }

            init();

            // The search view is swapped in via AJAX; re-init when #searchbox reappears.
            if ('MutationObserver' in window) {
                var host = getScroller(document.getElementById('searchbox')) || document.body;
                new MutationObserver(function () {
                    var sb = document.getElementById('searchbox');
                    if (sb && sb.dataset.stickyInit !== '1') { init(); }
                }).observe(host, { childList: true, subtree: true });
            }
        })();
    });
</script>

</body> <!-- Close BODY...almost done..go..go..go -->
</html> <!-- Close HTML...ufff too much work!! :)) -->
<!-- Insert coin . . . -->
