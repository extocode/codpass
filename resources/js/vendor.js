/**
 * Vendor Dependencies
 *
 * All third-party libraries are imported here.
 * Vite will create optimized chunks for these.
 */

console.log('[VENDOR] Starting imports...');

// jQuery - Core dependency
import $ from 'jquery';
console.log('[VENDOR] jQuery imported:', typeof $);
window.$ = window.jQuery = $;

// Date/Time libraries
import moment from 'moment';
console.log('[VENDOR] moment imported:', typeof moment);
import 'moment-timezone';
window.moment = moment;

// UI Components
import TomSelect from 'tom-select';
console.log('[VENDOR] TomSelect imported:', typeof TomSelect);

// jQuery plugin wrapper for backward compatibility with selectize API
$.fn.selectize = function(options = {}) {
    return this.each(function() {
        // Skip if already initialized
        if (this.tomselect) {
            return;
        }

        // Map Selectize options to Tom Select options
        const tsOptions = { ...options };

        // Tom Select uses 'create' as a function or boolean
        // It's compatible with Selectize's create option

        // Map plugin names from Selectize to Tom Select
        if (tsOptions.plugins) {
            const pluginMap = {
                'remove_button': 'remove_button',
                'clear_selection': 'clear_button',
                'dropdown_header': 'dropdown_header'
            };

            const mappedPlugins = {};
            for (const [pluginName, pluginOptions] of Object.entries(tsOptions.plugins)) {
                const mappedName = pluginMap[pluginName] || pluginName;

                if (pluginName === 'clear_selection') {
                    // Use dropdown_header plugin with clear behavior
                    mappedPlugins['dropdown_header'] = {
                        title: pluginOptions.title || 'Clear Selection'
                    };
                    // Store the clear behavior for later
                    tsOptions._clearOnHeaderClick = true;
                } else {
                    mappedPlugins[mappedName] = pluginOptions || {};
                }
            }
            tsOptions.plugins = mappedPlugins;
        }

        // Create Tom Select instance
        const ts = new TomSelect(this, tsOptions);

        // Handle clear_selection plugin behavior (click header to clear)
        if (tsOptions._clearOnHeaderClick && ts.dropdown_header) {
            ts.dropdown_header.addEventListener('click', function(e) {
                ts.clear();
                ts.close();
                ts.blur();
                e.preventDefault();
                e.stopPropagation();
            });
        }

        // Store instance for access via element
        this.selectize = ts;  // Legacy property name for compatibility
    });
};

window.TomSelect = TomSelect;

import toastr from 'toastr';
console.log('[VENDOR] toastr imported:', typeof toastr);
import GLightbox from 'glightbox';
console.log('[VENDOR] GLightbox imported:', typeof GLightbox);

// Global lightbox instance for programmatic control
let currentLightbox = null;

// jQuery compatibility wrapper for Magnific Popup API
$.magnificPopup = {
    open: function(options) {
        // Close any existing lightbox
        if (currentLightbox) {
            currentLightbox.close();
            currentLightbox = null;
        }

        const items = options.items || {};
        const callbacks = options.callbacks || {};
        const showCloseBtn = options.showCloseBtn !== false;

        // Create container for inline content
        let content;
        if (typeof items.src === 'string') {
            content = items.src;
        } else if (items.src instanceof $) {
            // jQuery object - get the outer HTML
            content = items.src.prop('outerHTML');
        } else if (items.src instanceof Element) {
            content = items.src.outerHTML;
        } else {
            content = String(items.src);
        }

        // Wrap content in glightbox-compatible format
        const wrappedContent = '<div class="glightbox-inline-content">' + content + '</div>';

        currentLightbox = GLightbox({
            elements: [{
                content: wrappedContent,
                type: 'inline'
            }],
            closeButton: showCloseBtn,
            touchNavigation: false,
            zoomable: false,
            draggable: false,
            onOpen: function() {
                if (typeof callbacks.open === 'function') {
                    // Pass the lightbox instance as 'this' context
                    callbacks.open.call(currentLightbox);
                }
            },
            onClose: function() {
                if (typeof callbacks.close === 'function') {
                    callbacks.close.call(currentLightbox);
                }
                currentLightbox = null;
            }
        });

        currentLightbox.open();
        return currentLightbox;
    },
    close: function() {
        if (currentLightbox) {
            currentLightbox.close();
            currentLightbox = null;
        }
    }
};

window.GLightbox = GLightbox;

import Clipboard from 'clipboard';
console.log('[VENDOR] Clipboard imported:', typeof Clipboard);

// Crypto & Security
import JSEncrypt from 'jsencrypt';
console.log('[VENDOR] JSEncrypt imported:', typeof JSEncrypt);
import zxcvbn from 'zxcvbn';
console.log('[VENDOR] zxcvbn imported:', typeof zxcvbn);
import SparkMD5 from 'spark-md5';
console.log('[VENDOR] SparkMD5 imported:', typeof SparkMD5);

// Polyfills
import 'eventsource-polyfill';
console.log('[VENDOR] eventsource-polyfill imported');

// Expose to global scope for legacy code compatibility
console.log('[VENDOR] Exposing to window...');
window.toastr = toastr;
window.Clipboard = Clipboard;
window.JSEncrypt = JSEncrypt;
window.zxcvbn = zxcvbn;
window.SparkMD5 = SparkMD5;

// Import vendor CSS
import 'tom-select/dist/css/tom-select.default.css';
import 'toastr/build/toastr.css';
import 'glightbox/dist/css/glightbox.css';

console.log('[VENDOR] All libraries loaded and exposed to window');
