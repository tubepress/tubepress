/**
 * @license
 *
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/*global jQuery, TubePressAjax, TubePressEvents, getTubePressBaseUrl, TubePressCss, Shadowbox */
/*jslint sloppy: true, white: true, onevar: true, undef: true, newcap: true, nomen: true, regexp: true, plusplus: true, bitwise: true, continue: true, browser: true, maxerr: 50, indent: 4 */


(function (tubePress) {

    /** http://ejohn.org/blog/ecmascript-5-strict-mode-json-and-more/ */
    'use strict';

    /* this stuff helps compression */

    var text_shadowbox       = 'shadowbox',
        text_player          = 'player',
        url                  = TubePressJsConfig.urls.usr + '/vendor/' + text_shadowbox + 'js/v3/' + text_shadowbox,
        text_html            = 'html',
        text_gallery         = 'gallery',
        text_galleryId       = text_gallery + 'Id',
        text_embedded        = 'embedded',
        beacon               = tubePress.Beacon,
        subscribe            = beacon.subscribe,
        langUtils            = tubePress.Lang.Utils,
        domInjector          = tubePress.DomInjector,
        event_prefix_players = 'tubepress.' + text_gallery + '.' + text_player + '.',
        sbId                 = '#sb-' + text_player,
        jquery               = tubePress.Vendors.jQuery,

        isShadowBoxAvailable = function () {

            return langUtils.isDefined(window.Shadowbox);
        },

        initShadowbox = function () {

            Shadowbox.path = url;

            Shadowbox.init({

                initialHeight : 160,
                initialWidth  : 320,
                skipSetup     : true,
                players       : [text_html],
                useSizzle     : false
            });

            Shadowbox.load();
        },

        loadShadowboxIfNeeded = function () {

            if (! isShadowBoxAvailable()) {

                domInjector.loadJs(url + '.js');
                domInjector.loadCss(url + '.css');

                langUtils.callWhenTrue(

                    initShadowbox,
                    isShadowBoxAvailable,
                    300
                );
            }
        },

        onPlayerInvoked = function (e, data) {

            var gallery   = tubePress.Gallery,
                galleryId = data[text_galleryId],
                options   = gallery.Options,
                getOpt    = options.getOption,
                height    = getOpt(galleryId, text_embedded + 'Height'),
                width     = getOpt(galleryId, text_embedded + 'Width');

            Shadowbox.open({

                player  : text_html,
                height  : height,
                width   : width,
                content : '&nbsp;'
            });
        },

        doPopulate = function (html) {

            jquery(sbId).html(html);
        },

        onPlayerPopulated = function (e, data) {

            var callback = function () {

                    doPopulate(data.html);
                },

                test = function () {

                    return jquery(sbId).length > 0;
                };

            langUtils.callWhenTrue(

                callback,
                test,
                100
            );
        };

    subscribe(event_prefix_players + 'invoke.' + text_shadowbox, onPlayerInvoked);
    subscribe(event_prefix_players + 'populate.' + text_shadowbox, onPlayerPopulated);

    loadShadowboxIfNeeded();

}(TubePress));