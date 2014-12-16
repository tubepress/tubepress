/*!
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

    var text_tubepress          = 'tubepress',
        text_hash               = '#',
        text_tubepress_         = text_tubepress + '_',
        text_embedded_          = 'embedded_',
        text_player             = 'player',
        text_gallery            = 'gallery',
        text_galleryId          = text_gallery + 'Id',
        text_html               = 'html',
        text_dot                = '.',
        text_space              = ' ',
        text_dash               = '-',
        text_normal             = 'normal',
        text_title              = 'title',
        text_firstQualifier     = ':first',
        text_modernSelectorPre  = text_dot + 'js' + text_dash + text_tubepress + text_dash + text_player + text_dash + text_normal + text_firstQualifier,
        text_eventPrefix_player = text_tubepress + text_dot + text_gallery + text_dot + text_player + text_dot,

        /* this stuff helps compression */
        beacon    = tubePress.Beacon,
        subscribe = beacon.subscribe,
        styler    = tubePress.Ajax.LoadStyler,
        addStyle  = styler.applyLoadingStyle,
        remStyle  = styler.removeLoadingStyle,

        getModernSelector = function (galleryId) {

            return tubePress.Gallery.Selectors.getOutermostSelectorModern(galleryId)
                + text_space + text_modernSelectorPre;
        },

        getLegacyTitleSelector = function (galleryId) {

            return text_hash + text_tubepress_ + text_embedded_ + text_title + '_' + galleryId;
        },

        getLegacyTitleElement = function (galleryId) {

            return jquery(getLegacyTitleSelector(galleryId));
        },

        getLegacyEmbedSelector = function (galleryId) {

            return text_hash + text_tubepress_ + text_embedded_ + 'object_' + galleryId;
        },

        getLegacyEmbedElement = function (galleryId) {

            return jquery(getLegacyEmbedSelector(galleryId));
        },

        scrollTo = function (element) {

            if (element.length > 0) {

                element[0].scrollIntoView(true);
            }
        },

        invoke = function (e, data) {

            var galleryId           = data[text_galleryId],
                legacyTitleSelector = getLegacyTitleSelector(galleryId),
                legacyTitleElement  = getLegacyTitleElement(galleryId),
                legacyEmbedSelector = getLegacyEmbedSelector(galleryId),
                modernSelector      = getModernSelector(galleryId),
                modernElement       = jquery(modernSelector);

            addStyle(modernSelector);
            addStyle(legacyTitleSelector);
            addStyle(legacyEmbedSelector);

            scrollTo(modernElement);
            scrollTo(legacyTitleElement);
        },

        populate = function (e, data) {

            var galleryId           = data[text_galleryId],
                legacyTitleElement  = getLegacyTitleElement(galleryId),
                legacyEmbedElement  = getLegacyEmbedElement(galleryId),
                html                = data[text_html],
                modernSelector      = getModernSelector(galleryId),
                modernElement       = jquery(modernSelector);

            if (legacyEmbedElement.length > 0 && legacyTitleElement.length > 0) {

                legacyTitleElement.parent('div').replaceWith(html);

            } else {

                modernElement.html(html);
                remStyle(modernSelector);
            }
        };

    subscribe(text_eventPrefix_player + 'invoke' + text_dot + text_normal, invoke);
    subscribe(text_eventPrefix_player + 'populate' + text_dot + text_normal, populate);

}(jQuery, TubePress));