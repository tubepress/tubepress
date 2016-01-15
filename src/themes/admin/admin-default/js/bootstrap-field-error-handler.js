/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
(function (jquery, win) {

    'use strict';

    var scrollTo = function (selector) {

            var element = jquery(selector),
                offset  = element.offset();

            if (!offset) {

                return;
            }

            jquery('html, body').animate({

                scrollTop: offset.top - 80
            }, 800);
        },

        applyErrorToField = function (fieldId, message) {

            var fieldSelector    = '#' + fieldId,
                closestFormGroup = jquery(fieldSelector).closest('div.form-group');

            closestFormGroup.addClass('has-error');

            closestFormGroup.find('span.help-block:first').before('<div class="help-block tubepress-field-error"><strong>' + message + '</strong></div>');
        },

        applyErrorsToFields = function (errors) {

            var callback = function (index, value) {

                applyErrorToField(value[0], value[1]);
            };

            jquery.each(errors, callback);
        },

        showFirstError = function (errors) {

            var firstErrorId = errors[0][0],
                tabId = jquery('#' + firstErrorId).closest('.tab-pane').attr('id');

            jquery('.nav a[href="#' + tabId + '"]').tab('show');
            scrollTo('#' + firstErrorId);
        },

        init = function () {

            var errors = win.TubePressErrors;

            if (errors.length === 0) {

                return;
            }

            applyErrorsToFields(errors);
            showFirstError(errors);
        };

    jquery(init);

}(jQuery, window));