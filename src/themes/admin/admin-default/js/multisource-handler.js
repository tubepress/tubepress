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

    var text_dot_js_multisource_single_container = '.js-multisource-single-container',
        text_multisourceid                       = 'multisourceid',
        text_deleting                            = 'deleting',

        sourceFinder = (function () {

        var getSourceContainerForElement = function (element) {

                return element.closest(text_dot_js_multisource_single_container);
            },

            getAllSourceContainers = function () {

                return jquery(text_dot_js_multisource_single_container);
            };

        return {

            getSourceContainerForElement : getSourceContainerForElement,
            getAllSourceContainers       : getAllSourceContainers
        };
    }());

    /**
     * Deletion handler.
     */
    (function () {

        var onDeleteInitiatorClicked = function () {

                var dis        = jquery(this),
                    allSources = sourceFinder.getAllSourceContainers(),
                    source     = sourceFinder.getSourceContainerForElement(dis),
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
                source.data(text_deleting, true);

                /**
                 * Show the confirmation modal.
                 */
                jquery('#js-confirm-delete').modal();
            },

            isDeleting = function () {

                return jquery(this).data(text_deleting) === true;
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
     * Add source button
     */
    (function () {

        var newRandomSourceId = function () {

                return Math.floor(Math.random() * (1000000 - 100000) + 100000);
            },

            replaceIds = function (element, oldId, newId) {

                var attrs = [ 'id', 'href', 'name', 'data-multisourceid', 'aria-labelledby', 'aria-controls'],
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

                if (element.data(text_multisourceid) !== undefined) {

                    element.data(text_multisourceid, newId.toString());
                }
            },

            onAddSourceButtonClicked = function () {

                win.tubePressBeacon.publish('newsource.start');

                var allContainers = sourceFinder.getAllSourceContainers(),
                    last          = allContainers.last(),
                    oldId         = last.data(text_multisourceid),
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

                        var allDone = jquery(text_dot_js_multisource_single_container + ' .collapse.in,' + text_dot_js_multisource_single_container + ' .collapsing').length === 0;

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

                jquery('#js-multisource-button-add').click(onAddSourceButtonClicked).popover();
            };

        jquery(init);
    }());

    /**
     * Title bar text and icon handling.
     */
    (function () {

        var radioNameRegex                     = /^tubepress-multisource-[0-9]+-mode$/,
            gallerySourceNameToProviderIdCache = {},

            hasProp = function (element, propertyName) {

                return element.hasOwnProperty(propertyName);
            },

            getMediaProviderProperties = function () {

                return win.tubePressMediaProviderProperties;
            },

            getSingleProviderPropertyByProviderIdAndPropertyName = function (providerId, propertyName) {

                var providerProps    = getMediaProviderProperties()[providerId];

                if (hasProp(providerProps, propertyName)) {

                    return providerProps[propertyName];
                }

                return '';
            },

            getProviderDisplayNameById = function (providerId) {

                return getSingleProviderPropertyByProviderIdAndPropertyName(providerId, 'displayName');
            },

            getIconUrlByProviderId = function (providerId) {

                return getSingleProviderPropertyByProviderIdAndPropertyName(providerId, 'miniIconUrl');
            },

            findProviderIdByGallerySourceValue = function (gallerySourceName) {

                var mediaProviderProperties = getMediaProviderProperties(),
                    providerId,
                    providerProps,
                    sourceNames,
                    text_sourceNames = 'sourceNames';

                if (!hasProp(gallerySourceNameToProviderIdCache, gallerySourceName)) {

                    for (providerId in mediaProviderProperties) {

                        if (hasProp(mediaProviderProperties, providerId)) {

                            providerProps = mediaProviderProperties[providerId];

                            if (hasProp(providerProps, text_sourceNames)) {

                                sourceNames = providerProps[text_sourceNames];

                                if (jquery.inArray(gallerySourceName, sourceNames) !== -1) {

                                    gallerySourceNameToProviderIdCache[gallerySourceName] = providerId;
                                    break;
                                }
                            }
                        }
                    }
                }

                return gallerySourceNameToProviderIdCache[gallerySourceName];
            },

            escapeHtml = function (text) {
                var map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };

                return text.replace(/[&<>"']/g, function (m) { return map[m]; });
            },

            getDescriptiveTextForSource = function (providerId, gallerySourceName, originalRadioElement) {

                var mediaProviderProps = getMediaProviderProperties(),
                    rawTemplate        = mediaProviderProps[providerId]['untranslatedModeTemplateMap'][gallerySourceName],
                    nextField          = originalRadioElement.parent().next('textarea,input').first(),
                    nextFieldRawValue,
                    nextFieldValueClean;

                if (!nextField) {

                    return rawTemplate;
                }

                nextFieldRawValue = nextField.val();

                if (nextFieldRawValue.length > 20) {

                    nextFieldRawValue = nextFieldRawValue.substr(0, 17) + '...';
                }

                nextFieldValueClean = escapeHtml(nextFieldRawValue);

                return rawTemplate.replace('%s', '<code>' + nextFieldValueClean + '</code>');
            },

            updateTitleBarToCurrentSelection = function (currentlyActiveRadioElement) {

                var nearestContainer = currentlyActiveRadioElement.closest(text_dot_js_multisource_single_container),
                    icon             = nearestContainer.find('.js-media-provider-icon'),
                    text             = nearestContainer.find('.js-media-provider-title'),
                    value            = currentlyActiveRadioElement.attr('value'),
                    providerId       = findProviderIdByGallerySourceValue(value),
                    displayName,
                    iconUrl,
                    template;

                if (!providerId) {

                    icon.attr('src', '');
                    text.html('...');
                    return;
                }

                displayName = getProviderDisplayNameById(providerId);
                iconUrl     = getIconUrlByProviderId(providerId);
                template    = getDescriptiveTextForSource(providerId, value, currentlyActiveRadioElement);

                icon.attr('src', iconUrl);
                text.html(displayName + ' - ' + template);
            },

            onRadioInputChange = function () {

                var dis     = jquery(this),
                    name    = dis.attr('name'),
                    checked = dis.is(':checked');

                if (!name || !checked || !radioNameRegex.test(name)) {

                    return;
                }

                updateTitleBarToCurrentSelection(dis);
            },

            onGallerySourceTextChange = function () {

                var dis    = jquery(this),
                    parent = dis.parent(),
                    radio  = parent.find('input:radio').first();

                if (!radio.is(':checked')) {

                    return;
                }

                onRadioInputChange.apply(radio);
            },

            init = function () {

                jquery(document).on('change', '#gallery_source_category input:radio', {}, onRadioInputChange);
                jquery(document).on('change keyup paste', '#gallery_source_category input:text,#gallery_source_category textarea,#gallery_source_category select', {}, onGallerySourceTextChange);

                jquery('#gallery_source_category input:radio').each(onRadioInputChange);
            };

        jquery(init);
    }());

    /**
     * Initializes sorting.
     */
    (function () {

        var onNewSourceAdded = function (e, data) {

                var reorder = jquery('div[data-multisourceid="' + data.newId + '"]:first .js-multisource-control-reorder');

                reorder.popover();
            },

            onSortStop = function () {

                win.tubePressBeacon.publish('sources.sort');
            },

            init = function () {

                jquery('#gallery_source_category > .row > .col-xs-12').sortable({

                    axis   : 'y',
                    handle : '.multisource-title-bar',
                    stop   : onSortStop
                });

                jquery('.js-multisource-control-reorder').popover();

                tubePressBeacon.subscribe('newsource.post', onNewSourceAdded);
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