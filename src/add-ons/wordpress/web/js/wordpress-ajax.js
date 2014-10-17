/*!
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

    var ajaxUrl,
        text_url       = 'url',
        text_tubepress = 'tubepress',
        text_data      = 'data',

        onAjax = function (options, originalOptions, jqxhr) {

            if (options[text_tubepress] !== true || options[text_url] !== ajaxUrl) {

                return;
            }

            var data = originalOptions[text_data];

            data.action = text_tubepress;

            options[text_data] = jquery.param(data);
        },

        init = function () {

            jquery.ajaxPrefilter(onAjax);
            ajaxUrl = tubePress.Environment.getAjaxEndpointUrl();
        };

    jquery(init);

}(jQuery, TubePress));