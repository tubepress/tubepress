/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
(function (jquery) {

    'use strict';

    var multiSelectIt = function (index, val) {

            var field      = jquery(val),
                selectText = field.data('selecttext');

            field.multiselect({

                buttonClass : 'btn btn-default btn-sm',
                dropRight   : true,
                buttonText  : function () { return selectText; }
            });
        },

        init = function () {

            jquery('.tubepress-bootstrap-multiselect-field').each(multiSelectIt);
        };

    jquery(init);

}(jQuery));