/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
/* jslint browser: true, devel: true */
/* global jQuery TubePress */
(function (jquery, tubePress) {

    /** http://ejohn.org/blog/ecmascript-5-strict-mode-json-and-more/ */
    'use strict';

    /* this stuff helps compression */
    var name                 = 'jqmodal',
        subscribe            = tubePress.Beacon.subscribe,
        path                 = tubePress.Environment.getBaseUrl() + '/src/main/web/vendor/jqmodal/jqModal.',
        domInjector          = tubePress.DomInjector,
        event_prefix_players = 'tubepress.playerlocation.',

        invoke = function (e, playerName, height, width, videoId, galleryId) {

            if (playerName !== name) {

                return;
            }

            var element = jquery('<div id="jqmodal' + galleryId + videoId + '" style="visibility: none; height: ' + height + 'px; width: ' + width + 'px;"></div>').appendTo('body'),
                hider = function (hash) {
                    hash.o.remove();
                    hash.w.remove();
                };

            element.addClass('jqmWindow');
            element.jqm({ onHide : hider }).jqmShow();
        },

        populate = function (e, playerName, title, html, height, width, videoId, galleryId) {

            if (playerName !== name) {

                return;
            }

            jquery('#jqmodal' + galleryId + videoId).html(html);
        };

    if (!jquery.isFunction(jquery.fn.jqm)) {

        domInjector.loadJs(path + 'js');
        domInjector.loadCss(path + 'css');
    }

    subscribe(event_prefix_players + 'invoke', invoke);
    subscribe(event_prefix_players + 'populate', populate);

}(jQuery, TubePress));