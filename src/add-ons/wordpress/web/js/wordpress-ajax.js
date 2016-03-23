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

(function (tubePress) {

    /** http://ejohn.org/blog/ecmascript-5-strict-mode-json-and-more/ */
    'use strict';

    var ajaxUrl,
        text_url       = 'url',
        text_tubepress = 'tubepress',
        text_action    = 'action',
        text_data      = 'data',
        text_dataType  = text_data + 'Type',
        jquery         = tubePress.Vendors.jQuery,

        onAjax = function (options, originalOptions, jqxhr) {

            var data               = originalOptions[text_data],
                hasTubePressAction = data && data.hasOwnProperty(text_tubepress + '_' + text_action),
                actualUrl,
                dataTypeIsHtml,
                exactUrlMatch,
                spaceAfterUrl;

            if (!hasTubePressAction) {

                /** This is not a TubePress call */
                return;
            }

            actualUrl      = options[text_url];
            exactUrlMatch  = actualUrl === ajaxUrl;
            dataTypeIsHtml = options[text_dataType] === 'html';
            spaceAfterUrl  = actualUrl.indexOf(ajaxUrl + ' ') === 0;

            /**
             * Only process if URLs match *or* dataType is HTML and URL has a space at the end.
             */
            if (exactUrlMatch || (dataTypeIsHtml && spaceAfterUrl)) {

                data[text_action]  = text_tubepress;
                options[text_data] = jquery.param(data);
            }
        },

        init = function () {

            jquery.ajaxPrefilter(onAjax);
            ajaxUrl = tubePress.Environment.getAjaxEndpointUrl();
        };

    jquery(init);

}(TubePress));