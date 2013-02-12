/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
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

	var events       = tubePress.Events,
        playerEvents = events.PLAYERS,
        name         = 'shadowbox',
		url          = 'src/main/web/players/' + name + '/',
        text_lib     = 'lib',
        text_html    = 'html',
        beacon       = tubePress.Beacon,
        subscribe    = beacon.subscribe,
        langUtils    = tubePress.LangUtils,
        domInjector  = tubePress.DomInjector,

        isShadowBoxAvailable = function () {

            return langUtils.isDefined(window.Shadowbox);
        },

		boot = function () {

			Shadowbox.path = url + text_lib + '/';

			Shadowbox.init({

				initialHeight	: 160,
				initialWidth	: 320,
				skipSetup		: true,
				players			: [text_html],
				useSizzle		: false
			});

			Shadowbox.load();
		},

        performInitialBoot = function () {

            if (! isShadowBoxAvailable()) {

                var prefix = url + text_lib + '/' + name;

                domInjector.loadJs(prefix + '.js');
                domInjector.loadCss(prefix + '.css');

                langUtils.callWhenTrue(

                    boot,
                    isShadowBoxAvailable,
                    300
                );
            }
        },

		onPlayerInvoked = function (e, playerName, videoId, galleryId, width, height) {

            if (playerName !== name) {

                return;
            }

			Shadowbox.open({

				player:		text_html,
				height:		height,
				width:		width,
				content:	'&nbsp;'
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

	subscribe(playerEvents.PLAYER_INVOKE, onPlayerInvoked);
	subscribe(playerEvents.PLAYER_POPULATE, onPlayerPopulated);

    performInitialBoot();

}(jQuery, TubePress));