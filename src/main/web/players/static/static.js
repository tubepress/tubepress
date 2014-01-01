/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
(function (jquery, tubePress) {

    /** http://ejohn.org/blog/ecmascript-5-strict-mode-json-and-more/ */
    'use strict';

    /* this stuff helps compression */
    var tubePressLangUtils = tubePress.Lang.Utils,
        galleryRegistry    = tubePress.Gallery.Registry,

        isJqueryQueryAvailable = function () {

            return tubePressLangUtils.isDefined(jquery.query);
        },

        getVideoIdFromIdAttr = function (id) {

            var end = id.lastIndexOf('_');

            return id.substring(16, end);
        },

        scanAndModifyThumbs = function () {

            jquery("a[id^='tubepress_']").each(function () {

                var dis       = jquery(this),
                    rel_split = dis.attr('rel').split('_'),
                    page,
                    newId,
                    newUrl,
                    galleryId = rel_split[3];

                if (galleryRegistry.getPlayerLocationName(galleryId) !== 'static') {

                    return;
                }

                newId  = getVideoIdFromIdAttr(dis.attr('id'));
                page   = galleryRegistry.getCurrentPageNumber(galleryId);
                newUrl = jquery.query.set('tubepress_video', newId).set('tubepress_page', page).toString();

                dis.attr('href', newUrl);
                dis.unbind('click');
            });
        };

    if (isJqueryQueryAvailable()) {

        scanAndModifyThumbs();

    } else {

        tubePress.DomInjector.loadJs(tubePress.Environment.getBaseUrl() + '/src/main/web/vendor/jquery.query/jQuery.query.js');
        tubePressLangUtils.callWhenTrue(

            scanAndModifyThumbs,
            isJqueryQueryAvailable,
            100
        );
    }

    tubePress.Beacon.subscribe('tubepress.gallery.newthumbs', scanAndModifyThumbs);

}(jQuery, TubePress));