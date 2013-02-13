/**
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

    var jquery_isFunction = jquery.isFunction,
        text_tubepress    = 'tubepress',
        beacon            = tubepress.Beacon,
        subscribe         = beacon.subscribe,
        publish           = beacon.publish,
        langUtils         = tubepress.LangUtils,
        events            = tubepress.Events,
        environment       = tubepress.Environment,
        domInjector       = tubepress.DomInjector,

        /**
         * Gallery-related events.
         */
        galleryEvents = (function () {

            var xdot          = '.',
                xrequest      = 'request',
                xthumbgallery = text_tubepress + xdot + 'thumbgallery' + xdot;

            return {

                /**
                 * An entirely new gallery has entered the DOM.
                 *
                 * @arg string galleryId The identifier of this gallery.
                 * @arg object params    The parameters of this gallery.
                 */
                NEW_GALLERY_LOADED : xthumbgallery + 'newgallery',

                /** A new set of thumbnails has entered the DOM. */
                NEW_THUMBS_LOADED  : xthumbgallery + 'newthumbs',

                NEW_VIDEO_REQUESTED : xthumbgallery + xrequest + 'newvideo',

                NEXT_VIDEO_REQUESTED : xthumbgallery + xrequest + 'nextvideo',

                PREV_VIDEO_REQUESTED : xthumbgallery + xrequest + 'prevvideo',

                PAGE_CHANGE_REQUESTED : xthumbgallery + xrequest + 'pagechange'
            };
        }()),

        /**
         * Exposes a parse() function that wraps jQuery.parseJSON(), but adapts to
         * jQuery < 1.6.
         */
        jsonParser = (function () {

            var version      = jquery.fn.jquery,
                modernJquery = /1\.6|7|8|9\.[0-9]+/.test(version) !== false,
                parser,
                parse = function (msg) {

                    return parser(msg);
                };

            if (modernJquery) {

                parser = function (msg) {

                    return jquery.parseJSON(msg);
                };

            } else {

                parser = function (data) {

                    if (typeof data !== 'string' || !data) {

                        return null;
                    }

                    data = jquery.trim(data);


                    if (/^[\],:{}\s]*$/.test(data.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, "@")
                            .replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]")
                            .replace(/(?:^|:|,)(?:\s*\[)+/g, ""))) {

                        return (win.JSON && win.JSON.parse) ?

                                win.JSON.parse(data) : (new Function('return ' + data))();

                    } else {

                        throw 'Invalid JSON: ' + data;
                    }
                };
            }

            return {

                parse : parse
            };
        }()),

        /**
         * Handles styling DOM elements before and after loads.
         */
        loadStyler = (function () {

            /**
             * Fade to "white".
             */
            var applyLoadingStyle = function (targetDiv) {

                    jquery(targetDiv).fadeTo(0, 0.3);
                },

                /**
                 * Fade back to full opacity.
                 */
                removeLoadingStyle = function (targetDiv) {

                    jquery(targetDiv).fadeTo(0, 1);
                };

            return {

                applyLoadingStyle  : applyLoadingStyle,
                removeLoadingStyle : removeLoadingStyle
            };
        }()),

        /**
         * Various Ajax utilities.
         */
        ajaxExecutor = (function () {

            /**
             * Similar to jQuery's "load" but tolerates non-200 status codes.
             * https://github.com/jquery/jquery/blob/master/src/ajax.js#L168.
             */
            var load = function (method, url, targetDiv, selector, preLoadFunction, postLoadFunction) {

                    var completeCallback = function (res) {

                        var responseText = res.responseText,
                            html         = selector ? jquery('<div>').append(responseText).find(selector) : responseText;

                        jquery(targetDiv).html(html);

                        /* did the user supply a post-load function? */
                        if (jquery_isFunction(postLoadFunction)) {

                            postLoadFunction();
                        }
                    };

                    /** did the user supply a pre-load function? */
                    if (jquery_isFunction(preLoadFunction)) {

                        preLoadFunction();
                    }

                    jquery.ajax({

                        url      : url,
                        type     : method,
                        dataType : 'html',
                        complete : completeCallback
                    });
                },

                /**
                 * Similar to jQuery's "get" but ignores response code.
                 */
                get = function (method, url, data, success, dataType) {

                    jquery.ajax({

                        url      : url,
                        type     : method,
                        data     : data,
                        dataType : dataType,
                        complete : success
                    });

                },

                triggerDoneLoading = function (targetDiv) {

                    loadStyler.removeLoadingStyle(targetDiv);
                },

                /**
                 * Calls "load", but does some additional styling on the target element while it's processing.
                 */
                loadAndStyle = function (method, url, targetDiv, selector, preLoadFunction, postLoadFunction) {

                    /** one way or another, we're removing the loading style when we're done... */
                    var post = function () {

                        triggerDoneLoading(targetDiv);
                    };

                    loadStyler.applyLoadingStyle(targetDiv);

                    /** ... but maybe we want to do something else too */
                    if (jquery_isFunction(postLoadFunction)) {

                        post = function () {

                            triggerDoneLoading(targetDiv);
                            postLoadFunction();
                        };
                    }

                    /** do the load. do it! */
                    load(method, url, targetDiv, selector, preLoadFunction, post);
                };

            return {

                loadAndStyle : loadAndStyle,
                get          : get
            };
        }()),

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

                embeddedEvents = events.EMBEDDED,
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

                        domInjector.loadJs('src/main/web/js/' + text_tubepress + '/embedded.js');
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

            subscribe(galleryEvents.NEW_GALLERY_LOADED, onNewGallery);

            subscribe(galleryEvents.PAGE_CHANGE_REQUESTED, onPageChange);

            subscribe(galleryEvents.NEW_VIDEO_REQUESTED, onNewVideoRequested);

            subscribe(embeddedEvents.PLAYBACK_STOPPED, onPlaybackStopped);

            subscribe(embeddedEvents.PLAYBACK_STARTED, onPlaybackStarted);

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

                publish(galleryEvents.NEW_GALLERY_LOADED, [ galleryId, params ]);
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

        subscribe(galleryEvents.NEW_THUMBS_LOADED + ' ' + galleryEvents.NEW_GALLERY_LOADED, onNewGalleryOrThumbs);
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

                publish(galleryEvents.NEW_VIDEO_REQUESTED, [ galleryId, videoId ]);
            },

            /**
             * Callback for thumbnail loads.
             */
            onNewGalleryOrThumbs = function (e, galleryId) {

                /* add a click handler to each link in this gallery */
                jquery('#' + text_tubepress + '_gallery_' + galleryId + " a[id^='" + text_tubepress + "_']").click(clickListener);
            };

        subscribe(galleryEvents.NEW_THUMBS_LOADED + ' ' + galleryEvents.NEW_GALLERY_LOADED, onNewGalleryOrThumbs);

    }());

    /**
     * Handles player-related functionality (popup, Shadowbox, etc)
     */
    (function () {

        var
            /** These variable declarations help compression. */
            playerEvents     = tubepress.Events.PLAYERS,

            /**
             * Find the player required for a gallery and load the JS.
             */
            onNewGalleryLoaded = function (e, galleryId) {

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

                        var result = jsonParser.parse(data.responseText),
                            title  = result.title,
                            html   = result.html;

                        publish(playerEvents.PLAYER_POPULATE, [ playerName, title, html, height, width, videoId, galleryId ]);
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
                publish(playerEvents.PLAYER_INVOKE, [ playerName, videoId, galleryId, width, height ]);

                /** If this player requires population, go fetch the HTML for it. */
                if (galleryRegistry.getPlayerLocationProducesHtml(galleryId)) {

                    method = galleryRegistry.getHttpMethod(galleryId);

                    /* ... and fetch the HTML for it */
                    ajaxExecutor.get(method, url, dataToSend, callback, 'json');
                }
            };

        /** When we see a new gallery... */
        subscribe(galleryEvents.NEW_GALLERY_LOADED, onNewGalleryLoaded);

        /** When a user clicks a thumbnail... */
        subscribe(galleryEvents.NEW_VIDEO_REQUESTED, onNewVideoRequested);
    }());

    /**
     * Handles pagination clicks.
     */
    (function () {

        var handlePaginationClick = function (anchor, galleryId) {

                var page = anchor.data('page');

                publish(galleryEvents.PAGE_CHANGE_REQUESTED, [ galleryId, page ]);
            },

            onNewGalleryOrThumbs = function (event, galleryId) {

                var pagationClickCallback = function () {

                    handlePaginationClick(jquery(this), galleryId);
                };

                jquery('#' + text_tubepress + '_gallery_' + galleryId + ' div.pagination a').click(pagationClickCallback);
            };

        subscribe(galleryEvents.NEW_GALLERY_LOADED + ' ' + galleryEvents.NEW_THUMBS_LOADED, onNewGalleryOrThumbs);
    }());

    return {

        AysncRegistrar : asyncGalleryRegistrar,
        LoadStyler     : loadStyler,
        Registry       : galleryRegistry,
        Events         : events
    };

}(jQuery, window, TubePress));

TubePress.AsyncUtil.processQueueCalls('tubePressGallery', TubePressGallery.AysncRegistrar);