/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 *
 * @author Eric D. Hough (eric@tubepress.org)
 */

/**
 * Handles "next" and "previous" video requests.
 */
(function (jquery, win, tubepress, gallery) {

    /** http://ejohn.org/blog/ecmascript-5-strict-mode-json-and-more/ */
    'use strict';

    var

        /** These variable declarations aide in compression. */
        galleryEvents   = gallery.Events,
        beacon          = tubepress.Beacon,
        galleryRegistry = gallery.Registry,
        subscribe       = beacon.subscribe,
        publish         = beacon.publish,

        /**
         * Go to the next video in the gallery.
         */
        onNextVideoRequested = function (event, galleryId) {

            /** Get the gallery's sequence. This is an array of video ids. */
            var sequence  = galleryRegistry.getSequence(galleryId),
                vidId     = galleryRegistry.getCurrentVideoId(galleryId),
                index     = jquery.inArray(vidId, sequence),
                lastIndex = sequence ? sequence.length - 1 : index;

            /** Sorry, we don't know anything about this video id, or we've reached the end of the gallery. */
            if (index === -1 || index === lastIndex) {

                return;
            }

            /** Start the next video in line. */
            publish(galleryEvents.NEW_VIDEO_REQUESTED, [ galleryId, sequence[index + 1] ]);
        },

        /** Play the previous video in the gallery. */
        onPrevVideoRequested = function (event, galleryId) {

            /** Get the gallery's sequence. This is an array of video ids. */
            var sequence = galleryRegistry.getSequence(galleryId),
                vidId    = galleryRegistry.getCurrentVideoId(galleryId),
                index    = jquery.inArray(vidId, sequence);

            /** Sorry, we don't know anything about this video id, or we're at the start of the gallery. */
            if (index === -1 || index === 0) {

                return;
            }

            /** Start the previous video in line. */
            publish(galleryEvents.NEW_VIDEO_REQUESTED, [ galleryId, sequence[index - 1] ]);
        };

    subscribe(galleryEvents.NEXT_VIDEO_REQUESTED, onNextVideoRequested);

    subscribe(galleryEvents.PREV_VIDEO_REQUESTED, onPrevVideoRequested);

}(jQuery, window, TubePress, TubePressGallery));
