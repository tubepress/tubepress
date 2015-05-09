/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
(function (jquery, win) {

    var sourceFinder = (function () {

        var getSourceContainerForElement = function (element) {

                return element.closest('.js-gallery-source-outermost');
            },

            getAllSourceContainers = function () {

                return jquery('.js-gallery-source-outermost');
            };

        return {

            getSourceContainerForElement : getSourceContainerForElement,
            getAllSourceContainers       : getAllSourceContainers
        }
    }());

    /**
     * Add source button
     */
    (function () {

        var onAddSourceButtonClicked = function () {

                var last     = sourceFinder.getAllSourceContainers().last(),
                    clone    = last.clone(),
                    appendTo = last.parent();

                clone.appendTo(appendTo);
            },

            init = function () {

                jquery('#js-add-gallery-source-button').click(onAddSourceButtonClicked);
            };

        jquery(init);
    }());

    /**
     * Deletion handler.
     */
    (function () {

        var onDeleteInitiatorClicked = function () {

                var dis        = jquery(this),
                    allSources = sourceFinder.getAllSourceContainers(),
                    source     =  sourceFinder.getSourceContainerForElement(dis);

                /**
                 * Don't allow deletion if we're down to just one.
                 */
                if (allSources.length <= 1) {

                    return;
                }

                /**
                 * Note that we are thinking about deleting this source.
                 */
                source.data('deleting', true);

                /**
                 * Show the confirmation modal.
                 */
                jquery('#js-confirm-delete').modal();
            },

            isDeleting = function (element) {

                return jquery(this).data('deleting') === true;
            },

            onDeleteConfirmationClicked = function () {

                var allSources = sourceFinder.getAllSourceContainers(),
                    toDelete   = allSources.filter(isDeleting);

                toDelete.remove();
            },

            init = function () {

                jquery('#js-delete-confirmation-button').click(onDeleteConfirmationClicked);
                jquery('.js-gallery-sources-delete-initiate').click(onDeleteInitiatorClicked);
            };

        jquery(init);
    }());

    /**
     * Accordion handling.
     */
    (function () {

        var init = function () {

            /**
             * Make the accordion sortable.
             */
            jquery('#js-gallery-sources-accordion').sortable();
        };

        jquery(init);
    }());

}(jQuery, window));