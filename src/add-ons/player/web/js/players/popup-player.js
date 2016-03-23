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

/* global jQuery, TubePressEvents */
/* jslint white: true, onevar: true, undef: true, newcap: true, nomen: true, regexp: true, plusplus: true, bitwise: true, continue: true, browser: true, maxerr: 50, indent: 4 */

(function (tubePress, screen, win) {

    /** http://ejohn.org/blog/ecmascript-5-strict-mode-json-and-more/ */
    'use strict';

    /* this stuff helps compression */
    var text_popup           = 'popup',
        subscribe            = tubePress.Beacon.subscribe,
        windows              = {},
        text_gallery         = 'gallery',
        text_Id              = 'Id',
        event_prefix_players = 'tubepress.' + text_gallery + '.player.',
        text_galleryId       = text_gallery + text_Id,
        text_item            = 'item',
        text_itemId          = text_item + text_Id,
        text_height          = 'height',
        text_width           = 'width',
        text_embedded        = 'embedded',
        text_mediaItem       = 'mediaItem',

        half = function (val) {

            return (val / 2);
        },

        invoke = function (e, data) {

            var gallery   = tubePress.Gallery,
                galleryId = data[text_galleryId],
                options   = gallery.Options,
                getOpt    = options.getOption,
                height    = getOpt(galleryId, text_embedded + 'Height'),
                width     = getOpt(galleryId, text_embedded + 'Width'),
                top       = half(screen[text_height]) - half(height),
                left      = half(screen[text_width]) - half(width);

            windows[galleryId + data[text_itemId]] = win.open('', '', 'location=0,directories=0,menubar=0,scrollbars=0,status=0,toolbar=0,width=' + width + 'px,height=' + height + 'px,top=' + top + ',left=' + left);
        },

        populate = function (e, data) {

            var item      = data[text_mediaItem],
                title     = item.title,
                preamble  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">\n<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html;charset=utf-8" /><title>' + title + '</title></head><body style="margin: 0pt; background-color: black;">',
                postAmble = '</body></html>',
                wind      = windows[data[text_galleryId] + data[text_itemId]].document;

            wind.write(preamble + data.html + postAmble);
            wind.close();
        };

    subscribe(event_prefix_players + 'invoke.' + text_popup, invoke);
    subscribe(event_prefix_players + 'populate.' + text_popup, populate);

}(TubePress, screen, window));