/**!
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 *
 * @author Eric D. Hough (eric@tubepress.org)
 */

var TubePressGallery = (function (jquery, win, tubepress) {

    /** http://ejohn.org/blog/ecmascript-5-strict-mode-json-and-more/ */
    'use strict';

    var text_tubepress               = 'tubepress',
        text_eventPrefix_gallery     = text_tubepress + '.thumbgallery.',
        text_eventPrefix_events      = text_tubepress + '.embedded.',
        text_eventPrefix_players     = text_tubepress + '.players.',
        text_event_playerPopulate    = text_eventPrefix_players + 'populate',
        text_event_playerInvoke      = text_eventPrefix_players + 'invoke',
        text_event_newGallery        = text_eventPrefix_gallery + 'newgallery',
        text_event_newThumbs         = text_eventPrefix_gallery + 'newthumbs',
        text_event_galleryPageChange = text_eventPrefix_gallery + 'requestpagechange',
        text_event_galleryNewVideo   = text_eventPrefix_gallery + 'requestnewvideo',
        text_event_embeddedStart     = text_eventPrefix_events + 'start',
        text_event_embeddedStop      = text_eventPrefix_events + 'stop',
        beacon                       = tubepress.Beacon,
        subscribe                    = beacon.subscribe,
        publish                      = beacon.publish,
        langUtils                    = tubepress.Lang.Utils,
        environment                  = tubepress.Environment,

        /**
         * Keeps state for any gallery loaded on the page.
         */
        galleryRegistry = (function () {

            var internalRegistry = {},
                nvpMap           = 'nvpMap',
                jsMap            = 'jsMap',
                page             = 'page',
                currentVideoId   = 'currentVideoId',
                isPlayingNow     = 'playingNow',

                parseIntOrZero = langUtils.parseIntOrZero,
                text_player    = 'player',
                text_embedded  = 'embedded',
                subscribe      = beacon.subscribe,

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
                    return isRegistered(galleryId) ? internalRegistry[galleryId][page] : undefined;
                },

                /**
                 * What video is this gallery currently playing?
                 */
                getCurrentVideoId = function (galleryId) {

                    return isRegistered(galleryId) ? internalRegistry[galleryId][currentVideoId] : undefined;
                },

                /**
                 * Does the gallery use Ajax pagination?
                 */
                isAjaxPagination = function (galleryId) {

                    return internalGet(galleryId, jsMap, 'ajaxPagination');
                },

                /**
                 * Does the gallery use auto-next?
                 */
                isAutoNext = function (galleryId) {

                    return internalGet(galleryId, jsMap, 'autoNext');
                },

                /**
                 * Is this gallery currently playing a video?
                 */
                isCurrentlyPlayingVideo = function (galleryId) {

                    return isRegistered(galleryId) ? internalRegistry[galleryId][isPlayingNow] : false;
                },

                /**
                 * Does the gallery use fluid thumbs?
                 */
                isFluidThumbs = function (galleryId) {

                    return internalGet(galleryId, jsMap, 'fluidThumbs');
                },

                /**
                 * What's the embedded height for the video player of this gallery?
                 */
                getEmbeddedHeight = function (galleryId) {

                    return internalGet(galleryId, nvpMap, text_embedded + 'Height');
                },

                /**
                 * What's the embedded width for the video player of this gallery?
                 */
                getEmbeddedWidth = function (galleryId) {

                    return internalGet(galleryId, nvpMap, text_embedded + 'Width');
                },

                /**
                 * Which HTTP method (GET or POST) does this gallery want to use?
                 */
                getHttpMethod = function (galleryId) {

                    return internalGet(galleryId, jsMap, 'httpMethod');
                },

                getNvpMap = function (galleryId) {

                    return internalRegistry[galleryId][nvpMap];
                },

                /**
                 * What's the gallery's player location name?
                 */
                getPlayerLocationName = function (galleryId) {

                    return internalGet(galleryId, nvpMap, text_player + 'Location');
                },

                /**
                 * Where is the JS init code for this player?
                 */
                getPlayerJsUrl = function (galleryId) {

                    return internalGet(galleryId, jsMap, text_player + 'JsUrl');
                },

                /**
                 * Does this player produce HTML?
                 */
                getPlayerProducesHtml = function (galleryId) {

                    return internalGet(galleryId, jsMap, text_player + 'LocationProducesHtml');
                },

                /**
                 * What's the sequence of videos for this gallery?
                 */
                getSequence = function (galleryId) {

                    return internalGet(galleryId, jsMap, 'sequence');
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
                onNewGallery = function (event, galleryId, params) {

                    var currentPage = langUtils.getParameterByName(text_tubepress + '_page'),
                        pageAsInt   = parseIntOrZero(currentPage),
                        sequence;

                    /** Save the params. */
                    internalRegistry[galleryId] = params;

                    /**
                     * Save the current page.
                     */
                    internalRegistry[galleryId][page] = pageAsInt === 0 ? 1 : pageAsInt;

                    /**
                     * Record that we're *not* currently playing a video.
                     */
                    internalRegistry[galleryId][isPlayingNow] = false;

                    /**
                     * If this gallery has a sequence,
                     * save the first video as the "current" video.
                     */
                    sequence = getSequence(galleryId);

                    if (sequence) {

                        internalRegistry[galleryId][currentVideoId] = sequence[0];
                    }
                },

                onPageChange = function (event, galleryId, newPage) {

                    if (isRegistered(galleryId)) {

                        var asInt = parseIntOrZero(newPage);

                        internalRegistry[galleryId][page] = asInt === 0 ? 1 : asInt;
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
                 * Find the gallery with the given video loaded up.
                 */
                findGalleryIdWithVideoIdAsCurrent = function (videoId) {

                    var test = function (galleryId) {

                        return internalRegistry[galleryId][currentVideoId] === videoId;
                    };

                    return findGalleryThatMatchesTest(test);
                },

                /**
                 * Find the gallery that is currently playingcurrentVideoId the given video.
                 */
                findGalleryIdCurrentlyPlayingVideo = function (videoId) {

                    var test = function (galleryId) {

                        var gall               = internalRegistry[galleryId],
                            isCurrentlyPlaying = gall[currentVideoId] === videoId && gall[isCurrentlyPlayingVideo];

                        if (isCurrentlyPlaying) {

                            return galleryId;
                        }

                        return false;
                    };

                    return findGalleryThatMatchesTest(test);
                },

                /**
                 * A video on the page has stopped.
                 */
                onPlaybackStopped = function (e, videoId) {

                    var matchingGalleryId = findGalleryIdCurrentlyPlayingVideo(videoId);

                    /**
                     * If we don't have a gallery assigned to this video, we don't really care.
                     */
                    if (!matchingGalleryId) {

                        return;
                    }

                    /**
                     * Record the video as not playing.
                     */
                    internalRegistry[matchingGalleryId][isCurrentlyPlayingVideo] = false;
                },

                /**
                 * A video on the page has started.
                 */
                onPlaybackStarted = function (e, videoId) {

                    var matchingGalleryId = findGalleryIdWithVideoIdAsCurrent(videoId);

                    /**
                     * If we don't have a gallery assigned to this video, we don't really care.
                     */
                    if (!matchingGalleryId) {

                        return;
                    }

                    /**
                     * Record the video as playing.
                     */
                    internalRegistry[matchingGalleryId][isCurrentlyPlayingVideo] = true;
                    internalRegistry[matchingGalleryId][currentVideoId]          = videoId;
                },

                /**
                 * Set a video as "current" for a gallery.
                 */
                onNewVideoRequested = function (event, galleryId, videoId) {

                    if (isRegistered(galleryId)) {

                        internalRegistry[galleryId][currentVideoId] = videoId;
                    }
                };

            subscribe(text_event_newGallery, onNewGallery);

            subscribe(text_event_galleryPageChange, onPageChange);

            subscribe(text_event_galleryNewVideo, onNewVideoRequested);

            subscribe(text_event_embeddedStop, onPlaybackStopped);

            subscribe(text_event_embeddedStart, onPlaybackStarted);

            return {

                isAjaxPagination                   : isAjaxPagination,
                isAutoNext                         : isAutoNext,
                isCurrentlyPlayingVideo            : isCurrentlyPlayingVideo,
                isFluidThumbs                      : isFluidThumbs,
                isRegistered                       : isRegistered,
                findGalleryIdCurrentlyPlayingVideo : findGalleryIdCurrentlyPlayingVideo,
                getCurrentPageNumber               : getCurrentPageNumber,
                getCurrentVideoId                  : getCurrentVideoId,
                getEmbeddedHeight                  : getEmbeddedHeight,
                getEmbeddedWidth                   : getEmbeddedWidth,
                getHttpMethod                      : getHttpMethod,
                getNvpMap                          : getNvpMap,
                getPlayerLocationName              : getPlayerLocationName,
                getPlayerLocationProducesHtml      : getPlayerProducesHtml,
                getPlayerJsUrl                     : getPlayerJsUrl,
                getSequence                        : getSequence,
                getThumbAreaSelector               : getThumbAreaSelector
            };
        }()),

        asyncGalleryRegistrar = (function () {

            var init = function (galleryId, params) {

                publish(text_event_newGallery, [ galleryId, params ]);
            };

            return {

                init : init
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
                    colWrap         = gallery.width(),
                    colNum          = floor(colWrap / columnWidth),
                    colFixed        = floor(colWrap / colNum),
                    thumbs          = jquery(gallerySelector + ' div.' + text_tubepress + '_thumb');

                gallery.css({ 'width' : '100%'});
                gallery.css({ 'width' : colWrap });
                thumbs.css({ 'width' : colFixed});
            },

            /**
             * Callback for thumbnail loads.
             */
            onNewGalleryOrThumbs = function (e, galleryId) {

                /* fluid thumbs if we need it */
                if (galleryRegistry.isFluidThumbs(galleryId)) {

                    makeThumbsFluid(galleryId);
                }
            };

        subscribe(text_event_newThumbs + ' ' + text_event_newGallery, onNewGalleryOrThumbs);
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

                publish(text_event_galleryNewVideo, [ galleryId, videoId ]);
            },

            /**
             * Callback for thumbnail loads.
             */
            onNewGalleryOrThumbs = function (e, galleryId) {

                /* add a click handler to each link in this gallery */
                jquery('#' + text_tubepress + '_gallery_' + galleryId + " a[id^='" + text_tubepress + "_']").click(clickListener);
            };

        subscribe(text_event_newThumbs + ' ' + text_event_newGallery, onNewGalleryOrThumbs);

    }());

    /**
     * Handles player-related functionality (popup, Shadowbox, etc)
     */
    (function () {

        /**
         * Find the player required for a gallery and load the JS.
         */
        var onNewGalleryLoaded = function (e, galleryId) {

                var path = galleryRegistry.getPlayerJsUrl(galleryId);

                /*
                 * Load this player's JS, if needed.
                 */
                tubepress.DomInjector.loadJs(path);
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

                        publish(text_event_playerPopulate, [ playerName, title, html, height, width, videoId, galleryId ]);
                    },

                    dataToSend = {

                        'action'          : 'playerHtml',
                        'tubepress_video' : videoId
                    },

                    url = environment.getBaseUrl() + 'src/main/php/scripts/ajaxEndpoint.php',
                    method;

                /**
                 * Add the NVPs for TubePress to the data.
                 */
                jquery.extend(dataToSend, nvpMap);

                /** Announce we're gonna invoke the player... */
                publish(text_event_playerInvoke, [ playerName, height, width, videoId, galleryId ]);

                /** If this player requires population, go fetch the HTML for it. */
                if (galleryRegistry.getPlayerLocationProducesHtml(galleryId)) {

                    method = galleryRegistry.getHttpMethod(galleryId);

                    /* ... and fetch the HTML for it */
                    tubepress.Ajax.Executor.get(method, url, dataToSend, callback, 'json');
                }
            };

        /** When we see a new gallery... */
        subscribe(text_event_newGallery, onNewGalleryLoaded);

        /** When a user clicks a thumbnail... */
        subscribe(text_event_galleryNewVideo, onNewVideoRequested);
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
                };

                jquery('#' + text_tubepress + '_gallery_' + galleryId + ' div.pagination a').click(pagationClickCallback);
            };

        subscribe(text_event_newThumbs + ' ' + text_event_newGallery, onNewGalleryOrThumbs);
    }());

    return {

        AysncRegistrar : asyncGalleryRegistrar,
        Registry       : galleryRegistry
    };

}(jQuery, window, TubePress));

TubePress.AsyncUtil.processQueueCalls('tubePressGallery', TubePressGallery.AysncRegistrar);