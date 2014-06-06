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

    var ajaxUrl,
        text_url       = 'url',
        text_action    = 'action',
        text_tubepress = 'tubepress',

        onAjax = function (e, dataToSend) {

            if (dataToSend[text_url] !== ajaxUrl) {

                return;
            }

            var data = dataToSend.data;

            data[text_tubepress + '_wp_' + text_action] = data[text_action];
            data[text_action]                           = text_tubepress;
        },

        init = function () {

            ajaxUrl = tubePress.Environment.getAjaxEndpointUrl();
            tubePress.Beacon.subscribe('tubepress.ajax', onAjax);
        };

    jquery(init);

}(jQuery, TubePress));