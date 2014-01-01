/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/*global jQuery, TubePressAjax, TubePressEvents, getTubePressBaseUrl, TubePressCss, Shadowbox */
/*jslint sloppy: true, white: true, onevar: true, undef: true, newcap: true, nomen: true, regexp: true, plusplus: true, bitwise: true, continue: true, browser: true, maxerr: 50, indent: 4 */


(function (jquery, tubePress) {

    /** http://ejohn.org/blog/ecmascript-5-strict-mode-json-and-more/ */
    'use strict';

    /* this stuff helps compression */

    var name                 = 'shadowbox',
        url                  = 'src/main/web/players/' + name + '/',
        text_lib             = 'lib',
        text_html            = 'html',
        beacon               = tubePress.Beacon,
        subscribe            = beacon.subscribe,
        langUtils            = tubePress.Lang.Utils,
        domInjector          = tubePress.DomInjector,
        event_prefix_players = 'tubepress.playerlocation.',

        isShadowBoxAvailable = function () {

            return langUtils.isDefined(window.Shadowbox);
        },

        initShadowbox = function () {

            Shadowbox.path = url + text_lib + '/';

            Shadowbox.init({

                initialHeight    : 160,
                initialWidth    : 320,
                skipSetup        : true,
                players            : [text_html],
                useSizzle        : false
            });

            Shadowbox.load();
        },

        loadShadowboxIfNeeded = function () {

            if (! isShadowBoxAvailable()) {

                var prefix = url + text_lib + '/' + name;

                domInjector.loadJs(prefix + '.js');
                domInjector.loadCss(prefix + '.css');

                langUtils.callWhenTrue(

                    initShadowbox,
                    isShadowBoxAvailable,
                    300
                );
            }
        },

        onPlayerInvoked = function (e, playerName, height, width, videoId, galleryId) {

            if (playerName !== name) {

                return;
            }

            Shadowbox.open({

                player:        text_html,
                height:        height,
                width:        width,
                content:    '&nbsp;'
            });
        },

        doPopulate = function (html) {

            jquery('#sb-player').html(html);
        },

        onPlayerPopulated = function (e, playerName, title, html, height, width, videoId, galleryId) {

            var callback, test;

            if (playerName !== name) {

                return;
            }

            callback = function () {

                doPopulate(html);
            };

            test = function () {

                return jquery('#sb-player').length > 0;
            };

            langUtils.callWhenTrue(

                callback,
                test,
                200
            );
        };

    subscribe(event_prefix_players + 'invoke', onPlayerInvoked);
    subscribe(event_prefix_players + 'populate', onPlayerPopulated);

    loadShadowboxIfNeeded();

}(jQuery, TubePress));