/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/*global jQuery, TubePressAjax, TubePressEvents */
/*jslint sloppy: true, white: true, undef: true, newcap: true, nomen: true, regexp: true, plusplus: true, bitwise: true, continue: true, browser: true, maxerr: 50, indent: 4 */
(function (jquery, tubePress) {

    /** http://ejohn.org/blog/ecmascript-5-strict-mode-json-and-more/ */
    'use strict';

    var prefix  = 'tubepress_',
        embedded = 'embedded_',
        text_playerName = 'playerName',
        text_galleryId  = 'galleryId',
        text_html       = 'html',
    
        getTitleId = function (gId) {

            return '#' + prefix + embedded + 'title_' + gId;
        },
    
        /* this stuff helps compression */
        beacon                   = tubePress.Beacon,
        subscribe                = beacon.subscribe,
        name                     = 'normal',
        styler                   = tubePress.Ajax.LoadStyler,
        addStyle                 = styler.applyLoadingStyle,
        remStyle                 = styler.removeLoadingStyle,
        text_eventPrefix_players = 'tubepress.playerlocation.',
    
        getEmbedId = function (gId) {

            return '#' + prefix + embedded + 'object_' + gId;
        },

        invoke = function (e, data) {

            if (data[text_playerName] !== name) {

                return;
            }

            var galleryId  = data[text_galleryId],
                titleDivId = getTitleId(galleryId),
                titleDiv   = jquery(titleDivId);

            addStyle(titleDivId);
            addStyle(getEmbedId(galleryId));

            if (titleDiv.length > 0) {

                titleDiv[0].scrollIntoView(true);
            }
        },
        
        populate = function (e, data) {

            if (data[text_playerName] !== name) {

                return;
            }

            var galleryId  = data[text_galleryId];

            jquery('#' + prefix + 'gallery_' + galleryId + ' div.' + prefix + 'normal_' + embedded + 'wrapper:first').replaceWith(data[text_html]);

            remStyle(getTitleId(galleryId));
            remStyle(getEmbedId(galleryId));
        };

    subscribe(text_eventPrefix_players + 'invoke', invoke);
    subscribe(text_eventPrefix_players + 'populate', populate);

}(jQuery, TubePress));