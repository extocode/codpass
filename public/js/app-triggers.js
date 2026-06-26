/*
 * sysPass
 *
 * @author nuxsmin
 * @link https://syspass.org
 * @copyright 2012-2018, Rubén Domínguez nuxsmin@$syspass.org
 *
 * This file is part of sysPass.
 *
 * sysPass is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * sysPass is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 *  along with sysPass.  If not, see <http://www.gnu.org/licenses/>.
 */

sysPass.Triggers = function (log) {
    "use strict";

    const regex = {
        email: "^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$",
    };

    // Detectar los campos select y añadir funciones
    const selectDetect = function ($container) {
        const options = {
            valueField: "id",
            labelField: "name",
            searchField: ["name"],
            onDropdownOpen: function ($dropdown) {
                // Add class to body to allow overflow for dropdowns
                $("body").addClass("selectize-dropdown-open");
                // Add overflow-visible to ALL ancestor containers up to body so the
                // dropdown is never clipped, regardless of which view it lives in
                // (tables, fieldsets, tab panels, popups, etc.). Reverted on close.
                const $parents = $(this.$input[0]).parentsUntil("body");
                $parents.addClass("dropdown-overflow-visible");
                // Raise z-index of parent row to ensure dropdown appears above subsequent rows
                $(this.$input[0]).closest("tr").css("z-index", "1000");
            },
            onDropdownClose: function ($dropdown) {
                $("body").removeClass("selectize-dropdown-open");
                $(this.$input[0]).parents(".dropdown-overflow-visible").removeClass("dropdown-overflow-visible");
                // Reset z-index of parent row
                $(this.$input[0]).closest("tr").css("z-index", "");
            },
            onInitialize: function () {
                const $wrapper = $(this.$wrapper[0]);
                const $input = $(this.$input[0]);
                const $selectBoxAddIcon = $input.siblings(".btn-add-select");

                if ($selectBoxAddIcon.length === 1) {
                    $wrapper.append($selectBoxAddIcon);
                }
            },
        };

        $container.find(".select-box").each(function (e) {
            const $this = $(this);
            const self_options = {};

            // Render the dropdown on <body> so it is not clipped by the
            // .mdl-layout / .mdl-layout__content scroll containers (overflow:auto).
            // Popups manage clipping via their own overflow/z-index rules, so they
            // keep the default in-place dropdown.
            if ($this.closest("#box-popup, .mfp-content, dialog").length === 0) {
                self_options.dropdownParent = "body";
            }

            if ($this.data("create") === true) {
                self_options.create = true;
            }

            options.plugins = $this.hasClass("select-box-deselect")
                ? { clear_selection: { title: sysPassApp.config.LANG[51] } }
                : {};

            if ($this.data("onchange")) {
                const onchange = $this.data("onchange").split("/");

                options.onChange = function (value) {
                    if (value > 0) {
                        if (onchange.length === 2) {
                            sysPassApp.actions[onchange[0]][onchange[1]]($this);
                        } else {
                            sysPassApp.actions[onchange[0]]($this);
                        }
                    }
                };
            }

            $this.selectize($.extend(self_options, options));
        });

        $container.find("#allowed_exts").selectize({
            create: function (input) {
                return {
                    value: input.toUpperCase(),
                    text: input.toUpperCase(),
                };
            },
            createFilter: new RegExp("^[a-z0-9]{1,4}$", "i"),
            plugins: ["remove_button"],
        });

        $container.find("#wikifilter").selectize({
            create: true,
            createFilter: new RegExp("^[a-z0-9:._-]+$", "i"),
            plugins: ["remove_button"],
        });

        $container.find(".select-items-tag").selectize({
            create: function (input) {
                return {
                    value: input.toLowerCase(),
                    text: input.toLowerCase(),
                };
            },
            createFilter: new RegExp(regex.email),
            plugins: ["remove_button"],
        });
    };

    /**
     * Ejecutar acción para botones
     * @param $obj
     */
    const handleActionButton = function ($obj) {
        log.info("handleActionButton: " + $obj.attr("id"));

        const onclick = $obj.data("onclick").split("/");
        let actions;

        const plugin = $obj.data("plugin");

        if (plugin !== undefined && sysPassApp.plugins[plugin] !== undefined) {
            actions = sysPassApp.plugins[plugin];
        } else {
            actions = sysPassApp.actions;
        }

        if (onclick.length === 2) {
            actions[onclick[0]][onclick[1]]($obj);
        } else {
            actions[onclick[0]]($obj);
        }
    };

    /**
     * Validate form fields and switch to tab with first invalid field if needed
     *
     * @param $form
     * @returns {boolean} true if valid, false if invalid
     */
    const validateFormWithTabs = function ($form) {
        // Find the first invalid required field
        const invalidField = $form.find("input[required], select[required], textarea[required]").filter(function () {
            return !this.validity.valid;
        }).first();

        if (invalidField.length === 0) {
            return true; // All valid
        }

        // Check if the invalid field is in a hidden tab panel
        const $tabPanel = invalidField.closest(".mdl-tabs__panel");
        if ($tabPanel.length > 0 && !$tabPanel.hasClass("is-active")) {
            // Switch to the tab containing the invalid field
            const panelId = $tabPanel.attr("id");
            const $tabsContainer = $tabPanel.closest(".mdl-tabs");
            const $tabLink = $tabsContainer.find(".mdl-tabs__tab[href='#" + panelId + "']");

            if ($tabLink.length > 0) {
                $tabsContainer.find(".mdl-tabs__tab").removeClass("is-active");
                $tabsContainer.find(".mdl-tabs__panel").removeClass("is-active");
                $tabLink.addClass("is-active");
                $tabPanel.addClass("is-active");
            }
        }

        // Focus and show validation message
        invalidField.focus();
        if (invalidField[0].reportValidity) {
            invalidField[0].reportValidity();
        }

        return false;
    };

    /**
     * Ejecutar acción para formularios
     *
     * @param $obj
     */
    const handleFormAction = function ($obj) {
        log.info("formAction");

        // For forms with tabs (novalidate), do manual validation first
        if ($obj.attr("novalidate") !== undefined || $obj.hasClass("form-action-tabs")) {
            if (!validateFormWithTabs($obj)) {
                return false;
            }
        }

        const lastHash = $obj.attr("data-hash");
        const currentHash = sysPassApp.util.hash.md5($obj.serialize());

        if (lastHash === currentHash) {
            sysPassApp.msg.ok(sysPassApp.config.LANG[55]);
            return false;
        }

        const plugin = $obj.data("plugin");
        let actions;

        if (plugin !== undefined && sysPassApp.plugins[plugin] !== undefined) {
            actions = sysPassApp.plugins[plugin];
        } else {
            actions = sysPassApp.actions;
        }

        const onsubmit = $obj.data("onsubmit").split("/");

        $obj.find("input[name='sk']").val(sysPassApp.sk.get());

        if (onsubmit.length === 2) {
            actions[onsubmit[0]][onsubmit[1]]($obj);
        } else {
            actions[onsubmit[0]]($obj);
        }
    };

    const bodyHooks = function () {
        log.info("bodyHooks");

        $("body")
            .on(
                "click",
                "button.btn-action[data-onclick][type='button']" +
                    ",li.btn-action[data-onclick]" +
                    ",span.btn-action[data-onclick]" +
                    ",i.btn-action[data-onclick]" +
                    ",a.btn-action[data-onclick]" +
                    ",div.btn-action[data-onclick]" +
                    ",tr.btn-action[data-onclick]" +
                    ",.btn-action-pager[data-onclick]",
                function (e) {
                    // For div.btn-action (card) or tr.btn-action (row), don't trigger if clicking on buttons/actions inside
                    if ($(this).is("div.btn-action, tr.btn-action")) {
                        var $target = $(e.target);
                        // Skip if clicking on a button, link, or action icon inside the card/row
                        if (
                            $target.closest(
                                "button, a, i.btn-action, i.material-icons, .mdl-menu, .account-info, .account-actions, .cell-actions",
                            ).length > 0
                        ) {
                            return;
                        }
                    }
                    handleActionButton($(this));
                },
            )
            .on("click", ".btn-back", function () {
                if (sysPassApp.requests.history.length() > 0) {
                    log.info("back");

                    const lastHistory = sysPassApp.requests.history.del();

                    if (!lastHistory) {
                        // No history to go back to, go home
                        sysPassApp.requests.history.reset();
                        sysPassApp.actions.getContent({ r: "account/index" }, "search");
                        return;
                    }

                    if (!lastHistory.hasOwnProperty("data")) {
                        lastHistory.data = { sk: sysPassApp.sk.get() };
                    } else {
                        lastHistory.data.sk = sysPassApp.sk.get();
                    }

                    sysPassApp.requests.getActionCall(
                        lastHistory,
                        lastHistory.callback,
                    );
                }
            })
            .on("click", ".btn-back-home", function (e) {
                e.preventDefault();
                log.info("back-home");
                sysPassApp.requests.history.reset();
                sysPassApp.actions.getContent({ r: "account/index" }, "search");
            })
            .on("submit", ".form-action", function (e) {
                e.preventDefault();

                handleFormAction($(this));
            })
            .on("invalid", ".form-action input, .form-action select, .form-action textarea", function (e) {
                // Handle validation errors for fields in hidden tab panels
                const $field = $(this);
                const $tabPanel = $field.closest(".mdl-tabs__panel");

                if ($tabPanel.length > 0 && !$tabPanel.hasClass("is-active")) {
                    // Field is in a hidden tab - switch to that tab first
                    e.preventDefault();

                    const panelId = $tabPanel.attr("id");
                    const $tabsContainer = $tabPanel.closest(".mdl-tabs");
                    const $tabLink = $tabsContainer.find(".mdl-tabs__tab[href='#" + panelId + "']");

                    if ($tabLink.length > 0) {
                        // Switch to the tab containing the invalid field
                        $tabsContainer.find(".mdl-tabs__tab").removeClass("is-active");
                        $tabsContainer.find(".mdl-tabs__panel").removeClass("is-active");
                        $tabLink.addClass("is-active");
                        $tabPanel.addClass("is-active");

                        // Focus the invalid field after tab switch
                        setTimeout(function() {
                            $field.focus();
                            // Trigger validation message display
                            if ($field[0].reportValidity) {
                                $field[0].reportValidity();
                            }
                        }, 100);
                    }
                }
            })
            .on("click", ".btn-help[data-help]", function () {
                const $this = $(this);
                const $helpSrc = $.find(
                    "div[for='" + $this.data("help") + "']",
                );

                if ($helpSrc.length > 0) {
                    const title =
                        sysPassApp.config.LANG[54] +
                            " - " +
                            $helpSrc[0].getAttribute("title") ||
                        sysPassApp.config.LANG[54];

                    mdlDialog().show({
                        title: title,
                        text: $helpSrc[0].innerHTML,
                        positive: {
                            title: sysPassApp.config.LANG[43],
                        },
                    });
                }
            })
            .on("reset", ".form-action", function (e) {
                e.preventDefault();

                log.info("reset");

                const $this = $(this);

                $this
                    .find("input:text, input:password, input:file, textarea")
                    .val("")
                    .parent("div")
                    .removeClass("is-dirty");
                $this
                    .find("input:radio, input:checkbox")
                    .prop("checked", false)
                    .prop("selected", false);
                $this
                    .find(
                        "input[name='start'], input[name='skey'], input[name='sorder']",
                    )
                    .val(0);

                $this.find("select").each(function () {
                    $(this)[0].selectize.clear(true);
                });

                $this.submit();
            })
            .on("click", ".btn-popup-close", function (e) {
                $.magnificPopup.close();
            })
            .on("theme:update", function () {
                log.debug("on:theme:update");

                const $box = $("#box-popup");

                if ($box.length > 0) {
                    sysPassApp.util.focus($box);
                } else {
                    sysPassApp.util.focus($(this));
                }
            });
    };

    /**
     * Triggers que se ejecutan en determinadas vistas
     */
    const views = {
        main: function ($obj) {
            log.info("views:main");

            if (!clipboard.isSupported()) {
                sysPassApp.msg.info(sysPassApp.config.LANG[65]);
            }

            $(document).on("click", ".btn-menu", function (e) {
                e.preventDefault();
                const $this = $(this);

                if (
                    $this.attr("data-historyreset") === "1" ||
                    $this.attr("data-history-reset") === "1"
                ) {
                    sysPassApp.requests.history.reset();
                }

                sysPassApp.actions.getContent(
                    { r: $this.data("route") },
                    $this.data("view"),
                );
            });

            sysPassApp.actions.notification.getActive();

            if (sysPassApp.config.STATUS.CHECK_NOTIFICATIONS) {
                setInterval(function () {
                    sysPassApp.actions.notification.getActive();
                }, 120000);
            }

            if ($obj.data("upgraded") === 0) {
                sysPassApp.actions.getContent({ r: "account/index" }, "search");
            } else {
                const $content = $("#content");
                const page = $content.data("page");

                views.common($content);

                if (page !== "" && typeof views[page] === "function") {
                    views[page]();
                }

                // Set initial active nav based on page
                const pageRouteMap = {
                    "search": "account/index",
                    "items": "itemsPreset/index",
                    "notifications": "notification/index"
                };
                if (page && pageRouteMap[page]) {
                    $(".mdl-navigation__link").removeClass("nav-active");
                    $(".mdl-navigation__link[data-route='" + pageRouteMap[page] + "']").addClass("nav-active");
                } else if (page === "search" || page === "") {
                    // Default to search/home
                    $(".mdl-navigation__link").removeClass("nav-active");
                    $(".mdl-navigation__link[data-route='account/index']").addClass("nav-active");
                }
            }

            if (typeof sysPassApp.theme.viewsTriggers.main === "function") {
                sysPassApp.theme.viewsTriggers.main();
            }
        },
        search: function () {
            log.info("views:search");

            const $frmSearch = $("#frmSearch");

            if ($frmSearch.length === 0) {
                return;
            }

            // $frmSearch.find("input[name='search']")
            //     .on("keyup", function (e) {
            //         e.preventDefault();
            //
            //         if (e.key === "Enter"
            //             || e.which === 13
            //         ) {
            //             $frmSearch.submit();
            //         }
            //     });

            $frmSearch.find("select, #rpp").on("change", function () {
                $frmSearch.submit();
            });

            $frmSearch.find("button.btn-clear").on("click", function (e) {
                e.preventDefault();

                $frmSearch.find('input[name="searchfav"]').val(0);

                $frmSearch[0].reset();
            });

            $("#globalSearch").click(function () {
                const val = $(this).prop("checked") == true ? 1 : 0;

                $frmSearch.find("input[name='gsearch']").val(val);
                $frmSearch.submit();
            });

            if (typeof sysPassApp.theme.viewsTriggers.search === "function") {
                sysPassApp.theme.viewsTriggers.search();
            }
        },
        login: function () {
            log.info("views:login");

            const $frmLogin = $("#frmLogin");

            if (
                sysPassApp.config.AUTH.AUTHBASIC_AUTOLOGIN &&
                $frmLogin.find("input[name='loggedOut']").val() === "0"
            ) {
                log.info("views:login:autologin");

                sysPassApp.msg.info(sysPassApp.config.LANG[66]);

                sysPassApp.actions.main.login($frmLogin);
            }
        },
        userpassreset: function () {
            log.info("views:userpassreset");

            const $form = $("#frmUserPassReset");

            sysPassApp.theme.passwordDetect($form);
        },
        footer: function () {
            log.info("views:footer");
        },
        common: function ($container) {
            log.info("views:common");

            selectDetect($container);

            const $sk = $container.find(":input[name='sk']");

            if ($sk.length > 0 && $sk[0].value !== "") {
                sysPassApp.sk.set($sk[0].value);
            }

            if (typeof sysPassApp.theme.viewsTriggers.common === "function") {
                sysPassApp.theme.viewsTriggers.common($container);
            }

            initializeTags($container);

            // Load parent account options for account forms (also needed in popups)
            const $selParentAccount = $container.find("#parent_account_id");
            if ($selParentAccount.length > 0) {
                sysPassApp.actions.items.get($selParentAccount);
            }

            sysPassApp.triggers.updateFormHash($container);
        },
        datatabs: function () {
            log.info("views:datatabs");

            $(".datagrid-action-search>form").each(function () {
                const $this = $(this);

                $this.find("button.btn-clear").on("click", function (e) {
                    e.preventDefault();

                    $this.trigger("reset");
                });
            });
        },
        config: function () {
            log.info("views:config");

            const $dropFiles = $("#drop-import-files");

            if ($dropFiles.length > 0) {
                const upload = sysPassApp.util.fileUpload($dropFiles);

                upload.url = sysPassApp.util.getUrl(
                    sysPassApp.actions.ajaxUrl.entrypoint,
                    { r: $dropFiles.data("action-route") },
                );
                upload.allowedMime =
                    sysPassApp.config.FILES.IMPORT_ALLOWED_MIME;
                upload.beforeSendAction = function () {
                    upload.setRequestData({
                        sk: sysPassApp.sk.get(),
                        csvDelimiter: $("#csvDelimiter").val(),
                        importPwd: $("#importPwd").val(),
                        importMasterPwd: $("#importMasterPwd").val(),
                        import_defaultuser: $("#import_defaultuser").val(),
                        import_defaultgroup: $("#import_defaultgroup").val(),
                    });
                };
            }
        },
        account: function () {
            log.info("views:account");

            const $listFiles = $("#list-account-files");

            if ($listFiles.length > 0) {
                sysPassApp.actions.account.listFiles($listFiles);
            }

            const $dropFiles = $("#drop-account-files");

            if ($dropFiles.length > 0) {
                const upload = sysPassApp.util.fileUpload($dropFiles);

                upload.url = sysPassApp.util.getUrl(
                    sysPassApp.actions.ajaxUrl.entrypoint,
                    {
                        r: [
                            $dropFiles.data("action-route"),
                            $dropFiles.data("item-id"),
                        ],
                    },
                );
                upload.allowedMime =
                    sysPassApp.config.FILES.ACCOUNT_ALLOWED_MIME;
                upload.requestDoneAction = function () {
                    sysPassApp.actions.account.listFiles($listFiles);
                };
            }

            const $selParentAccount = $("#parent_account_id");

            if ($selParentAccount.length > 0) {
                $selParentAccount.on("change", function () {
                    const $this = $(this);
                    const $pass = $("#accountpass,#accountpassR");

                    if ($this[0].value > 0) {
                        $pass.each(function () {
                            $(this).prop("disabled", "true");
                            $(this).prop("required", "false");
                        });
                    } else {
                        $pass.each(function () {
                            $(this).prop("disabled", "");
                            $(this).prop("required", "true");
                        });
                    }
                });

                sysPassApp.actions.items.get($selParentAccount);
            }
        },
        install: function () {
            log.info("views:install");

            const $form = $("#frmInstall");

            sysPassApp.theme.passwordDetect($form);
            selectDetect($form);
        },
    };

    const initializeTags = function ($container) {
        log.info("initializeTags");

        $container.find(".select-box-tags").selectize({
            persist: false,
            valueField: "id",
            labelField: "name",
            searchField: ["name"],
            plugins: ["remove_button"],
            onDropdownOpen: function () {
                // Tom Select: 'this' is the TomSelect instance
                // this.wrapper and this.input are DOM elements (not jQuery)
                $("body").addClass("selectize-dropdown-open");
                const $input = $(this.input);
                const $parents = $input.parentsUntil("body");
                $parents.addClass("dropdown-overflow-visible");
                $input.closest("tr").css("z-index", "1000");
            },
            onDropdownClose: function () {
                $("body").removeClass("selectize-dropdown-open");
                const $input = $(this.input);
                $input.parents(".dropdown-overflow-visible").removeClass("dropdown-overflow-visible");
                $input.closest("tr").css("z-index", "");
            },
            onInitialize: function () {
                const $wrapper = $(this.wrapper);
                const $input = $(this.input);
                const value = this.getValue();

                if (value !== "") {
                    $input.attr(
                        "data-hash",
                        sysPassApp.util.hash.md5(Array.isArray(value) ? value.join() : value),
                    );
                }

                const currentItemId = $input.data("currentItemId");

                if (currentItemId !== undefined) {
                    this.removeOption(currentItemId, true);
                }

                const $selectBoxTagsNext = $input.siblings(".btn-add-select");

                if ($selectBoxTagsNext.length === 1) {
                    $wrapper.append($selectBoxTagsNext);
                }

                const $selectBoxIcon = $input.siblings(".select-icon");

                if ($selectBoxIcon.length === 1) {
                    $wrapper.prepend($selectBoxIcon);
                }
            },
            onChange: function () {
                const $input = $(this.input);
                const value = this.getValue();

                // Calculates the current data hash and compares it against the orginal one.
                // It sets the data-updated attribute to the comparation result
                const updated =
                    sysPassApp.util.hash.md5(Array.isArray(value) ? value.join() : value) !==
                    $input.data("hash");
                $input.attr("data-updated", updated);
            },
        });
    };

    /**
     * Actualizar el token de seguridad en los atributos de los botones y formularios
     *
     */
    const updateSk = function () {
        $("#content")
            .find("[data-sk]")
            .each(function () {
                log.info("updateSk");

                $(this).data("sk", sysPassApp.sk.get());
            });
    };

    /**
     * Actualizar el hash de los formularios de acción
     */
    const updateFormHash = function ($container) {
        log.info("updateFormHash");

        let $form;

        if ($container !== undefined) {
            $form = $container.find(".form-action[data-hash]");
        } else {
            $form = $(".form-action[data-hash]");
        }

        if ($form.length > 0) {
            $form.each(function () {
                const $this = $(this);

                $this.attr(
                    "data-hash",
                    sysPassApp.util.hash.md5($this.serialize()),
                );
            });
        }
    };

    return {
        views: views,
        selectDetect: selectDetect,
        updateSk: updateSk,
        updateFormHash: updateFormHash,
        bodyHooks: bodyHooks,
    };
};
