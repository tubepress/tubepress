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

                return element.closest('.js-multisource-single-source-outermost');
            },

            getAllSourceContainers = function () {

                return jquery('.js-multisource-single-source-outermost');
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

        var newRandomSourceId = function () {

                return Math.floor(Math.random() * (1000000 - 100000) + 100000);
            },

            replaceIds = function (element, oldId, newId) {

                var attrs = [ 'id', 'href', 'data-multisourceid', 'aria-labelledby', 'aria-controls'],
                    attribute,
                    oldAttrValue,
                    newAttrValue,
                    index;

                for (index = 0; index < attrs.length; index += 1) {

                    attribute    = attrs[index];
                    oldAttrValue = element.attr(attribute);

                    if (oldAttrValue) {

                        newAttrValue = oldAttrValue.replace(oldId, newId);
                        element.attr(attribute, newAttrValue);
                    }
                }

                if (element.data('multisourceid') !== undefined) {

                    element.data('multisourceid', newId.toString());
                }
            },

            onAddSourceButtonClicked = function () {

                win.tubePressBeacon.publish('newsource.start');

                var last      = sourceFinder.getAllSourceContainers().last(),
                    oldId     = last.data('multisourceid'),
                    newId     = newRandomSourceId(),
                    clone     = last.clone(false).find('*').andSelf().each(function () {

                        replaceIds(jquery(this), oldId, newId);

                    }).first(),
                    appendTo  = last.parent(),
                    beacon    = win.tubePressBeacon,
                    eventData = {

                        'oldId'        : oldId,
                        'newId'        : newId,
                        'originSource' : last,
                        'newSource'    : clone
                    };

                beacon.publish('newsource.pre', eventData);

                clone.appendTo(appendTo);

                beacon.publish('newsource.post', eventData);
            },

            init = function () {

                jquery('#js-multisource-button-add').click(onAddSourceButtonClicked);
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
                    source     =  sourceFinder.getSourceContainerForElement(dis),
                    buttons    = jquery('.js-gallery-sources-delete-initiate');

                /**
                 * Don't allow deletion if we're down to just one.
                 */
                if (allSources.length <= 1) {

                    return;
                }

                /**
                 * Don't allow deletion if this is the first source.
                 */
                if (dis.is(buttons.first())) {

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

            isDeleting = function () {

                return jquery(this).data('deleting') === true;
            },

            onDeleteConfirmationClicked = function () {

                var allSources = sourceFinder.getAllSourceContainers(),
                    toDelete   = allSources.filter(isDeleting),
                    data       = {

                        source : toDelete
                    };

                win.tubePressBeacon.publish('sources.removal.pre', data);

                toDelete.remove();

                win.tubePressBeacon.publish('sources.removal.pre', data);
            },

            disableFirstDeleteButton = function () {

                var buttons = jquery('.js-gallery-sources-delete-initiate'),
                    first   = buttons.first();

                buttons.removeAttr('style');

                first.css('color', 'grey');
            },

            init = function () {

                disableFirstDeleteButton();
                jquery('#js-delete-confirmation-button').click(onDeleteConfirmationClicked);
                jquery(document).on('click', '.js-gallery-sources-delete-initiate', {}, onDeleteInitiatorClicked);
                win.tubePressBeacon.subscribe('newsource.post', disableFirstDeleteButton);
                win.tubePressBeacon.subscribe('sources.sort', disableFirstDeleteButton);
            };

        jquery(init);
    }());

    /**
     * Accordion handling.
     */
    (function () {

        var getAccordion = function () {

            return jquery('#js-multisource-accordion');

        },

            onAfterNewSourceAdded = function () {

                var colls = jquery('#gallery_source_category .collapse'),
                    last  = colls.last();

                last.collapse('show');
            },

            onSortStop = function () {

                win.tubePressBeacon.publish('sources.sort');
            },

            init = function () {

            /**
             * Make the accordion sortable.
             */
            getAccordion().sortable({

                stop: onSortStop
            });

            win.tubePressBeacon.subscribe('newsource.post', onAfterNewSourceAdded);
        };

        jquery(init);
    }());

}(jQuery, window));