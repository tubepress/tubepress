/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
(function (jquery) {

    'use strict';

    var id = '#provider_filter_field',

        showAndHide = function (selected, all) {

            jquery.each(all, function (index, value) {

                var elements = jquery('div[class*="' + value + '"]');

                if (jquery.inArray(value, selected) === -1) {

                    elements.hide();

                } else {

                    elements.show();
                }
            });
        },

        normalizeProviderName = function (e) {

            return 'tubepress-field-provider-' + jquery(this).val().toLowerCase();
        },

        applySettings = function () {

            var rawSelected = jquery(id + ' option:selected'),
                selected    = rawSelected.map(normalizeProviderName),
                all         = jquery(id + ' option').map(normalizeProviderName);

            showAndHide(selected, all);
        },

        init = function () {

            var field = jquery(id);

            if (field.length === 0) {

                return;
            }

            field.change(applySettings);

            applySettings();
        };

    jquery(init);

}(jQuery));