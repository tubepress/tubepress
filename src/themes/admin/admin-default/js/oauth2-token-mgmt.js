/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
(function (jquery, win, dokument) {

    'use strict';

    var openWindow,

        getAllConnectButtons = function () {

            return jquery('.oauth2-token-add');
        },

        switchToWindowClosedMode = function () {

            getAllConnectButtons().prop('disabled', false);
        },

        switchToWindowOpenMode = function () {

            getAllConnectButtons().prop('disabled', true);
            pollWindow();
        },

        addTokenToDropdowns = function (data) {

            var providerName  = data.provider.name,
                slug          = data.slug,
                dropdowns     = jquery('.oauth2-token-selection').filter('[data-provider=\'' + providerName + '\']'),
                existingSlugs = dropdowns.find("option[value='" + slug + "']"),
                slugExists    = existingSlugs.length === 1,
                newOption;

            if (!slugExists) {

                dropdowns.each(function () {

                    var select = jquery(this);

                    select.append(jquery('<option value="' + slug + '">' + slug + '</option>'));
                });
            }
        },

        addTokenToList = function (data) {

            var providerName = data.provider.name,
                slug         = data.slug,
                listElement  = jquery('#oauth2-tokens-' + providerName),
                existingSlug = listElement.find("li[data-slug='" + slug + "']"),
                slugExists   = existingSlug.length === 1,
                slugLabel    = existingSlug.find('span.label-success'),
                hiddenLi     = listElement.find("li[data-slug='noshow']"),
                newLi;

            if (slugExists) {

                if (slugLabel.length === 0) {

                    existingSlug.prepend(jquery('<span class="label label-success">UPDATED</span>'));

                } else {

                    slugLabel.html('UPDATED');
                }

            } else {

                newLi = jquery(hiddenLi.prop('outerHTML').replace(/noshow/g, slug).replace(/iiiii/g, providerName));

                listElement.append(newLi);

                newLi.prepend(jquery('<span class="label label-success">NEW</span>'));
                newLi.show();
            }

            listElement.show();
        },

        showNewTokenModal = function (slug, providerName) {

            var opts = {

                title: "New " + providerName + " API Token",

                message: "Successfully connected to " + providerName + " and obtained a new API token:<br/><br/><mark>" + slug + "</mark>",

                buttons : {

                    ok: {

                        label: "OK",
                        className: "btn-primary",
                        callback: function () {}
                    }
                },

                container: "html .tp-bs:first"
            };

            bootbox.dialog(opts);
        },

        onNewOauth2Token = function (e, data) {

            var providerName = data.provider.displayName,
                slug         = data.slug;

            showNewTokenModal(slug, providerName);
            addTokenToList(data);
            addTokenToDropdowns(data);
        },

        pollWindow = function () {

            if (openWindow.closed) {

                switchToWindowClosedMode();
                return;
            }

            try {

                if (openWindow.hasOwnProperty('tubepressOauth2Success')) {

                    openWindow.close();

                    win.tubePressBeacon.publish('oauth2.new-token', openWindow.tubepressOauth2Success);
                }

            } catch (e) {

                // if the user is sitting on the provider's site, we will get a SecurityError when
                // we try to access the window
            }

            setTimeout(pollWindow, 300);
        },

        onConnectButtonClick = function () {

            var dis          = jquery(this),
                url          = dis.data('url'),
                jqueryWindow = jquery(win),
                windowHeight = jqueryWindow.height(),
                windowWidth  = jqueryWindow.width(),
                round        = Math.round,
                left         = round(0.25 * windowWidth) + win.screenX,
                top          = round(0.25 * windowHeight),
                height       = round(0.5 * windowHeight),
                width        = round(0.5 * windowWidth),
                features     = [
                    'left=' + left,
                    'top=' + top,
                    'height=' + height,
                    'width=' + width
                ].join(',');

            openWindow = window.open(url, '', features);

            switchToWindowOpenMode();
        },

        init = function () {

            jquery(dokument).on('click', '.oauth2-token-add', onConnectButtonClick);
            win.tubePressBeacon.subscribe('oauth2.new-token', onNewOauth2Token);
        };

    jquery(init);

}(jQuery, window, document));