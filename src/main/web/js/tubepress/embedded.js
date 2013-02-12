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

var TubePressEmbedded = (function (jquery, win, tubepress) {

    /** http://ejohn.org/blog/ecmascript-5-strict-mode-json-and-more/ */
    'use strict';

    var events      = tubepress.Events,
        beacon      = tubepress.Beacon,
        publish     = beacon.publish,
        subscribe   = beacon.subscribe,
        langUtils   = tubepress.LangUtils,
        domInjector = tubepress.DomInjector,
        logger      = tubepress.Logger,

        /**
         * Utilities for the embedded APIs.
         */
        embeddedApiUtils = (function () {

            var

                /** These variable declarations aide in compression. */
                embeddedEvents = events.EMBEDDED,

                /**
                 * Helper method to trigger events on jQuery(document).
                 */
                triggerEvent = function (eventName, videoId) {

                    publish(eventName, [ videoId ]);
                },

                /**
                 * A video has started.
                 */
                fireVideoStarted = function (videoId) {

                    triggerEvent(embeddedEvents.PLAYBACK_STARTED, videoId);
                },

                /**
                 * A video has stopped.
                 */
                fireVideoStopped = function (videoId) {

                    triggerEvent(embeddedEvents.PLAYBACK_STOPPED, videoId);
                },

                /**
                 * A video is buffering.
                 */
                fireVideoBuffering = function (videoId) {

                    triggerEvent(embeddedEvents.PLAYBACK_BUFFERING, videoId);
                },

                /**
                 * A video has paused.
                 */
                fireVideoPaused = function (videoId) {

                    triggerEvent(embeddedEvents.PLAYBACK_PAUSED, videoId);
                },

                /**
                 * A video has encountered an error.
                 */
                fireVideoError = function (videoId) {

                    triggerEvent(embeddedEvents.PLAYBACK_ERROR, videoId);
                };

            return {

                fireVideoError     : fireVideoError,
                fireVideoPaused    : fireVideoPaused,
                fireVideoBuffering : fireVideoBuffering,
                fireVideoStopped   : fireVideoStopped,
                fireVideoStarted   : fireVideoStarted
            };

        }()),

        youTubeIframeApi = (function () {

            var

                /** These variable declarations aide in compression. */
                youTubePrefix = 'tubepress-youtube-player-',
                isDef         = langUtils.isDefined,

                isLoadingApi     = false,
                youTubePlayers   = {},
                youTubeIdPattern = /[a-z0-9\-_]{11}/i,

                /**
                 * Is the given video ID from YouTube?
                 */
                isYouTubeVideoId = function (videoId) {

                    return youTubeIdPattern.test(videoId);
                },

                /**
                 * Pulls out the video ID from a YouTube event.
                 */
                getVideoIdFromYouTubeEvent = function (event) {

                    var domId  = event.target.a.id,
                        vId    = domId.replace(youTubePrefix, ''),
                        player = youTubePlayers[vId],

                        url,
                        loadedId,
                        ampersandPosition;

                    //noinspection JSUnresolvedVariable
                    if (!jquery.isFunction(player.getVideoUrl)) {

                        return null;
                    }

                    //noinspection JSUnresolvedFunction
                    url               = player.getVideoUrl();
                    loadedId          = url.split('v=')[1];
                    ampersandPosition = loadedId.indexOf('&');

                    if (ampersandPosition !== -1) {

                        loadedId = loadedId.substring(0, ampersandPosition);
                    }

                    return loadedId;
                },

                /**
                 * Is the YouTube API available yet?
                 */
                isYouTubeApiAvailable = function () {

                    //noinspection JSUnresolvedVariable
                    return isDef(win.YT) && isDef(win.YT.Player);
                },

                /**
                 * Load the YouTube API, if necessary.
                 */
                loadYouTubeApi = function () {

                    if (isLoadingApi || isYouTubeApiAvailable()) {

                        return;
                    }

                    isLoadingApi = true;

                    domInjector.loadJs(win.location.protocol + '//www.youtube.com/player_api');
                },

                /**
                 * The YouTube player will call this method when a player event
                 * fires.
                 */
                onYouTubeStateChange = function (event) {

                    //noinspection JSUnresolvedVariable
                    var videoId     = getVideoIdFromYouTubeEvent(event),
                        eventData   = event.data,
                        playerState = YT.PlayerState;

                    /**
                     * If we can't parse the event, just bail.
                     */
                    if (videoId === null) {

                        return;
                    }

                    //noinspection JSUnresolvedVariable
                    switch (eventData) {

                    case playerState.PLAYING:

                        embeddedApiUtils.fireVideoStarted(videoId);
                        break;

                    case playerState.PAUSED:

                        embeddedApiUtils.fireVideoPaused(videoId);
                        break;

                    case playerState.ENDED:

                        embeddedApiUtils.fireVideoStopped(videoId);
                        break;

                    case playerState.BUFFERING:

                        embeddedApiUtils.fireVideoBuffering(videoId);
                        break;

                    case -1:

                        //YouTune "unstarted" event
                        //https://developers.google.com/youtube/iframe_api_reference#Events
                        break;

                    default:

                        if (logger.on()) {

                            logger.log('Unknown YT event');
                            logger.dir(event);
                        }

                        break;
                    }
                },

                /**
                 * YouTube will call this when a player hits an error.
                 */
                onYouTubeError = function (event) {

                    var videoId = getVideoIdFromYouTubeEvent(event);

                    if (videoId === null) {

                        return;
                    }

                    if (logger.on()) {

                        logger.log('YT error');
                        logger.dir(event);
                    }

                    embeddedApiUtils.fireVideoError(videoId);
                },

                /**
                 * Registers a YouTube player for use with the TubePress API.
                 */
                registerYouTubeVideo = function (videoId) {

                    /** Load 'er up. */
                    loadYouTubeApi();

                    /** This stuff will execute once the TubePress API is loaded. */
                    var callback = function () {

                        //noinspection JSUnresolvedFunction
                        youTubePlayers[videoId] = new YT.Player(youTubePrefix + videoId, {

                            events: {

                                'onError'       : onYouTubeError,
                                'onStateChange' : onYouTubeStateChange
                            }
                        });
                    };

                    langUtils.callWhenTrue(callback, isYouTubeApiAvailable, 250);
                },

                onNewVideoRegistered = function (event, videoId) {

                    if (isYouTubeVideoId(videoId)) {

                        registerYouTubeVideo(videoId);
                    }
                };

            subscribe(events.EMBEDDED.EMBEDDED_LOAD, onNewVideoRegistered);

            /**
             * It's necessary to make these functions public, as the YouTube API will need to call them
             */
            return {

                onYouTubeStateChange : onYouTubeStateChange,
                onYouTubeError       : onYouTubeError
            };
        }()),

        vimeoEmbeddedApi = (function () {

            var

                /** These variable declarations aide in compression. */
                vimeoPrefix = 'tubepress-vimeo-player-',

                /** Vimeo variables. */
                loadingVimeoApi = false,
                vimeoPlayers    = {},
                vimeoIdPattern  = /[0-9]+/,

                /**
                 * Is the given video ID from Vimeo?
                 */
                isVimeoVideoId = function (videoId) {

                    return vimeoIdPattern.test(videoId);
                },

                /**
                 * Pulls out the video ID from a Vimeo event.
                 */
                getVideoIdFromVimeoEvent = function (event) {

                    return event.replace(vimeoPrefix, '');
                },

                /**
                 * Is the Vimeo API available yet?
                 */
                isVimeoApiAvailable = function () {

                    return langUtils.isDefined(win.Froogaloop);
                },

                /**
                 * Load the Vimeo API, if necessary.
                 */
                loadVimeoApi = function () {

                    if (!loadingVimeoApi && !isVimeoApiAvailable()) {

                        loadingVimeoApi = true;

                        domInjector.loadJs(win.location.protocol + '//a.vimeocdn.com/js/froogaloop2.min.js');
                    }
                },

                /**
                 * Vimeo will call then when a video starts.
                 */
                onVimeoPlay = function (event) {

                    var videoId = getVideoIdFromVimeoEvent(event);

                    embeddedApiUtils.fireVideoStarted(videoId);
                },

                /**
                 * Vimeo will call then when a video pauses.
                 */
                onVimeoPause = function (event) {

                    var videoId = getVideoIdFromVimeoEvent(event);

                    embeddedApiUtils.fireVideoPaused(videoId);
                },

                /**
                 * Vimeo will call then when a video ends.
                 */
                onVimeoFinish = function (event) {

                    var videoId = getVideoIdFromVimeoEvent(event);

                    embeddedApiUtils.fireVideoStopped(videoId);
                },

                /**
                 * A Vimeo player is ready for action.
                 */
                onVimeoReady = function (playerId) {

                    var froog = vimeoPlayers[playerId];

                    froog.addEvent('play', onVimeoPlay);
                    froog.addEvent('pause', onVimeoPause);
                    froog.addEvent('finish', onVimeoFinish);
                },

                /**
                 * Registers a Vimeo player for use with the TubePress API.
                 */
                registerVimeoVideo = function (videoId) {

                    /** Load up the API. */
                    loadVimeoApi();

                    var playerId = vimeoPrefix + videoId,
                        iframe   = win.document.getElementById(playerId),

                        callback = function () {

                            /** Create and save the player. */
                            var froog = new Froogaloop(iframe);

                            vimeoPlayers[playerId] = froog;

                            froog.addEvent('ready', onVimeoReady);
                        };

                    /** Execute it when Vimeo is ready. */
                    langUtils.callWhenTrue(callback, isVimeoApiAvailable, 500);
                },

                onNewVideoRegistered = function (event, videoId) {

                    if (isVimeoVideoId(videoId)) {

                        registerVimeoVideo(videoId);
                    }
                };

            subscribe(events.EMBEDDED.EMBEDDED_LOAD, onNewVideoRegistered);

            return {

                onVimeoReady  : onVimeoReady,
                onVimeoPlay   : onVimeoPlay,
                onVimeoPause  : onVimeoPause,
                onVimeoFinish : onVimeoFinish
            };
        }()),

        tubePressEmbeddedApi = (function () {

            var text_tubePressEmbeddedApi = 'tubePressEmbeddedApi',
                queue                     = win[text_tubePressEmbeddedApi],
                entity                    = [],

                register = function (videoId) {

                    publish(events.EMBEDDED.EMBEDDED_LOAD, [ videoId ]);
                },

                onReady = function () {

                    //http://tmxcredit.com/tech-blog/understanding-javascript-asynchronous-apis/
                    var queueCall = function (callArray) {

                        var method = callArray[0],
                            args   = callArray.slice(1);

                        tubePressEmbeddedApi[method].apply(this, args);
                    };

                    if (langUtils.isDefined(queue)) {

                        // loop through our existing queue, calling methods in order
                        queue.reverse();

                        while (queue.length) {

                            entity = queue.pop();

                            queueCall(entity);
                        }
                    }

                    // over write the sampleQueue, replacing the push method with 'queueCall'
                    // this creates a globally accessible interface to your API through sampleQueue.push()
                    win[text_tubePressEmbeddedApi] = {

                        push : queueCall
                    };
                };

            subscribe('tubepress.embeddedapi.ready', onReady);

            return {

                register : register
            };
        }());

}(jQuery, window, TubePress));

TubePress.Beacon.publish('tubepress.embeddedapi.ready', []);