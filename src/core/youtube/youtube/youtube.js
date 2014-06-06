/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
(function () {

    /** http://ejohn.org/blog/ecmascript-5-strict-mode-json-and-more/ */
    'use strict';

    /* this stuff helps compression */
    var invoke = function (e, data) {

        if (data.playerName !== 'youtube') {

            return;
        }

        window.location = 'http://www.youtube.com/watch?v=' + data.videoId;
    };

    TubePress.Beacon.subscribe('tubepress.playerlocation.invoke', invoke);

}());