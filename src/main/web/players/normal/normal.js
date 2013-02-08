/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/*global jQuery, TubePressAjax, TubePressEvents */
/*jslint sloppy: true, white: true, undef: true, newcap: true, nomen: true, regexp: true, plusplus: true, bitwise: true, continue: true, browser: true, maxerr: 50, indent: 4 */
var TubePressNormalPlayer = (function () {

    /** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
    'use strict';

	var prefix  = 'tubepress_',
        embedded = 'embedded_',
	
		getTitleId = function (gId) {

			return '#' + prefix + embedded + 'title_' + gId;
		},
	
		/* this stuff helps compression */
		jquery       = jQuery,
        events       = TubePressEvents,
		playerEvents = events.PLAYERS,
        domEvents    = events.DOM,
        beacon       = TubePressBeacon,
        subscribe    = beacon.subscribe,
		name         = 'normal',
        addLoadEvent = domEvents.FADE_REQUESTED,
        remLoadEvent = domEvents.UNFADE_REQUESTED,
	
		getEmbedId = function (gId) {

			return '#' + prefix + embedded + 'object_' + gId;
		},

        fireEventIfExists = function (target, eventName) {

            if (jquery(target).length > 0) {

                beacon.publish(eventName, [ target ]);
            }
        },

		invoke = function (e, playerName, videoId, galleryId, width, height) {

            if (playerName !== name) {

                return;
            }

			var titleDivId = getTitleId(galleryId),
                titleDiv   = jquery(titleDivId);

            fireEventIfExists(titleDivId, addLoadEvent);
            fireEventIfExists(getEmbedId(galleryId), addLoadEvent);

            if (titleDiv.length > 0) {

                titleDiv[0].scrollIntoView(true);
            }
		},
		
		populate = function (e, playerName, title, html, height, width, videoId, galleryId) {

            if (playerName !== name) {

                return;
            }

			jquery('#' + prefix + 'gallery_' + galleryId + ' div.' + prefix + 'normal_' + embedded + 'wrapper:first').replaceWith(html);

            fireEventIfExists(getTitleId(galleryId), remLoadEvent);
            fireEventIfExists(getEmbedId(galleryId), remLoadEvent);
		};

	subscribe(playerEvents.PLAYER_INVOKE, invoke);
    subscribe(playerEvents.PLAYER_POPULATE, populate);

}());