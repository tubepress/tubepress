/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/* global jQuery, TubePressEvents */
/* jslint white: true, onevar: true, undef: true, newcap: true, nomen: true, regexp: true, plusplus: true, bitwise: true, continue: true, browser: true, maxerr: 50, indent: 4 */

(function (jquery, tubePress) {

    /** http://ejohn.org/blog/ecmascript-5-strict-mode-json-and-more/ */
    'use strict';

    /* this stuff helps compression */
    var name                 = 'popup',
        subscribe            = tubePress.Beacon.subscribe,
        windows              = {},
        event_prefix_players = 'tubepress.playerlocation.',

        invoke = function (e, playerName, height, width, videoId, galleryId) {

            if (playerName !== name) {

                return;
            }

            var top  = (screen.height / 2) - (height / 2),
                left = (screen.width / 2) - (width / 2);

            windows[galleryId + videoId] = window.open('', '', 'location=0,directories=0,menubar=0,scrollbars=0,status=0,toolbar=0,width=' + width + 'px,height=' + height + 'px,top=' + top + ',left=' + left);
        },

        populate = function (e, playerName, title, html, height, width, videoId, galleryId) {

            if (playerName !== name) {

                return;
            }

            var preamble  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">\n<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html;charset=utf-8" /><title>' + title + '</title></head><body style="margin: 0pt; background-color: black;">',
                postAmble = '</body></html>',
                wind      = windows[galleryId + videoId].document;

            wind.write(preamble + html + postAmble);
            wind.close();
        };

    subscribe(event_prefix_players + 'invoke', invoke);
    subscribe(event_prefix_players + 'populate', populate);

}(jQuery, TubePress));