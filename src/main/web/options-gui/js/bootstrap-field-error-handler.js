/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
var BootstrapFieldErrorHandler = (function (win) {

    var scrollTo = function (selector) {

            jQuery('html, body').animate({

                scrollTop: jQuery(selector).offset().top
            }, 2000);
        },

        applyErrorToField = function (fieldId, message) {

            var fieldSelector = '#' + fieldId;

            jQuery(fieldSelector).closest('div.form-group').addClass('has-error');

            jQuery(fieldSelector).next('span.help-block').before('<div class="help-block tubepress-field-error"><strong>' + message + '</strong></div>');
        },

        applyErrorsToFields = function (errors) {

            var callback = function (index, value) {

                applyErrorToField(value[0], value[1]);
            };

            jQuery.each(errors, callback);
        },

        showFirstError = function (errors) {

            var firstErrorId = errors[0][0],
                tabId = jQuery('#' + firstErrorId).closest('.tab-pane').attr('id');

            jQuery('.nav a[href="#' + tabId + '"]').tab('show');
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

    return {

        init              : init,
        applyErrorToField : applyErrorToField
    };

}(window));

jQuery(function() {

    BootstrapFieldErrorHandler.init();
});