/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
var TubePressParticipantFilterHandler = (function () {

    var id = '#participant-filter-field',

        showAndHide = function (selected, all) {

            jQuery.each(all, function (index, value) {

                var elements = jQuery('div.' + value);

                if (jQuery.inArray(value, selected) === -1) {

                    elements.hide();

                } else {
                    
                    elements.show();
                }
            });
        },

        normalizeParticipantName = function (e) {

            return 'tubepress-participant-' + jQuery(this).val().toLowerCase();
        },

        applySettings = function () {

            var rawSelected = jQuery(id + ' option:selected'),
                selected    = rawSelected.map(normalizeParticipantName),
                all         = jQuery(id + ' option').map(normalizeParticipantName);

            showAndHide(selected, all);
        },

        init = function () {

            var field = jQuery(id);

            if (field.length === 0) {

                return;
            }

            field.change(applySettings);

            applySettings();
        };

    return { init : init };

}());

jQuery(function() {

    TubePressParticipantFilterHandler.init();
});