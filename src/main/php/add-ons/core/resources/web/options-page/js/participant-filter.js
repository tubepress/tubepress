/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
var TubePressOptionFilter = (function () {

        'use strict';

        var normalizeProviderName = function (raw) {

                var normal = raw.replace('show', '').replace('Options', '');

                return 'tubepress-participant-' + normal.toLowerCase();
            },

            doShowAndHide = function (arrayOfSelected, arrayOfPossible) {

                var selector = '', i;

                for (i = 0; i < arrayOfPossible.length; i += 1) {

                    if (i !== 0) {

                        selector += ', ';
                    }

                    selector += '.' + arrayOfPossible[i];
                }

                jQuery(selector).each(function () {

                    var element = jQuery(this), x;

                    for (x = 0; x < arrayOfSelected.length; x += 1) {

                        if (element.hasClass(arrayOfSelected[x])) {

                            element.show();
                            return;
                        }
                    }

                    element.hide();
                });
            },

            filterHandler = function () {

                //get the selected classes
                var selected = jQuery('#multiselect-disabledOptionsPageParticipants option:selected').map(function (e) {

                        return normalizeProviderName(jQuery(this).val());
                    }),

                    //get all the classes
                    allPossible = jQuery('#multiselect-disabledOptionsPageParticipants option').map(function (e) {

                        return normalizeProviderName(jQuery(this).val());
                    });

                //run it, yo
                doShowAndHide(selected, allPossible);
            },

            init = function () {

                var multiSelect = jQuery('#multiselect-disabledOptionsPageParticipants');

                //make the multi-selects
                multiSelect.multiselect({

                    selectedText : 'choose...'
                });

                jQuery('#multiselect-metadropdown').multiselect({

                    selectedText : 'choose...',
                    height: 350
                });

                //bind to value changes on the filter drop-down
                multiSelect.change(filterHandler);

                //filter based on what's in the drop-down
                filterHandler();
            };

        return {

            init : init
        };

    }());

jQuery(document).ready(function () {

    'use strict';

    TubePressOptionFilter.init();
});