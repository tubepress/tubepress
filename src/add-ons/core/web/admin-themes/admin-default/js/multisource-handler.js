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

                return element.closest('.js-multisource-single-container');
            },

            getAllSourceContainers = function () {

                return jquery('.js-multisource-single-container');
            };

        return {

            getSourceContainerForElement : getSourceContainerForElement,
            getAllSourceContainers       : getAllSourceContainers
        }
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
     * Expands the last/single source.
     */
    (function () {

        var onAfterNewSourceAdded = function () {

                var colls = jquery('#gallery_source_category .collapse'),
                    last  = colls.last();

                //last.collapse('show');
            },

            init = function () {

                win.tubePressBeacon.subscribe('newsource.post', onAfterNewSourceAdded);
            };

        jquery(init);
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

                var allContainers = sourceFinder.getAllSourceContainers(),
                    last          = allContainers.last(),
                    oldId         = last.data('multisourceid'),
                    newId         = newRandomSourceId(),
                    clone         = last.clone(false).find('*').andSelf().each(function () {

                        replaceIds(jquery(this), oldId, newId);

                    }).first(),
                    appendTo  = last.parent(),
                    beacon    = win.tubePressBeacon,
                    eventData = {

                        'oldId'        : oldId,
                        'newId'        : newId,
                        'originSource' : last,
                        'newSource'    : clone
                    },

                    whenCollapseDoneCallback = function () {

                        clone.find('.collapse.in').removeClass('in');

                        clone.css('background-color', '#FFFF80');
                        clone.appendTo(appendTo);

                        clone.animate({
                            backgroundColor: 'white'
                        }, 1700);

                        beacon.publish('newsource.post', eventData);
                    },

                    allDoneCollapsing = function () {

                        var allDone = jquery('.js-multisource-single-container .collapse.in,.js-multisource-single-container .collapsing').length === 0;

                        if (allDone) {

                            whenCollapseDoneCallback();

                        } else {

                            setTimeout(allDoneCollapsing, 100);
                        }
                    };

                beacon.publish('newsource.pre', eventData);

                allContainers.find('.collapse').collapse('hide');

                setTimeout(allDoneCollapsing, 100);
            },

            init = function () {

                jquery('#js-multisource-button-add').click(onAddSourceButtonClicked);
            };

        jquery(init);
    }());

    /**
     * Title bar text and icon handling.
     */
    (function () {

        var init = function () {

            jquery('.js-media-provider-icon').attr('src', win.tubePressMediaProviderProperties.youtube.miniIconUrl);
            jquery('.js-media-provider-title').html(win.tubePressMediaProviderProperties.youtube.displayName);
        };

        jquery(init);
    }());

    /**
     * Initializes sorting.
     */
    (function () {

        var onSortStop = function () {

                win.tubePressBeacon.publish('sources.sort');
            },

            init = function () {

                jquery('#gallery_source_category > .row > .col-xs-12').sortable({

                    stop: onSortStop
                });
            };

        jquery(init);

    }());

    /**
     * Collapses and expands content when title bar is clicked.
     */
    (function () {

        var onTitleBarClicked = function (e) {

                var clicked         = jquery(e.target),
                    closestTitleBar = clicked.closest('.multisource-title-bar'),
                    tabArea         = closestTitleBar.next('.multisource-tab-area'),
                    isDelete        = clicked.hasClass('js-gallery-sources-delete-initiate'),
                    isSort          = !isDelete && clicked.hasClass('multisource-control-reorder');

                if (!isDelete && !isSort) {

                    jquery('.multisource-tab-area').not(tabArea).collapse('hide');
                    tabArea.collapse('toggle');
                }
            },

            init = function () {

                jquery(document).on('click', '.multisource-title-bar', {}, onTitleBarClicked);
            };

        jquery(init);

    }());

}(jQuery, window));