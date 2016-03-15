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
/* jslint browser: true, devel: true */
/* global jQuery TubePress */
(function (tubePress) {

    /** http://ejohn.org/blog/ecmascript-5-strict-mode-json-and-more/ */
    'use strict';

    /* this stuff helps compression */
    var text_jqmodal         = 'jqmodal',
        subscribe            = tubePress.Beacon.subscribe,
        path                 = 'web/vendor/jqmodal/jqModal.',
        domInjector          = tubePress.DomInjector,
        text_gallery         = 'gallery',
        text_embedded        = 'embedded',
        text_px              = 'px',
        text_Id              = 'Id',
        text_id              = 'id',
        event_prefix_players = 'tubepress.' + text_gallery + '.player.',
        text_galleryId       = text_gallery + text_Id,
        text_itemId          = 'item' + text_Id,
        jquery               = tubePress.Vendors.jQuery,

        hider = function (hash) {

            hash.o.remove();
            hash.w.remove();
        },

        getDivId = function (data) {

            return text_jqmodal + data[text_galleryId] + data[text_itemId];
        },

        invoke = function (e, data) {

            var element   = jquery('<div />'),
                gallery   = tubePress.Gallery,
                galleryId = data[text_galleryId],
                options   = gallery.Options,
                getOpt    = options.getOption,
                width     = getOpt(galleryId, text_embedded + 'Width', 640),
                height    = getOpt(galleryId, text_embedded + 'Height', 360),
                halfWidth = (-1 * (width / 2.0));

            element.attr(text_id, getDivId(data));
            element.hide();
            element.height(height + text_px);
            element.width(width + text_px);
            element.addClass('jqmWindow');
            element.css('margin-left', halfWidth + text_px);
            element.appendTo('body');
            element.jqm({ onHide : hider }).jqmShow();
        },

        populate = function (e, data) {

            var element = jquery('#' + getDivId(data));

            element.html(data.html);
        };

    if (!jquery.isFunction(jquery.fn.jqm)) {

        domInjector.loadJs(path + 'js');
        domInjector.loadCss(path + 'css');
    }

    subscribe(event_prefix_players + 'invoke.' + text_jqmodal, invoke);
    subscribe(event_prefix_players + 'populate.' + text_jqmodal, populate);

}(TubePress));