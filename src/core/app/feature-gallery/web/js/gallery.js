/*!
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com).
 * This file is part of TubePress (http://tubepress.com).
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * This script expects galleries to "register" themselves by including a snippet of JS
 * anywhere on the page. This snippet of JS should look something like this:
 *
 * <script type="text/javascript">
 *     var tubePressGalleryRegistrar = tubePressGalleryRegistrar || [];
 *     tubePressGalleryRegistrar.push(['register', '123456789', {} ]);
 * </script>
 *
 * As you can see, this is an asynchronous JS call that can happen anywhere on the page.
 * The second argument to push(), "123456789" in the example, is the page-unique gallery identifier.
 * The third argument to push(), "{}" in the example, is JSON config for this gallery. This config
 * should contain the following elements:
 *
 * {
 *     "options" : { ... },       //a dictionary of TubePress options to values
 *                                //that are likely useful for frontend functionality
 *
 *     "ephemeral" : { ... },     //a dictionary of "ephemeral" options to values for this gallery that should be
 *                                //sent in any Ajax request back to the server. We separate these from the
 *                                //"options" attribute to cut down on URL length and server processing.
 * }
 *
 * When this script sees such a registration, it will fire an event called "tubepress.gallery.load" with the following
 * data object:
 *
 * {
 *     "galleryId" : 123456789,
 *     "params"    : {
 *
 *         "options"   : { ... },
 *         "ephemeral" : { ... }
 *     }
 * }
 *
 * This script may also publish the following events:
 *
 * tubepress.gallery.fluidThumbs                    //After we have made thumbnails fluid
 * {
 *     "galleryId"     : 123456789,
 *     "newThumbWidth" : 155
 * }
 *
 * tubepress.gallery.item.change                    //When an item change has been requested
 * {
 *     "galleryId" : 123456789,
 *     "itemId"   : "ekv9384jJD"
 * }
 *
 * tubepress.gallery.player.invoke.<playername>     //When we are invoking a player location (e.g. right after a user clicks
 *                                                  //a thumbnail
 * {
 *     "galleryId" : 123456789,
 *     "itemId"   : "ekv9384jJD"
 * }
 * 
 * tubepress.gallery.player.populate.<playername>   //When we have received a response from the server for some player HTML
 * {
 *     "galleryId" : 123456789,
 *     "itemId"    : "ekv9384jJD"
 *     "item"      : { ... },            //a dictionary representing the backing tubepress_app_media_item_api_MediaItem attributes
 *     "html"      : " ... "             //the HTML for this player
 * }
 *
 * tubepress.gallery.player.error.<playername>     //When we have received an error response from the server for some player HTML
 * {
 *     "galleryId" : 123456789,
 *     "itemId"    : "ekv9384jJD"
 * }
 *
 * tubepress.gallery.pagechange                     //When the page for a gallery changes
 * {
 *     "galleryId" : 123456789,
 *     "page"      : 4
 * }
 */

/**
 * IE8 and below forces us to declare this now.
 *
 * http://tobyho.com/2013/03/13/window-prop-vs-global-var/
 */
var tubePressGalleryRegistrar;

(function (jquery, win, tubepress) {

    /** http://ejohn.org/blog/ecmascript-5-strict-mode-json-and-more/ */
    'use strict';

    var text_tubepress      = 'tubepress',
        text_gallery        = 'gallery',
        text_item           = 'item',
        text_dot            = '.',
        text_underscore     = '_',
        text_dash           = '-',
        text_hash           = '#',
        text_space          = ' ',
        text_player         = 'player',
        text_Id             = 'Id',
        text_id             = 'id',
        text_page           = 'page',
        text_params         = 'params',
        text_urls           = 'urls',
        text_sys            = 'sys',
        text_js             = 'js',
        text_div            = 'div',
        text_fluid          = 'fluid',
        text_class          = 'class',
        text_ajaxPagination = 'ajaxPagination',
        text_fluidThumbs    = text_fluid + 'Thumbs',
        text_html           = 'html',
        text_current        = 'current',
        text_thumb          = 'thumb',
        text_thumbs         = text_thumb + 's',
        text_js_dash        = text_js + text_dash,
        text_tubepress_dash = text_tubepress + text_dash,
        text_galleryId      = text_gallery + text_Id,
        text_itemId         = text_item + text_Id,
        text_dot_js_dash    = text_dot + text_js_dash,
        text_un_gallery_un  = text_underscore + text_gallery + text_underscore,

        text_eventPrefix_gallery        = text_tubepress + text_dot + text_gallery + text_dot,
        text_event_tx_galleryLoad       = text_eventPrefix_gallery + 'load',
        text_event_tx_galleryPageChange = text_eventPrefix_gallery + 'pagechange',
        text_event_rx_galleryNewThumbs  = text_eventPrefix_gallery + 'new' + text_thumbs,

        text_eventPrefix_item    = text_eventPrefix_gallery + text_item + text_dot,
        text_event_tx_itemChange = text_eventPrefix_item + 'change',
        text_event_rx_itemStart  = text_eventPrefix_item + 'start',
        text_event_rx_itemStop   = text_eventPrefix_item + 'stop',

        text_eventPrefix_player         = text_eventPrefix_gallery + text_player + text_dot,
        text_eventPrefix_playerPopulate = text_eventPrefix_player + 'populate' + text_dot,
        text_eventPrefix_playerInvoke   = text_eventPrefix_player + 'invoke' + text_dot,

        beacon            = tubepress.Beacon,
        subscribe         = beacon.subscribe,
        publish           = beacon.publish,
        langUtils         = tubepress.Lang.Utils,
        isDefined         = langUtils.isDefined,
        environment       = tubepress.Environment,
        domInjector       = tubepress.DomInjector,
        troo              = true,
        fawlse            = false,
        tubePressJsConfig = win.TubePressJsConfig,

        /**
         * Provides convenient selectors to access the outermost gallery div.
         */
        selectors = (function () {

            var outerMostCache = {},
                thumbAreaCache = {},

                getOutermostSelectorLegacy = function (galleryId) {

                    return text_hash + text_tubepress + text_un_gallery_un + galleryId;
                },

                getOutermostSelectorModern = function (galleryId) {

                    return text_dot_js_dash + text_tubepress_dash + text_gallery + text_dash + galleryId;
                },

                getOutermostElement = function (galleryId) {

                    if (!langUtils.isDefined(outerMostCache[galleryId])) {

                        outerMostCache[galleryId] = jquery(getOutermostSelectorLegacy(galleryId)).add(getOutermostSelectorModern(galleryId));
                    }

                    return outerMostCache[galleryId];
                },

                getThumbAreaSelectorModern = function (galleryId) {

                    return getOutermostSelectorModern(galleryId) + text_space + text_dot_js_dash + text_tubepress_dash + 'pagination-and-' + text_thumbs;
                },

                getThumbAreaSelectorLegacy = function (galleryId) {

                    return text_hash + text_tubepress + text_un_gallery_un + galleryId + '_thumbnail_area';
                },

                getThumbAreaElement = function (galleryId) {

                    if (!langUtils.isDefined(thumbAreaCache[galleryId])) {

                        thumbAreaCache[galleryId] = jquery(getThumbAreaSelectorLegacy(galleryId)).add(getThumbAreaSelectorModern(galleryId));
                    }

                    return thumbAreaCache[galleryId];
                };

            return {

                getOutermostElement        : getOutermostElement,
                getOutermostSelectorModern : getOutermostSelectorModern,
                getThumbAreaElement        : getThumbAreaElement
            };
        }()),

        /**
         * Simple wrapper around jQuery.data() to access gallery data.
         */
        dataFacade = (function () {

            var data = jquery.data,

                internalGetElement = function (galleryId) {

                    return selectors.getOutermostElement(galleryId);
                },

                internalDataGet = function (galleryId, key) {

                    return data(internalGetElement(galleryId), key);
                },

                internalDataSet = function (galleryId, key, value) {

                    data(internalGetElement(galleryId), key, value);
                };

            return {

                get : internalDataGet,
                set : internalDataSet
            };
        }()),

        /**
         * Provides access to the JS parameters for each gallery on the page.
         */
        options = (function () {

            var text_options   = 'options',
                text_ephemeral = 'ephemeral',
                isPlainObject  = jquery.isPlainObject,

                internalGetArray = function (galleryId, key) {

                    var raw = dataFacade.get(galleryId, text_params);

                    if (raw === '' || !isPlainObject(raw) || !isDefined(raw[key]) || !isPlainObject(raw[key])) {

                        return {};
                    }

                    return raw[key];
                },

                internalGet = function (galleryId, key, optionName, defaultValue) {

                    var raw = internalGetArray(galleryId, key);

                    return isDefined(raw[optionName]) ? raw[optionName] : defaultValue;
                },

                getOption = function (galleryId, optionName, defaultValue) {

                    return internalGet(galleryId, text_options, optionName, defaultValue);
                },

                getEphemeralOptions = function (galleryId) {

                    return internalGetArray(galleryId, text_ephemeral);
                },

                onNewGallery = function (event, data) {

                    var galleryId = data[text_galleryId],
                        options   = data[text_params];

                    dataFacade.set(galleryId, text_params, options);
                };

            subscribe(text_event_tx_galleryLoad, onNewGallery);

            return {

                getOption           : getOption,
                getEphemeralOptions : getEphemeralOptions
            };
        }()),

        /**
         * Keeps track of which item, if any, a gallery is currently playing.
         */
        nowShowing = (function () {

            var data = dataFacade,
                get  = data.get,
                set  = data.set,

                text_currentItem           = text_current + text_dash + text_item,
                text_currentlyPlayingVideo = text_current + 'ly-playing-' + text_item,

                /**
                 * What item is this gallery currently playing?
                 */
                getCurrentVideoId = function (galleryId) {

                    return get(galleryId, text_currentItem);
                },

                /**
                 * Is this gallery currently playing an item?
                 */
                isCurrentlyPlayingVideo = function (galleryId) {

                    return get(galleryId, text_currentlyPlayingVideo);
                },

                /**
                 * An item on the page has stopped.
                 */
                onVideoStop = function (e, data) {

                    set(data[text_galleryId], text_currentlyPlayingVideo, fawlse);
                },

                setItemIdAsCurrent = function (galleryId, itemId) {

                    set(data[galleryId], text_currentItem, itemId);
                },

                /**
                 * An item on the page has started.
                 */
                onVideoStart = function (e, data) {

                    /**
                     * Record the item as playing.
                     */
                    set(data[text_galleryId], text_currentlyPlayingVideo, troo);
                    set(data[text_galleryId], text_currentItem, data[text_itemId]);
                },

                /**
                 * Set an item as "current" for a gallery.
                 */
                onChangeVideo = function (event, data) {

                    setItemIdAsCurrent(data[text_galleryId], data[text_itemId]);
                },

                onNewGallery = function (event, data) {

                    var galleryId = data[text_galleryId],
                        sequence  = options.getOption(galleryId, 'sequence', []);

                    if (sequence.length > 0) {

                        setItemIdAsCurrent(galleryId, sequence[0]);
                    }

                    /**
                     * Record that we're *not* currently playing an item.
                     */
                    set(galleryId, text_currentlyPlayingVideo, fawlse);
                };

            subscribe(text_event_tx_itemChange,  onChangeVideo);
            subscribe(text_event_rx_itemStop,    onVideoStop);
            subscribe(text_event_rx_itemStart,   onVideoStart);
            subscribe(text_event_tx_galleryLoad, onNewGallery);

            return {

                getCurrentVideoId       : getCurrentVideoId,
                isCurrentlyPlayingVideo : isCurrentlyPlayingVideo
            };
        }()),

        /**
         * Keeps track of which page each gallery is on.
         */
        pageTracker = (function () {

            var data               = dataFacade,
                get                = data.get,
                set                = data.set,
                text_page          = 'page',
                parseIntOrZero     = langUtils.parseIntOrZero,
                pageFromQuery      = langUtils.getParameterByName(text_tubepress + text_underscore + text_page),
                pageFromQueryAsInt = parseIntOrZero(pageFromQuery),

                /**
                 * What page is this gallery on?
                 */
                getCurrentPageNumber = function (galleryId) {

                    var toReturn = get(galleryId, text_page);

                    return toReturn === '' ? 1 : toReturn;
                },

                getRealPageNum = function (candidate) {

                    var parsed = parseIntOrZero(candidate);

                    return parsed === 0 ? 1 : parsed;
                },

                onNewGallery = function (e, data) {

                    /**
                     * Save the current page.
                     */
                    set(data[text_galleryId], text_page, getRealPageNum(pageFromQueryAsInt));
                },

                onPageChange = function (event, data) {

                    var galleryId = data[text_galleryId],
                        newPage   = getRealPageNum(data[text_page]);

                    set(galleryId, text_page, newPage);
                };

            subscribe(text_event_tx_galleryLoad, onNewGallery);
            subscribe(text_event_tx_galleryPageChange, onPageChange);

            return {

                getCurrentPageNumber : getCurrentPageNumber
            };
        }()),

        asyncGalleryRegistrar = (function () {

            var register = function (galleryId, params) {

                var dataToPublish = {};

                dataToPublish[text_galleryId] = galleryId;
                dataToPublish[text_params]    = params;

                publish(text_event_tx_galleryLoad, dataToPublish);
            };

            return {

                register : register
            };
        }());

    /**
     * Assigns each gallery its ID from the load event.
     */
    (function () {

        var onGalleryLoad = function (e, data) {

            var galleryId = data[text_galleryId];

            dataFacade.set(galleryId, text_id, galleryId);
        };

        subscribe(text_event_tx_galleryLoad, onGalleryLoad);
    }());

    /**
     * Loads the Ajax pagination script, if required.
     */
    (function () {

        var onGalleryLoad = function (e, data) {

            var galleryId        = data[text_galleryId],
                isAjaxPagination = options.getOption(galleryId, 'ajaxPagination', fawlse),
                loadJs           = domInjector.loadJs;

            if (isAjaxPagination) {

                if (langUtils.hasOwnNestedProperty(tubePressJsConfig, text_urls, text_js, text_sys, text_ajaxPagination)) {

                    loadJs(tubePressJsConfig[text_urls][text_js][text_sys][text_ajaxPagination]);

                } else {

                    loadJs('/src/core/pro/web/js/' + text_ajaxPagination + text_dot + text_js);
                }
            }
        };

        subscribe(text_event_tx_galleryLoad, onGalleryLoad);
    }());

    /**
     * Handles fluid thumbs.
     *
     * http://www.sohtanaka.com/web-design/smart-columns-w-css-jquery/
     */
    (function () {

        var galleryIds      = [],
            floor           = Math.floor,
            text_first      = 'first',
            text_100percent = '100%',
            text_width      = 'width',
            text_px         = 'px',

            endswith = function (needle, haystack) {

                return haystack.indexOf(needle, haystack.length - needle.length) !== -1;
            },

            getRealWidth = function (element) {

                var width = element.css(text_width);

                /**
                 * Use specified width, if available.
                 */
                if (width && endswith(text_px, width)) {

                    return width.replace(text_px, '');
                }

                /**
                 * Fallback to jQuery
                 */
                return element.width();
            },

            /**
             * Get the thumbnail width. Usually this is just a static thumbnail
             * image, but *may* be an actual embed or something like that.
             *
             * Fallback value is 120.
             */
            getThumbWidth = function (galleryId) {

                var firstModernThumbSelector = selectors.getOutermostSelectorModern(galleryId) + text_space +
                    text_dot_js_dash + text_tubepress_dash + text_fluid + text_dash + text_thumb + text_dash + 'reference:' + text_first,
                    firstModernThumb         = jquery(firstModernThumbSelector),
                    thumbArea,
                    firstVisualElement,
                    width;

                /**
                 * Handle modern themes first.
                 */
                if (firstModernThumb.length > 0) {

                    return getRealWidth(firstModernThumb);
                }

                thumbArea          = selectors.getThumbAreaElement(galleryId);
                firstVisualElement = thumbArea.find('img:' + text_first);
                width              = 120;

                if (firstVisualElement.length === 0) {

                    firstVisualElement = thumbArea.find(text_div + text_dot + text_tubepress + text_underscore + text_thumb +
                        ':' + text_first + ' > ' + text_div + text_dot + text_tubepress + '_embed');

                    if (firstVisualElement.length === 0) {

                        return width;
                    }
                }

                return getRealWidth(firstVisualElement);
            },

            makeThumbsFluid = function (galleryId) {

                selectors.getThumbAreaElement(galleryId).css({ 'width' : text_100percent });

                var columnWidth     = getThumbWidth(galleryId),
                    gallery         = selectors.getThumbAreaElement(galleryId),
                    galleryWidth    = gallery.width(),
                    colNum          = floor(galleryWidth / columnWidth),
                    newThumbWidth   = floor(galleryWidth / colNum),
                    legacyThumbsSel = text_dot + text_tubepress + text_underscore + text_thumb,
                    modernThumbsSel = text_dot_js_dash + text_tubepress_dash + text_fluid + text_dash + text_thumb + text_dash + 'adjustable',
                    thumbs          = gallery.find(modernThumbsSel).add(gallery.find(legacyThumbsSel)),
                    dataToPublish   = {},
                    cssOptions      = {};

                cssOptions[text_width] = text_100percent;
                gallery.css(cssOptions);

                cssOptions[text_width] = galleryWidth;
                gallery.css(cssOptions);

                cssOptions[text_width] = newThumbWidth;
                thumbs.css(cssOptions);

                gallery.data(text_fluid + text_underscore + text_thumbs + '_applied', troo);
                dataToPublish[text_galleryId] = galleryId;
                dataToPublish.newThumbWidth   = newThumbWidth;
                publish(text_eventPrefix_gallery + text_fluidThumbs, dataToPublish);
            },

            /**
             * Callback for thumbnail loads.
             */
            onNewGalleryOrThumbs = function (e, data) {

                var galleryId = data[text_galleryId];

                /* fluid thumbs if we need it */
                if (options.getOption(galleryId, text_fluidThumbs, fawlse)) {

                    if (jquery.inArray(galleryId, galleryIds) === -1) {

                        galleryIds.push(galleryId);
                    }

                    makeThumbsFluid(galleryId);
                }
            },

            /**
             * On window resize.
             */
            onWindowResize = function (e) {

                var index  = 0,
                    length = galleryIds.length;

                for (index; index < length; index += 1) {

                    onNewGalleryOrThumbs(e, galleryIds[index]);
                }
            };

        subscribe(text_event_rx_galleryNewThumbs + text_space + text_event_tx_galleryLoad, onNewGalleryOrThumbs);
        subscribe(text_tubepress + '.window.resize', onWindowResize);
    }());

    /**
     * Handles thumbnail clicks for legacy themes.
     */
    (function () {

        /**
         * Parse the gallery ID from the "rel" attribute.
         */
        var getGalleryIdFromRelSplit = function (relSplit) {

                return relSplit[3];
            },

            /**
             * Parse the item ID from the "rel" attribute.
             */
            getVideoIdFromIdAttr = function (id) {

                var end = id.lastIndexOf(text_underscore);

                return id.substring(16, end);
            },

            /**
             * Click listener callback.
             */
            clickListener = function () {

                var rel_split     = jquery(this).attr('rel').split(text_underscore),
                    itemId       = getVideoIdFromIdAttr(jquery(this).attr(text_id)),
                    dataToPublish = {};

                dataToPublish[text_galleryId] = getGalleryIdFromRelSplit(rel_split);
                dataToPublish[text_itemId]   = itemId;

                publish(text_event_tx_itemChange, dataToPublish);
            },

            /**
             * Callback for thumbnail loads.
             */
            onNewGalleryOrThumbs = function (e, data) {

                /* add a click handler to each link in this gallery */
                jquery(text_hash + text_tubepress + text_un_gallery_un + data[text_galleryId] + " a[id^='" + text_tubepress + "_']").click(clickListener);
            };

        subscribe(text_event_rx_galleryNewThumbs + text_space + text_event_tx_galleryLoad, onNewGalleryOrThumbs);
    }());

    /**
     * Handles thumbnail clicks for modern themes.
     */
    (function () {

        var findSuffix = function (element, prefix) {

                var classes = element.attr(text_class).split(/\s+/),
                    i = 0;

                for (i; i < classes.length; i += 1) {

                    if (classes[i].indexOf(prefix) === 0) {

                        return classes[i].replace(prefix, '');
                    }
                }

                return null;
            },

            modernClickListener = function (e) {

                var clicked       = jquery(e.currentTarget),
                    itemId        = findSuffix(clicked, text_js_dash + text_tubepress_dash + text_item + text_id + text_dash),
                    dataToPublish = {};

                dataToPublish[text_galleryId] = jquery.data(this, text_id);
                dataToPublish[text_itemId]    = itemId;

                publish(text_event_tx_itemChange, dataToPublish);
            },

            /**
             * Callback for thumbnail loads.
             */
            onNewGallery = function (e, data) {

                var galleryId = data[text_galleryId],
                    selector  = selectors.getOutermostSelectorModern(galleryId),
                    gallery   = selectors.getOutermostElement(galleryId);

                /* add a click handler to each link in this gallery */
                jquery(selector).on('click', text_dot_js_dash + text_tubepress_dash + 'invoker', jquery.proxy(modernClickListener, gallery));
            };

        subscribe(text_event_tx_galleryLoad, onNewGallery);
    }());

    /**
     * Handles invoking and populating the new player when an item change is requested.
     */
    (function () {

            /**
             * Load up a TubePress player with the given item ID.
             */
        var onNewVideoRequested = function (e, data) {

                var galleryId  = data[text_galleryId],
                    itemId     = data[text_itemId],
                    playerName = options.getOption(galleryId, text_player + 'Location', '?'),

                    /**
                     * The data coming back should have the following keys:
                     * 
                     * item
                     * html
                     */
                    onPlayerHtmlReceived = function (data) {

                        var dataToPublish = {};

                        dataToPublish[text_item]      = data[text_item];
                        dataToPublish[text_html]      = data[text_html];
                        dataToPublish[text_galleryId] = galleryId;
                        dataToPublish[text_itemId]    = itemId;

                        publish(text_eventPrefix_playerPopulate + playerName, dataToPublish);
                    },

                    dataToSend = {},
                    method,
                    dataToPublish = {};

                dataToSend[text_tubepress + text_underscore + 'action']  = text_player + 'Html';
                dataToSend[text_tubepress + text_underscore + text_item] = itemId;
                dataToSend[text_tubepress + text_underscore + 'options'] = options.getEphemeralOptions(galleryId);

                /** Announce we're gonna invoke the player... */
                dataToPublish[text_itemId]     = itemId;
                dataToPublish[text_galleryId]  = galleryId;
                publish(text_eventPrefix_playerInvoke + playerName, dataToPublish);

                /** Go fetch the HTML for it. */
                method = options.getOption(galleryId, 'httpMethod', 'GET');
                tubepress.Ajax.Executor.get(method, environment.getAjaxEndpointUrl(), dataToSend, onPlayerHtmlReceived, 'json');
            };

        /** When a user clicks a thumbnail... */
        subscribe(text_event_tx_itemChange, onNewVideoRequested);
    }());

    /**
     * Handles pagination clicks for legacy themes.
     */
    (function () {

        var handlePaginationClick = function (anchor, galleryId) {

                var page          = anchor.data(text_page),
                    dataToPublish = {};

                dataToPublish[text_galleryId] = galleryId;
                dataToPublish[text_page]      = page;

                publish(text_event_tx_galleryPageChange, dataToPublish);
            },

            onNewGalleryOrThumbs = function (event, data) {

                var galleryId = data[text_galleryId],
                    pagationClickCallback = function () {

                        handlePaginationClick(jquery(this), galleryId);

                        if (options.getOption(galleryId, text_ajaxPagination, fawlse)) {

                            //prevent default click action
                            event.preventDefault();
                            return fawlse;
                        }

                        return troo;
                    },
                    selectorPrefix = text_hash + text_tubepress + text_underscore + text_gallery + text_underscore + galleryId + text_space;

                /**
                 * Modern themes will use div.tubepress-pagination, but there are still lots of themes
                 * that have div.pagination.
                 */
                jquery(selectorPrefix + text_div + text_dot + 'pagination a').click(pagationClickCallback);
            };

        subscribe(text_event_rx_galleryNewThumbs + text_space + text_event_tx_galleryLoad, onNewGalleryOrThumbs);

    }());

    /**
     * Handles pagination clicks for modern themes.
     */
    (function () {

    }());

    tubepress.AsyncUtil.processQueueCalls('tubePressGalleryRegistrar', asyncGalleryRegistrar);

    /**
     * Make this available via the primary TubePress object.
     */
    tubepress.Gallery = {

        Selectors   : selectors,
        Options     : options,
        NowShowing  : nowShowing,
        PageTracker : pageTracker
    };

    /**
     * Signal that gallery.js has been loaded completely.
     */
    tubepress.Beacon.publish(text_tubepress + text_dot + text_js + text_dot + text_sys + text_dot + text_gallery);

}(jQuery, window, TubePress));