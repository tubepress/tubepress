/*!
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com).
 * This file is part of TubePress (http://tubepress.com).
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
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

    var text_tubepress                    = 'tubepress',
        text_eventPrefix_gallery          = text_tubepress + '.gallery.',
        text_eventPrefix_video            = text_tubepress + '.video.',
        text_eventPrefix_playerLocation   = text_tubepress + '.playerlocation.',
        text_event_playerLocationPopulate = text_eventPrefix_playerLocation + 'populate',
        text_event_playerLocationInvoke   = text_eventPrefix_playerLocation + 'invoke',
        text_event_galleryLoad            = text_eventPrefix_gallery + 'load',
        text_event_galleryNewThumbs       = text_eventPrefix_gallery + 'newthumbs',
        text_event_galleryPageChange      = text_eventPrefix_gallery + 'pagechange',
        text_event_galleryChangeVideo     = text_eventPrefix_gallery + 'changevideo',
        text_event_galleryNextVideo       = text_eventPrefix_gallery + 'nextvideo',
        text_event_galleryPreviousVideo   = text_eventPrefix_gallery + 'previousvideo',
        text_event_videoStart             = text_eventPrefix_video + 'start',
        text_event_videoStop              = text_eventPrefix_video + 'stop',
        text_urls                         = 'urls',
        text_sys                          = 'sys',
        text_js                           = 'js',
        beacon                            = tubepress.Beacon,
        subscribe                         = beacon.subscribe,
        publish                           = beacon.publish,
        langUtils                         = tubepress.Lang.Utils,
        environment                       = tubepress.Environment,
        domInjector                       = tubepress.DomInjector,
        coreJsPrefix                      = 'src/main/web/js',
        troo                              = true,
        fawlse                            = false,
        tubePressJsConfig                 = win.TubePressJsConfig,

        /**
         * Keeps state for any gallery loaded on the page.
         */
        galleryRegistry = (function () {

            var internalRegistry    = {},
                text_nvpMap         = 'nvpMap',
                text_jsMap          = 'jsMap',
                text_page           = 'page',
                text_currentVideoId = 'currentVideoId',
                text_playingNow     = 'playingNow',
                parseIntOrZero      = langUtils.parseIntOrZero,
                text_playerLocation = 'playerLocation',
                text_embedded       = 'embedded',
                subscribe           = beacon.subscribe,

                /**
                 * Have we heard about this gallery?
                 */
                isRegistered = function (galleryId) {

                    return langUtils.isDefined(internalRegistry[galleryId]);
                },

                /**
                 * Gets a property for the given gallery.
                 */
                internalGet = function (galleryId, jsOrNvp, property) {

                    return isRegistered(galleryId) ?

                            internalRegistry[galleryId][jsOrNvp][property] : null;
                },

                /**
                 * What page is this gallery on?
                 */
                getCurrentPageNumber = function (galleryId) {

                    //noinspection JSUnresolvedVariable
                    return isRegistered(galleryId) ? internalRegistry[galleryId][text_page] : undefined;
                },

                /**
                 * What video is this gallery currently playing?
                 */
                getCurrentVideoId = function (galleryId) {

                    return isRegistered(galleryId) ? internalRegistry[galleryId][text_currentVideoId] : undefined;
                },

                /**
                 * Does the gallery use Ajax pagination?
                 */
                isAjaxPagination = function (galleryId) {

                    return internalGet(galleryId, text_jsMap, 'ajaxPagination');
                },

                /**
                 * Does the gallery use auto-next?
                 */
                isAutoNext = function (galleryId) {

                    return internalGet(galleryId, text_jsMap, 'autoNext');
                },

                /**
                 * Is this gallery currently playing a video?
                 */
                isCurrentlyPlayingVideo = function (galleryId) {

                    return isRegistered(galleryId) ? internalRegistry[galleryId][text_playingNow] : fawlse;
                },

                /**
                 * Does the gallery use fluid thumbs?
                 */
                isFluidThumbs = function (galleryId) {

                    return internalGet(galleryId, text_jsMap, 'fluidThumbs');
                },

                /**
                 * What's the embedded height for the video player of this gallery?
                 */
                getEmbeddedHeight = function (galleryId) {

                    return internalGet(galleryId, text_nvpMap, text_embedded + 'Height');
                },

                /**
                 * What's the embedded width for the video player of this gallery?
                 */
                getEmbeddedWidth = function (galleryId) {

                    return internalGet(galleryId, text_nvpMap, text_embedded + 'Width');
                },

                /**
                 * Which HTTP method (GET or POST) does this gallery want to use?
                 */
                getHttpMethod = function (galleryId) {

                    return internalGet(galleryId, text_jsMap, 'httpMethod');
                },

                getJsMap = function (galleryId) {

                    return internalRegistry[galleryId][text_jsMap];
                },

                getNvpMap = function (galleryId) {

                    return internalRegistry[galleryId][text_nvpMap];
                },

                /**
                 * What's the gallery's player location name?
                 */
                getPlayerLocationName = function (galleryId) {

                    return internalGet(galleryId, text_nvpMap, text_playerLocation);
                },

                /**
                 * Where is the JS init code for this player?
                 */
                getPlayerLocationJsUrl = function (galleryId) {

                    return internalGet(galleryId, text_jsMap, text_playerLocation + 'JsUrl');
                },

                /**
                 * Does this player produce HTML?
                 */
                getPlayerLocationProducesHtml = function (galleryId) {

                    return internalGet(galleryId, text_jsMap, text_playerLocation + 'ProducesHtml');
                },

                /**
                 * What's the sequence of videos for this gallery?
                 */
                getSequence = function (galleryId) {

                    return internalGet(galleryId, text_jsMap, 'sequence');
                },

                /**
                 * Get the jQuery selector where the thumbs live.
                 *
                 * this is hard-coded - need to get rid of that.
                 */
                getThumbAreaSelector = function (galleryId) {

                    return '#' + text_tubepress + '_gallery_' + galleryId + '_thumbnail_area';
                },

                /**
                 * Performs gallery initialization on jQuery(document).ready().
                 */
                onGalleryLoad = function (event, galleryId, params) {

                    var currentPage         = langUtils.getParameterByName(text_tubepress + '_page'),
                        pageAsInt           = parseIntOrZero(currentPage),
                        text_ajaxPagination = 'ajaxPagination',
                        sequence;

                    /** Save the params. */
                    internalRegistry[galleryId] = params;

                    /**
                     * Save the current page.
                     */
                    internalRegistry[galleryId][text_page] = pageAsInt === 0 ? 1 : pageAsInt;

                    /**
                     * Record that we're *not* currently playing a video.
                     */
                    internalRegistry[galleryId][text_playingNow] = fawlse;

                    /**
                     * If this gallery has a sequence,
                     * save the first video as the "current" video.
                     */
                    sequence = getSequence(galleryId);

                    if (sequence) {

                        internalRegistry[galleryId][text_currentVideoId] = sequence[0];
                    }

                    if (isAjaxPagination(galleryId)) {

                        if (langUtils.hasOwnNestedProperty(tubePressJsConfig, text_urls, text_js, text_sys, text_ajaxPagination)) {

                            domInjector.loadJs(tubePressJsConfig[text_urls][text_js][text_sys][text_ajaxPagination]);

                        } else {

                            domInjector.loadJs(coreJsPrefix + '/' + text_ajaxPagination + '.js');
                        }
                    }
                },

                onPageChange = function (event, galleryId, newPage) {

                    if (isRegistered(galleryId)) {

                        var asInt = parseIntOrZero(newPage);

                        internalRegistry[galleryId][text_page] = asInt === 0 ? 1 : asInt;
                    }
                },

                /**
                 * Searches through our galleries for one that matches the given.
                 */
                findGalleryThatMatchesTest = function (test) {

                    var galleryId;

                    for (galleryId in internalRegistry) {

                        if (internalRegistry.hasOwnProperty(galleryId)) {

                            if (test(galleryId)) {

                                return galleryId;
                            }
                        }
                    }

                    return undefined;
                },

                /**
                 * Find the gallery that is currently playingcurrentVideoId the given video.
                 */
                findGalleryContainingVideoDomId = function (domId) {

                    var selector = '[id^="' + domId + '"]',

                        test = function (galleryId) {

                            var gall = jquery('#' + text_tubepress + '_gallery_' + galleryId);

                            if (!gall.length) {

                                //we couldn't find this gallery for some reason
                                return false;
                            }

                            return gall.find(selector).length > 0;
                        };

                    return findGalleryThatMatchesTest(test);
                },

                /**
                 * A video on the page has stopped.
                 */
                onVideoStop = function (e, videoId, domId) {

                    var matchingGalleryId = findGalleryContainingVideoDomId(domId);

                    /**
                     * If we don't have a gallery assigned to this video, we don't really care.
                     */
                    if (!matchingGalleryId) {

                        return;
                    }

                    /**
                     * Record the video as not playing.
                     */
                    internalRegistry[matchingGalleryId][isCurrentlyPlayingVideo] = fawlse;
                },

                /**
                 * A video on the page has started.
                 */
                onVideoStart = function (e, videoId, domId) {

                    var matchingGalleryId = findGalleryContainingVideoDomId(domId);

                    /**
                     * If we don't have a gallery assigned to this video, we don't really care.
                     */
                    if (!matchingGalleryId) {

                        return;
                    }

                    /**
                     * Record the video as playing.
                     */
                    internalRegistry[matchingGalleryId][isCurrentlyPlayingVideo] = troo;
                    internalRegistry[matchingGalleryId][text_currentVideoId]     = videoId;
                },

                /**
                 * Set a video as "current" for a gallery.
                 */
                onChangeVideo = function (event, galleryId, videoId) {

                    if (isRegistered(galleryId)) {

                        internalRegistry[galleryId][text_currentVideoId] = videoId;
                    }
                },

                findAllGalleryIds = function () {

                    var ids = [],
                        id;

                    //noinspection JSLint
                    for (id in internalRegistry) {

                        //noinspection JSUnfilteredForInLoop
                        ids.push(id);
                    }

                    return ids;
                };

            subscribe(text_event_galleryLoad, onGalleryLoad);

            subscribe(text_event_galleryPageChange, onPageChange);

            subscribe(text_event_galleryChangeVideo, onChangeVideo);

            subscribe(text_event_videoStop, onVideoStop);

            subscribe(text_event_videoStart, onVideoStart);

            return {

                isAjaxPagination                : isAjaxPagination,
                isAutoNext                      : isAutoNext,
                isCurrentlyPlayingVideo         : isCurrentlyPlayingVideo,
                isFluidThumbs                   : isFluidThumbs,
                isRegistered                    : isRegistered,
                findAllGalleryIds               : findAllGalleryIds,
                findGalleryContainingVideoDomId : findGalleryContainingVideoDomId,
                getCurrentPageNumber            : getCurrentPageNumber,
                getCurrentVideoId               : getCurrentVideoId,
                getEmbeddedHeight               : getEmbeddedHeight,
                getEmbeddedWidth                : getEmbeddedWidth,
                getHttpMethod                   : getHttpMethod,
                getJsMap                        : getJsMap,
                getNvpMap                       : getNvpMap,
                getPlayerLocationName           : getPlayerLocationName,
                getPlayerLocationProducesHtml   : getPlayerLocationProducesHtml,
                getPlayerLocationJsUrl          : getPlayerLocationJsUrl,
                getSequence                     : getSequence,
                getThumbAreaSelector            : getThumbAreaSelector
            };
        }()),

        asyncGalleryRegistrar = (function () {

            var register = function (galleryId, params) {

                publish(text_event_galleryLoad, [ galleryId, params ]);
            };

            return {

                register : register
            };
        }());

    /**
     * Handles fluid thumbs.
     *
     * http://www.sohtanaka.com/web-design/smart-columns-w-css-jquery/
     */
    (function () {

        var floor = Math.floor,

            getThumbAreaSelector = function (galleryId) {

                return galleryRegistry.getThumbAreaSelector(galleryId);
            },

            /**
             * Get the jQuery reference to where the thumbnails live.
             */
            getThumbArea = function (galleryId) {

                return jquery(getThumbAreaSelector(galleryId));
            },

            /**
             * Get the thumbnail width. Usually this is just a static thumbnail
             * image, but *may* be an actual embed or something like that.
             *
             * Fallback value is 120.
             */
            getThumbWidth = function (galleryId) {

                var thumbArea          = getThumbArea(galleryId),
                    firstVisualElement = thumbArea.find('img:first'),
                    width              = 120;

                if (firstVisualElement.length === 0) {

                    firstVisualElement = thumbArea.find('div.' + text_tubepress + '_thumb:first > div.' + text_tubepress + '_embed');

                    if (firstVisualElement.length === 0) {

                        return width;
                    }
                }

                width = firstVisualElement.attr('width');

                if (width) {

                    return width;
                }

                return firstVisualElement.width();
            },

            makeThumbsFluid = function (galleryId) {

                getThumbArea(galleryId).css({ 'width' : '100%' });

                var gallerySelector = getThumbAreaSelector(galleryId),
                    columnWidth     = getThumbWidth(galleryId),
                    gallery         = jquery(gallerySelector),
                    galleryWidth    = gallery.width(),
                    colNum          = floor(galleryWidth / columnWidth),
                    newThumbWidth   = floor(galleryWidth / colNum),
                    thumbs          = jquery(gallerySelector + ' div.' + text_tubepress + '_thumb');

                gallery.css({ 'width' : '100%'});
                gallery.css({ 'width' : galleryWidth });
                thumbs.css({ 'width' : newThumbWidth});

                gallery.data('fluid_thumbs_applied', true);
                beacon.publish(text_eventPrefix_gallery + 'fluidThumbs', [ galleryId, newThumbWidth ]);
            },

            /**
             * Callback for thumbnail loads.
             */
            onNewGalleryOrThumbs = function (e, galleryId) {

                /* fluid thumbs if we need it */
                if (galleryRegistry.isFluidThumbs(galleryId)) {

                    makeThumbsFluid(galleryId);
                }
            },

            /**
             * On window resize.
             */
            onWindowResize = function (e) {

                var ids    = galleryRegistry.findAllGalleryIds(),
                    index  = 0,
                    length = ids.length;

                for (index; index < length; index += 1) {

                    onNewGalleryOrThumbs(e, ids[index]);
                }
            };

        subscribe(text_event_galleryNewThumbs + ' ' + text_event_galleryLoad, onNewGalleryOrThumbs);
        subscribe(text_tubepress + '.window.resize', onWindowResize);
    }());

    /**
     * Handles thumbnail clicks.
     */
    (function () {

        /**
         * Parse the gallery ID from the "rel" attribute.
         */
        var getGalleryIdFromRelSplit = function (relSplit) {

            return relSplit[3];
        },

            /**
             * Parse the video ID from the "rel" attribute.
             */
            getVideoIdFromIdAttr = function (id) {

                var end = id.lastIndexOf('_');

                return id.substring(16, end);
            },

            /**
             * Click listener callback.
             */
            clickListener = function () {

                var rel_split = jquery(this).attr('rel').split('_'),
                    galleryId = getGalleryIdFromRelSplit(rel_split),
                    videoId   = getVideoIdFromIdAttr(jquery(this).attr('id'));

                publish(text_event_galleryChangeVideo, [ galleryId, videoId ]);
            },

            /**
             * Callback for thumbnail loads.
             */
            onNewGalleryOrThumbs = function (e, galleryId) {

                /* add a click handler to each link in this gallery */
                jquery('#' + text_tubepress + '_gallery_' + galleryId + " a[id^='" + text_tubepress + "_']").click(clickListener);
            };

        subscribe(text_event_galleryNewThumbs + ' ' + text_event_galleryLoad, onNewGalleryOrThumbs);

    }());

    /**
     * Handles player-related functionality (popup, Shadowbox, etc)
     */
    (function () {

        /**
         * Find the player required for a gallery and load the JS.
         */
        var onNewGalleryLoaded = function (e, galleryId) {

                var path = galleryRegistry.getPlayerLocationJsUrl(galleryId);

                /*
                 * Load this player's JS, if needed.
                 */
                domInjector.loadJs(path);
            },

            /**
             * Load up a TubePress player with the given video ID.
             */
            onNewVideoRequested = function (e, galleryId, videoId) {

                var playerName = galleryRegistry.getPlayerLocationName(galleryId),
                    height     = galleryRegistry.getEmbeddedHeight(galleryId),
                    width      = galleryRegistry.getEmbeddedWidth(galleryId),
                    nvpMap     = galleryRegistry.getNvpMap(galleryId),

                    callback   = function (data) {

                        var result = tubepress.Lang.JsonParser.parse(data.responseText),
                            title  = result.title,
                            html   = result.html;

                        publish(text_event_playerLocationPopulate, [ playerName, title, html, height, width, videoId, galleryId ]);
                    },

                    dataToSend = {

                        'action'          : 'playerHtml',
                        'tubepress_video' : videoId
                    },

                    method;

                /**
                 * Add the NVPs for TubePress to the data.
                 */
                jquery.extend(dataToSend, nvpMap);

                /** Announce we're gonna invoke the player... */
                publish(text_event_playerLocationInvoke, [ playerName, height, width, videoId, galleryId ]);

                /** If this player requires population, go fetch the HTML for it. */
                if (galleryRegistry.getPlayerLocationProducesHtml(galleryId)) {

                    method = galleryRegistry.getHttpMethod(galleryId);

                    /* ... and fetch the HTML for it */
                    tubepress.Ajax.Executor.get(method, environment.getAjaxEndpointUrl(), dataToSend, callback, 'json');
                }
            };

        /** When we see a new gallery... */
        subscribe(text_event_galleryLoad, onNewGalleryLoaded);

        /** When a user clicks a thumbnail... */
        subscribe(text_event_galleryChangeVideo, onNewVideoRequested);
    }());

    /**
     * Handles pagination clicks.
     */
    (function () {

        var handlePaginationClick = function (anchor, galleryId) {

                var page = anchor.data('page');

                publish(text_event_galleryPageChange, [ galleryId, page ]);
            },

            onNewGalleryOrThumbs = function (event, galleryId) {

                var pagationClickCallback = function () {

                    handlePaginationClick(jquery(this), galleryId);

                    if (galleryRegistry.isAjaxPagination(galleryId)) {

                        //prevent default click action
                        event.preventDefault();
                        return fawlse;
                    }

                    return troo;
                };

                jquery('#' + text_tubepress + '_gallery_' + galleryId + ' div.pagination a').click(pagationClickCallback);
            };

        subscribe(text_event_galleryNewThumbs + ' ' + text_event_galleryLoad, onNewGalleryOrThumbs);

    }());

    /**
     * Handles "next" and "previous" video requests.
     */
    (function () {

        /**
         * Go to the next video in the gallery.
         */
        var onNextVideoRequested = function (event, galleryId) {

            /** Get the gallery's sequence. This is an array of video ids. */
            var sequence  = galleryRegistry.getSequence(galleryId),
                vidId     = galleryRegistry.getCurrentVideoId(galleryId),
                index     = jquery.inArray(vidId.toString(), sequence),
                lastIndex = sequence ? sequence.length - 1 : index;

            /** Sorry, we don't know anything about this video id, or we've reached the end of the gallery. */
            if (index === -1 || index === lastIndex) {

                return;
            }

            /** Start the next video in line. */
            publish(text_event_galleryChangeVideo, [ galleryId, sequence[index + 1] ]);
        },

            /** Play the previous video in the gallery. */
            onPrevVideoRequested = function (event, galleryId) {

                /** Get the gallery's sequence. This is an array of video ids. */
                var sequence = galleryRegistry.getSequence(galleryId),
                    vidId    = galleryRegistry.getCurrentVideoId(galleryId),
                    index    = jquery.inArray(vidId.toString(), sequence);

                /** Sorry, we don't know anything about this video id, or we're at the start of the gallery. */
                if (index === -1 || index === 0) {

                    return;
                }

                /** Start the previous video in line. */
                publish(text_event_galleryChangeVideo, [ galleryId, sequence[index - 1] ]);
            };

        subscribe(text_event_galleryNextVideo, onNextVideoRequested);

        subscribe(text_event_galleryPreviousVideo, onPrevVideoRequested);

    }());

    /**
     * Handles auto-next.
     */
    (function () {

        /**
         * A video on the page has stopped.
         */
        var onVideoStop = function (e, video) {

                var galleryId = galleryRegistry.findGalleryContainingVideoDomId(video.domId);

                if (!galleryId) {

                    return;
                }

                if (galleryRegistry.isAutoNext(galleryId) && galleryRegistry.getSequence(galleryId)) {

                    /** Go to the next one! */
                    beacon.publish(text_event_galleryNextVideo, [ galleryId ]);
                }
            };

        /** We would like to be notified when a video ends, in the case of auto-next. */
        beacon.subscribe(text_event_videoStop, onVideoStop);

    }());

    tubepress.AsyncUtil.processQueueCalls('tubePressGalleryRegistrar', asyncGalleryRegistrar);

    /**
     * Make this available via the primary TubePress object.
     */
    tubepress.Gallery = {

        Registry : galleryRegistry
    };

    /**
     * Signal that gallery.js has been loaded completely.
     */
    tubepress.Beacon.publish(text_tubepress + '.js.sys.gallery');

}(jQuery, window, TubePress));