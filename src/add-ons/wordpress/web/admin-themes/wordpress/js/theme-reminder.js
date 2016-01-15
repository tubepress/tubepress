/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

(function (jquery, doc) {

    'use strict';

    var createHtml = function () {

            var row        = jquery(doc.createElement('div')).addClass('row'),
                col        = jquery(doc.createElement('div')).addClass('col-xs-12'),
                calloutDiv = jquery(doc.createElement('div')).addClass('bs-callout').addClass('bs-callout-warning'),
                h4         = jquery(doc.createElement('h4')),
                p          = jquery(doc.createElement('p'));

            h4.html('Don\'t see what you\'re after?');
            p.html('You can <a target="_blank" href="http://support.tubepress.com/customer/portal/articles/2046757-introduction" style="font-weight: bold; text-decoration: underline">create your own custom theme</a> in minutes.');

            calloutDiv.append(h4);
            calloutDiv.append(p);

            col.append(calloutDiv);
            row.append(col);

            return row;
        },

        appendHtml = function (element) {

            element.insertAfter('#theme_category div.row:last');
        },

        init = function () {

            var html = createHtml();

            appendHtml(html);
        };

    jquery(init);

}(jQuery, document));