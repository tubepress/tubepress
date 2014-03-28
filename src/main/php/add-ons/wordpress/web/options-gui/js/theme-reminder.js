/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
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

            var row = jquery(doc.createElement('div')).addClass('row'),
                col = jquery(doc.createElement('div')).addClass('col-xs-12'),
                p   = jquery(doc.createElement('div')).addClass('well text-primary');

            p.html('Don\'t see what you\'re after? You can <a target="_blank" href="http://docs.tubepress.com/page/extend/themes/" style="font-weight: bold; text-decoration: underline">create your own custom theme</a> in minutes.');

            row.append(col.append(p));

            return row;
        },

        appendHtml = function (element) {

            element.insertAfter('#theme-category div.row:last');
        },

        init = function () {

            var html = createHtml();

            appendHtml(html);
        };

    jquery(init);

}(jQuery, document));