/**
 * Plugin: "clear_selection" (selectize.js)
 * Copyright (c) 2017 sysPass authors
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not use this
 * file except in compliance with the License. You may obtain a copy of the License at:
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software distributed under
 * the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF
 * ANY KIND, either express or implied. See the License for the specific language
 * governing permissions and limitations under the License.
 *
 * @author nuxsmin
 */

Selectize.define('clear_selection', function (options) {
    var self = this;

    options = $.extend({
        title: 'Clear Selection'
    }, options);

    self.setup = (function () {
        var original = self.setup;

        return function () {
            original.apply(self, arguments);

            var $dropdown_header = $('<div class="selectize-dropdown-header">' +
                '<span class="selectize-dropdown-header-title">' + options.title + '</span>' +
                '<span class="selectize-dropdown-header-close">&times;</span>' +
                '</div>');

            self.$dropdown.prepend($dropdown_header);

            // Use mousedown + stopPropagation so the clear runs before selectize's
            // document mousedown handler blurs/closes the dropdown. Binding on the
            // header div catches clicks on its children (title span, close icon) too,
            // which a plain 'click' handler missed (only empty header space worked).
            $dropdown_header.on('mousedown', function (e) {
                e.preventDefault();
                e.stopPropagation();
                self.clear();
                self.close();
                self.blur();
            });
        };
    })();
});
