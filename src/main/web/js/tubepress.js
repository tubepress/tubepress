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
 * @author Bill Jackson (genalgo@tubepress.org)
 */

/*global jQuery, TubePressGlobalJsConfig, YT, Froogaloop, console */
/*jslint devel: true, browser: true, sloppy: false, white: true, maxerr: 50, indent: 4 */

/*!
 * $script.js Async loader & dependency manager
 * https://github.com/ded/script.js
 * (c) Dustin Diaz, Jacob Thornton 2011
 * License: MIT
 */
(function(a,b,c){typeof c["module"]!="undefined"&&c.module.exports?c.module.exports=b():typeof c["define"]!="undefined"&&c["define"]=="function"&&c.define.amd?define(a,b):c[a]=b()})("TubePress$cript",function(){function p(a,b){for(var c=0,d=a.length;c<d;++c)if(!b(a[c]))return j;return 1}function q(a,b){p(a,function(a){return!b(a)})}function r(a,b,i){function o(a){return a.call?a():d[a]}function t(){if(!--n){d[m]=1,l&&l();for(var a in f)p(a.split("|"),o)&&!q(f[a],o)&&(f[a]=[])}}a=a[k]?a:[a];var j=b&&b.call,l=j?b:i,m=j?a.join(""):b,n=a.length;return setTimeout(function(){q(a,function(a){if(h[a])return m&&(e[m]=1),h[a]==2&&t();h[a]=1,m&&(e[m]=1),s(!c.test(a)&&g?g+a+".js":a,t)})},0),r}function s(c,d){var e=a.createElement("script"),f=j;e.onload=e.onerror=e[o]=function(){if(e[m]&&!/^c|loade/.test(e[m])||f)return;e.onload=e[o]=null,f=1,h[c]=2,d()},e.async=1,e.src=c,b.insertBefore(e,b.firstChild)}var a=document,b=a.getElementsByTagName("head")[0],c=/^https?:\/\//,d={},e={},f={},g,h={},i="string",j=!1,k="push",l="DOMContentLoaded",m="readyState",n="addEventListener",o="onreadystatechange";return!a[m]&&a[n]&&(a[n](l,function t(){a.removeEventListener(l,t,j),a[m]="complete"},j),a[m]="loading"),r.get=s,r.order=function(a,b,c){(function d(e){e=a.shift(),a.length?r(e,d):r(e,b,c)})()},r.path=function(a){g=a},r.ready=function(a,b,c){a=a[k]?a:[a];var e=[];return!q(a,function(a){d[a]||e[k](a)})&&p(a,function(a){return d[a]})?b():!function(a){f[a]=f[a]||[],f[a][k](b),c&&c(e)}(a.join("|")),r},r},this)

/**
 * Logger!
 */
var TubePressLogger = (function () {

    'use strict';

    /**
     * Is the log on?
     */
    var isLoggingRequested        = location.search.indexOf('tubepress_debug=true') !== -1,
        windowConsole            = window.console,
        isLoggingAvailable        = windowConsole !== undefined,

        /**
         * The log is on if it's been enabled and requested.
         */
        isLoggingOn = function () {

            return isLoggingRequested && isLoggingAvailable;
        },

        /**
         * Output a message.
         */
        log = function (msg) {

            windowConsole.log(msg);
        },

        dir = function (obj) {

            windowConsole.dir(obj);
        };

    return {

        on    : isLoggingOn,
        log    : log,
        dir    : dir
    };
}()),

/**
 * Lightweight event bus for TubePress.
 *
 * jQuery Tiny Pub/Sub - v0.3 - 11/4/2010
 * http://benalman.com/
 *
 * Copyright (c) 2010 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */
TubePressBeacon = (function () {

    /** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
    'use strict';

    var bus         = jQuery({}),
        subscribe   = function () {

            bus.bind.apply(bus, arguments);
        },

        unsubscribe = function () {

            bus.unbind.apply(bus, arguments);
        },

        publish = function () {

            bus.trigger.apply(bus, arguments);
        };

    return {

        subscribe   : subscribe,
        unsubscribe : unsubscribe,
        publish     : publish
    };
}()),

/**
 * Events that TubePress fires.
 */
TubePressEvents = (function () {

    /** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
    'use strict';

    /*
     * These variable declarations aide in compression.
     */
    var xdot = '.',
        xtubepress = 'tubepress',
        xsequencing = xtubepress + xdot + 'sequencing' + xdot,
        xembedded = xtubepress + xdot + 'embedded' + xdot,
        xplayers = xtubepress + xdot + 'players' + xdot,
        xthumbgallery = xtubepress + xdot + 'thumbgallery' + xdot;

    return {

        SEQUENCING : {

            /** A gallery's primary video has changed. */
            GALLERY_VIDEO_CHANGE    : xsequencing + '1'
        },

        EMBEDDED : {

            /** An embedded video has been loaded. */
            EMBEDDED_LOAD            : xembedded + '1',

            /** Playback of a video started. */
            PLAYBACK_STARTED        : xembedded + '2',

            /** Playback of a video stopped. */
            PLAYBACK_STOPPED        : xembedded + '3',

            /** Playback of a video is buffering. */
            PLAYBACK_BUFFERING        : xembedded + '4',

            /** Playback of a video is paused. */
            PLAYBACK_PAUSED            : xembedded + '5',

            /** Playback of a video has errored out. */
            PLAYBACK_ERROR            : xembedded + '6'
        },

        PLAYERS : {

            /** A TubePress player is being invoked. */
            PLAYER_INVOKE            : xplayers + '1',

            /** A TubePress player is being populated. */
            PLAYER_POPULATE            : xplayers + '2'
        },

        THUMBGALLERY : {

            /** A new set of thumbnails has entered the DOM. */
            NEW_THUMBS_LOADED        : xthumbgallery + '1',

            /** An entirely new gallery has entered the DOM. */
            NEW_GALLERY_LOADED        : xthumbgallery + '2'
        }
    };
}()),

/**
 * Exposes a parse() function that wraps jQuery.parseJSON(), but adapts to
 * jQuery < 1.6.
 */
TubePressJson = (function () {

    'use strict';

    var jquery            = jQuery,
        version            = jquery.fn.jquery,
        modernJquery    = /1\.6|7|8|9\.[0-9]+/.test(version) !== false,
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

                //noinspection JSHint,JSHint
                return window.JSON && window.JSON.parse ?

                    window.JSON.parse(data) :

                    (new Function("return " + data))();

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
TubePressLoadStyler = (function () {

    /** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
    'use strict';

    var jquery = jQuery,

        /**
         * Fade to "white".
         */
        applyLoadingStyle = function (targetDiv) {

            jquery(targetDiv).fadeTo(0, 0.3);
        },

        /**
         * Fade back to full opacity.
         */
        removeLoadingStyle = function (targetDiv) {

            jquery(targetDiv).fadeTo(0, 1);
        };

    return {

        applyLoadingStyle   : applyLoadingStyle,
        removeLoadingStyle  : removeLoadingStyle
    };

}()),

/**
 * Various Ajax utilities.
 */
TubePressAjax = (function () {

    /** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
    'use strict';

    var
        /** These variable declarations aide in compression. */
        jquery      = jQuery,
        isFunction  = jquery.isFunction,
        styler      = TubePressLoadStyler,

        /**
         * Similar to jQuery's "load" but tolerates non-200 status codes.
         * https://github.com/jquery/jquery/blob/master/src/ajax.js#L168.
         */
        load = function (method, url, targetDiv, selector, preLoadFunction, postLoadFunction) {

            var completeCallback = function (res) {

                var responseText    = res.responseText,
                    html            = selector ? jquery('<div>').append(responseText).find(selector) : responseText;

                jquery(targetDiv).html(html);

                /* did the user supply a post-load function? */
                if (isFunction(postLoadFunction)) {

                    postLoadFunction();
                }
            };

            /** did the user supply a pre-load function? */
            if (isFunction(preLoadFunction)) {

                preLoadFunction();
            }

            jquery.ajax({

                url            : url,
                type        : method,
                dataType    : 'html',
                complete    : completeCallback
            });
        },

        /**
         * Similar to jQuery's "get" but ignores response code.
         */
        get = function (method, url, data, success, dataType) {

            jquery.ajax({

                url            : url,
                type        : method,
                data        : data,
                dataType    : dataType,
                complete    : success
            });

        },

        triggerDoneLoading = function (targetDiv) {

            styler.removeLoadingStyle(targetDiv);
        },

        /**
         * Calls "load", but does some additional styling on the target element while it's processing.
         */
        loadAndStyle = function (method, url, targetDiv, selector, preLoadFunction, postLoadFunction) {

            /** one way or another, we're removing the loading style when we're done... */
            var post = function () {

                    triggerDoneLoading(targetDiv);
                };

            styler.applyLoadingStyle(targetDiv);

            /** ... but maybe we want to do something else too */
            if (isFunction(postLoadFunction)) {

                post = function () {

                    triggerDoneLoading(targetDiv);
                    postLoadFunction();
                };
            }

            /** do the load. do it! */
            load(method, url, targetDiv, selector, preLoadFunction, post);
        };

    return {

        loadAndStyle    : loadAndStyle,
        get             : get
    };
}()),

/**
 * Handles dynamic loading of CSS. This should only really be used for TubePress players
 * and NOT TubePress themes.
 */
TubePressCss = (function () {

    /** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
    'use strict';

    /*
     * Dynamically load CSS into the DOM.
     */
    var load = function (path) {

        var    fileref = document.createElement('link');

        fileref.setAttribute('rel', 'stylesheet');
        fileref.setAttribute('type', 'text/css');
        fileref.setAttribute('href', path);
        document.getElementsByTagName('head')[0].appendChild(fileref);
    };

    return {

        load    : load
    };
}()),

TubePressGallery = (function () {

    /** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
    'use strict';

    var galleryRegistry = {},
        nvpMap            = 'nvpMap',
        jsMap            = 'jsMap',

        /**
         * Have we heard about this gallery?
         */
        isRegistered = function (galleryId) {

            return typeof galleryRegistry[galleryId] !== 'undefined';
        },

        /**
         * Gets a property for the given gallery.
         */
        internalGet = function (galleryId, property, jsOrNvp) {

            return isRegistered(galleryId) ?

                    galleryRegistry[galleryId][jsOrNvp][property] : null;
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
         * Does the gallery use fluid thumbs?
         */
        isFluidThumbs = function (galleryId) {

            return internalGet(galleryId, jsMap, 'fluidThumbs');
        },

        /**
         * What's the embedded height for the video player of this gallery?
         */
        getEmbeddedHeight = function (galleryId) {

            return internalGet(galleryId, jsMap, 'embeddedHeight');
        },

        /**
         * What's the embedded width for the video player of this gallery?
         */
        getEmbeddedWidth = function (galleryId) {

            return internalGet(galleryId, jsMap, 'embeddedWidth');
        },

        /**
         * Which HTTP method (GET or POST) does this gallery want to use?
         */
        getHttpMethod = function (galleryId) {

            return internalGet(galleryId, jsMap, 'httpMethod');
        },

        getNvpMap = function (galleryId) {

            return galleryRegistry[galleryId][nvpMap];
        },

        /**
         * What's the gallery's player location name?
         */
        getPlayerLocationName = function (galleryId) {

            return internalGet(galleryId, jsMap, 'playerLocation');
        },

        /**
         * Where is the JS init code for this player?
         */
        getPlayerJsUrl = function (galleryId) {

            return internalGet(galleryId, jsMap, 'playerJsUrl');
        },

        /**
         * Does this player produce HTML?
         */
        getPlayerProducesHtml = function (galleryId) {

            return internalGet(galleryId, jsMap, 'playerLocationProducesHtml');
        },

        /**
         * What's the sequence of videos for this gallery?
         */
        getSequence = function (galleryId) {

            return internalGet(galleryId, jsMap, 'sequence');
        },


        /**
         * Performs gallery initialization on jQuery(document).ready().
         */
        onNewGallery = function (galleryId, params) {

            /** Save the params. */
            galleryRegistry[galleryId] = params;
        };

    /**
     * We want to get notified of any gallery appearances.
     */
    TubePressBeacon.subscribe(TubePressEvents.THUMBGALLERY.NEW_GALLERY_LOADED, onNewGallery);

    return {

        isAjaxPagination              : isAjaxPagination,
        isAutoNext                    : isAutoNext,
        isFluidThumbs                 : isFluidThumbs,
        getEmbeddedHeight             : getEmbeddedHeight,
        getEmbeddedWidth              : getEmbeddedWidth,
        getHttpMethod                 : getHttpMethod,
        getNvpMap                     : getNvpMap,
        getPlayerLocationName         : getPlayerLocationName,
        getPlayerLocationProducesHtml : getPlayerProducesHtml,
        getPlayerJsUrl                : getPlayerJsUrl,
        getSequence                   : getSequence,
        isRegistered                  : isRegistered
    };
}()),

/**
 * Handles player-related functionality (popup, Shadowbox, etc)
 */
TubePressPlayers = (function () {

    /** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
    'use strict';

    var
        /** These variable declarations help compression. */
        jquery           = jQuery,
        tubepressGallery = TubePressGallery,
        tubepressEvents  = TubePressEvents,
        tubepressBeacon  = TubePressBeacon,
        playerEvents     = tubepressEvents.PLAYERS,
        publish          = tubepressBeacon.publish,
        subscribe        = tubepressBeacon.subscribe,

        /**
         * Find the player required for a gallery and load the JS.
         */
        bootPlayer = function (e, galleryId) {

            var playerName = tubepressGallery.getPlayerLocationName(galleryId),
                path       = tubepressGallery.getPlayerJsUrl(galleryId);

            /*
             * Load this player's JS, if needed.
             */
            //noinspection JSUnresolvedFunction
            TubePress$cript(path, playerName);
        },

        /**
         * Load up a TubePress player with the given video ID.
         */
        invokePlayer = function (e, galleryId, videoId) {

            var playerName = tubepressGallery.getPlayerLocationName(galleryId),
                height     = tubepressGallery.getEmbeddedHeight(galleryId),
                width      = tubepressGallery.getEmbeddedWidth(galleryId),
                nvpMap     = tubepressGallery.getNvpMap(galleryId),

                callback   = function (data) {

                    var result = TubePressJson.parse(data.responseText),
                        title  = result.title,
                        html   = result.html;

                    publish(playerEvents.PLAYER_POPULATE + playerName, [ title, html, height, width, videoId, galleryId ]);
                },

                dataToSend = {

                    'action'          : 'playerHtml',
                    'tubepress_video' : videoId
                },

                url = TubePressGlobalJsConfig.baseUrl + '/src/main/php/scripts/ajaxEndpoint.php',
                method;

            /**
             * Add the NVPs for TubePress to the data.
             */
            jquery.extend(dataToSend, nvpMap);

            /** Announce we're gonna invoke the player... */
            publish(playerEvents.PLAYER_INVOKE + playerName, [ videoId, galleryId, width, height ]);

            /** If this player requires population, go fetch the HTML for it. */
            if (tubepressGallery.getPlayerLocationProducesHtml(galleryId)) {

                method = tubepressGallery.getHttpMethod(galleryId);

                /* ... and fetch the HTML for it */
                TubePressAjax.get(method, url, dataToSend, callback, 'json');
            }
        };

    /** When we see a new gallery... */
    subscribe(tubepressEvents.THUMBGALLERY.NEW_GALLERY_LOADED, bootPlayer);

    /** When a user clicks a thumbnail... */
    subscribe(tubepressEvents.SEQUENCING.GALLERY_VIDEO_CHANGE, invokePlayer);
}()),

/**
 * Sequencing support for TubePress.
 */
TubePressSequencer = (function () {

    /** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
    'use strict';

    var

        /** These variable declarations aide in compression. */
        tubepressGallery        = TubePressGallery,
        jquery                  = jQuery,
        events                  = TubePressEvents,
        logger                  = TubePressLogger,
        isCurrentlyPlayingVideo = 'isCurrentlyPlayingVideo',
        currentVideoId          = 'currentVideoId',
        tubePressBeacon         = TubePressBeacon,
        subscribe               = tubePressBeacon.subscribe,

        /** Galleries that we track. */
        galleries = {},

        /**
         * Searches through our galleries for one that matches the given.
         */
        findGalleryThatMatchesTest = function (test) {

            var galleryId;

            for (galleryId in galleries) {

                if (galleries.hasOwnProperty(galleryId)) {

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

                var gall = galleries[galleryId];

                return gall[currentVideoId] === videoId;
            };

            return findGalleryThatMatchesTest(test);
        },

        /**
         * Find the gallery that is currently playing the given video.
         */
        findGalleryIdCurrentlyPlayingVideo = function (videoId) {

            var test = function (galleryId) {

                var gall               = galleries[galleryId],
                    isCurrentlyPlaying = gall[currentVideoId] === videoId && gall[isCurrentlyPlayingVideo];

                if (isCurrentlyPlaying) {

                    return galleryId;
                }

                return false;
            };

            return findGalleryThatMatchesTest(test);
        },

        /**
         * When a new gallery is loaded...
         */
        onNewGalleryLoaded = function (e, galleryId) {

            var gall     = {},
                sequence = tubepressGallery.getSequence(galleryId);

            gall[isCurrentlyPlayingVideo] = false;

            /**
             * If this gallery has a sequence,
             * save the first video as the "current" video.
             */
            if (sequence) {

                gall[currentVideoId] = sequence[0];
            }

            /** Record it. */
            galleries[galleryId] = gall;

            if (logger.on()) {

                logger.log('Gallery ' + galleryId + ' loaded');
            }
        },

        /**
         * Set a video as "current" for a gallery.
         */
        changeToVideo = function (galleryId, videoId) {

            /** Save it as current. */
            galleries[galleryId][currentVideoId] = videoId;

            /** Announce the change. */
            tubePressBeacon.publish(events.SEQUENCING.GALLERY_VIDEO_CHANGE, [galleryId, videoId]);
        },

        /**
         * Go to the next video in the gallery.
         */
        next = function (galleryId) {

            /** Get the gallery's sequence. This is an array of video ids. */
            var sequence    = tubepressGallery.getSequence(galleryId),
                vidId        = galleries[galleryId][currentVideoId],
                index        = jquery.inArray(vidId, sequence),
                lastIndex    = sequence ? sequence.length - 1 : index;

            /** Sorry, we don't know anything about this video id, or we've reached the end of the gallery. */
            if (index === -1 || index === lastIndex) {

                return;
            }

            /** Start the next video in line. */
            changeToVideo(galleryId, sequence[index + 1]);
        },

        /** Play the previous video in the gallery. */
        prev = function (galleryId) {

            /** Get the gallery's sequence. This is an array of video ids. */
            var sequence    = tubepressGallery.getSequence(galleryId),
                vidId        = galleries[galleryId][currentVideoId],
                index        = jquery.inArray(vidId, sequence);

            /** Sorry, we don't know anything about this video id, or we're at the start of the gallery. */
            if (index === -1 || index === 0) {

                return;
            }

            /** Start the previous video in line. */
            changeToVideo(galleryId, sequence[index + 1]);
        },

        /**
         * A video on the page has started.
         */
        onPlaybackStarted = function (e, videoId) {

            var matchingGalleryId = findGalleryIdWithVideoIdAsCurrent(videoId);

            /**
             * If we don't have a gallery assigned to this video, we don't really care.
             */
            if (! matchingGalleryId) {

                return;
            }

            /**
             * Record the video as playing.
             */
            galleries[matchingGalleryId][isCurrentlyPlayingVideo]    = true;
            galleries[matchingGalleryId][currentVideoId]                = videoId;

            if (logger.on()) {

                logger.log('Playback of ' + videoId + ' started for gallery ' + matchingGalleryId);
            }
        },

        /**
         * A video on the page has stopped.
         */
        onPlaybackStopped = function (e, videoId) {

            var matchingGalleryId = findGalleryIdCurrentlyPlayingVideo(videoId);

            /**
             * If we don't have a gallery assigned to this video, we don't really care.
             */
            if (! matchingGalleryId) {

                return;
            }

            /**
             * Record the video as not playing.
             */
            galleries[matchingGalleryId][isCurrentlyPlayingVideo] = false;

            if (logger.on()) {

                logger.log('Playback of ' + videoId + ' stopped for gallery ' + matchingGalleryId);
            }

            if (tubepressGallery.isAutoNext(matchingGalleryId) && tubepressGallery.getSequence(matchingGalleryId)) {

                if (logger.on()) {

                    logger.log('Auto-starting next for gallery ' + matchingGalleryId);
                }

                /** Go to the next one! */
                next(matchingGalleryId);
            }
        };

    /** We want to keep track of all galleries that are loaded. */
    subscribe(events.THUMBGALLERY.NEW_GALLERY_LOADED, onNewGalleryLoaded);

    /** We would like to be notified when a video starts */
    subscribe(events.EMBEDDED.PLAYBACK_STARTED, onPlaybackStarted);

    /** We would like to be notified when a video ends, in the case of auto-next. */
    subscribe(events.EMBEDDED.PLAYBACK_STOPPED, onPlaybackStopped);

    return {

        changeToVideo : changeToVideo,
        next          : next,
        prev          : prev
    };

}()),

/**
 * Handles basic thumbnail tasks.
 */
TubePressThumbs = (function () {

    /** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
    'use strict';

    var
        /** These variables aide in compression. */
        jquery = jQuery,
        events = TubePressEvents.THUMBGALLERY,
        math   = Math,

        /**
         * Get the jQuery selector where the thumbs live.
         */
        //this is hard-coded - need to get rid of that.
        getThumbAreaSelector = function (galleryId) {

            return "#tubepress_gallery_" + galleryId + "_thumbnail_area";
        },

        /**
         * Get the jQuery reference to where the thumbnails live.
         */
        getThumbArea = function (galleryId) {

            return jquery(getThumbAreaSelector(galleryId));
        },

        /**
         * Parse the gallery ID from the "rel" attribute.
         */
        getGalleryIdFromRelSplit = function (relSplit) {

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

                firstVisualElement = thumbArea.find('div.tubepress_thumb:first > div.tubepress_embed');

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

        /**
         * Click listener callback.
         */
        clickListener = function () {

            var rel_split = jquery(this).attr('rel').split('_'),
                galleryId = getGalleryIdFromRelSplit(rel_split),
                videoId   = getVideoIdFromIdAttr(jquery(this).attr('id'));

            /** Tell the gallery to change it's video. */
            TubePressSequencer.changeToVideo(galleryId, videoId);
        },

        /** http://www.sohtanaka.com/web-design/smart-columns-w-css-jquery/ */
        makeThumbsFluid = function (galleryId) {

            getThumbArea(galleryId).css({ 'width' : '100%' });

            var gallerySelector = getThumbAreaSelector(galleryId),
                columnWidth     = getThumbWidth(galleryId),
                gallery         = jquery(gallerySelector),
                colWrap         = gallery.width(),
                colNum          = math.floor(colWrap / columnWidth),
                colFixed        = math.floor(colWrap / colNum),
                thumbs          = jquery(gallerySelector + ' div.tubepress_thumb');

            gallery.css({ 'width' : '100%'});
            gallery.css({ 'width' : colWrap });
            thumbs.css({ 'width' : colFixed});
        },

        /**
         * What page is the gallery on?
         */
        //this is way too fragile.
        getCurrentPageNumber = function (galleryId) {

            var page               = 1,
                paginationSelector = 'div#tubepress_gallery_' + galleryId + ' div.tubepress_thumbnail_area:first > div.pagination:first > span.current',
                current            = jquery(paginationSelector);

            if (current.length > 0) {

                page = current.html();
            }

            return page;
        },

        /**
         * Callback for thumbnail loads.
         */
        thumbBinder = function (e, galleryId) {

            /* add a click handler to each link in this gallery */
            jquery("#tubepress_gallery_" + galleryId + " a[id^='tubepress_']").click(clickListener);

            /* fluid thumbs if we need it */
            if (TubePressGallery.isFluidThumbs(galleryId)) {

                makeThumbsFluid(galleryId);
            }
        };

    TubePressBeacon.subscribe(events.NEW_THUMBS_LOADED + ' ' + events.NEW_GALLERY_LOADED, thumbBinder);

    /* return only public functions */
    return {

        getCurrentPageNumber     : getCurrentPageNumber,
        getGalleryIdFromRelSplit : getGalleryIdFromRelSplit,
        getThumbAreaSelector     : getThumbAreaSelector,
        getVideoIdFromIdAttr     : getVideoIdFromIdAttr
    };
}()),

/**
 * Functions for handling Ajax pagination.
 */
TubePressAjaxPagination = (function () {

    /** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
    'use strict';

    var
        /** These variable declarations aide in compression. */
        jquery  = jQuery,
        beacon  = TubePressBeacon,
        events  = TubePressEvents,
        gallery = TubePressGallery,

        /**
         * After we've loaded a new set of thumbs.
         */
        postLoad = function (galleryId) {

            beacon.publish(TubePressEvents.THUMBGALLERY.NEW_THUMBS_LOADED, galleryId);
        },

        /** Handles an ajax pagination click. */
        processClick = function (anchor, galleryId) {

            var baseUrl       = TubePressGlobalJsConfig.baseUrl,
                nvpMap        = gallery.getNvpMap(galleryId),
                page          = anchor.attr('rel'),
                thumbnailArea = TubePressThumbs.getThumbAreaSelector(galleryId),

                postLoadCallback = function () {

                    postLoad(galleryId);
                },

                toSend = {

                    action : 'shortcode'
                },

                pageToLoad         = baseUrl + '/src/main/php/scripts/ajaxEndpoint.php?tubepress_' + page + '&' + jquery.param(jquery.extend(toSend, nvpMap)),
                remotePageSelector = thumbnailArea + ' > *',
                httpMethod         = gallery.getHttpMethod(galleryId);

            TubePressAjax.loadAndStyle(httpMethod, pageToLoad, thumbnailArea, remotePageSelector, '', postLoadCallback);
        },

        /** Initializes pagination HTML for Ajax. */
        addClickHandlers = function (galleryId) {

            var clickCallback = function () {

                processClick(jquery(this), galleryId);
            };

            jquery('#tubepress_gallery_' + galleryId + ' div.pagination a').click(clickCallback);
        },

        /**
         * Adds click handlers to galleries with Ajax pagination.
         */
        paginationBinder = function (e, galleryId) {

            if (gallery.isAjaxPagination(galleryId)) {

                addClickHandlers(galleryId);
            }
        };

    /** Sets up new thumbnails for ajax pagination */
    beacon.subscribe(events.NEW_THUMBS_LOADED + ' ' + events.NEW_GALLERY_LOADED, paginationBinder);

}()),

/**
 * Provides auto-sequencing capability for TubePress.
 */
TubePressPlayerApiUtils = (function () {

    /** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
    'use strict';

    var

        /** These variable declarations aide in compression. */
        events = TubePressEvents.EMBEDDED,

        /**
         * Helper method to trigger events on jQuery(document).
         */
        triggerEvent = function (eventName, videoId) {

            TubePressBeacon.publish(eventName, videoId);
        },

        /**
         * A video has started.
         */
        fireVideoStarted = function (videoId) {

            triggerEvent(events.PLAYBACK_STARTED, videoId);
        },

        /**
         * A video has stopped.
         */
        fireVideoStopped = function (videoId) {

            triggerEvent(events.PLAYBACK_STOPPED, videoId);
        },

        /**
         * A video is buffering.
         */
        fireVideoBuffering = function (videoId) {

            triggerEvent(events.PLAYBACK_BUFFERING, videoId);
        },

        /**
         * A video has paused.
         */
        fireVideoPaused = function (videoId) {

            triggerEvent(events.PLAYBACK_PAUSED, videoId);
        },

        /**
         * A video has encountered an error.
         */
        fireVideoError = function (videoId) {

            triggerEvent(events.PLAYBACK_ERROR, videoId);
        },

        /**
         * Utility to wait for test() to be true, then call callback()
         */
        callWhenTrue = function (callback, test, delay) {

            /** It's ready... */
            if (test() === true) {

                callback();
                return;
            }

            /** Set up a timeout callback. */
            var func = function () {

                callWhenTrue(callback, test, delay);
            };

            /** Keep waiting... */
            setTimeout(func, delay);
        };

    return {

        fireVideoError     : fireVideoError,
        fireVideoPaused    : fireVideoPaused,
        fireVideoBuffering : fireVideoBuffering,
        fireVideoStopped   : fireVideoStopped,
        fireVideoStarted   : fireVideoStarted,
        callWhenTrue       : callWhenTrue
    };

}()),

TubePressYouTubePlayerApi = (function () {

    /** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
    'use strict';

    var

        /** These variable declarations aide in compression. */
        jquery           = jQuery,
        logger           = TubePressLogger,
        bundleName       = 'youtube_player_api',
        scriptLoader     = TubePress$cript,
        youTubePrefix    = 'tubepress-youtube-player-',
        undefText        = 'undefined',
        apiUtils         = TubePressPlayerApiUtils,

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
            if (! jquery.isFunction(player.getVideoUrl)) {

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
            return typeof window.YT !== undefText && typeof YT.Player !== undefText;
        },

        /**
         * Load the YouTube API, if necessary.
         */
        loadYouTubeApi = function () {

            if (isLoadingApi || isYouTubeApiAvailable()) {

                return;
            }

            isLoadingApi = true;

            //noinspection JSUnresolvedVariable
            var scheme = TubePressGlobalJsConfig.https ? 'https' : 'http';

            //noinspection JSUnresolvedVariable
            scriptLoader(scheme + '://www.youtube.com/player_api', bundleName);
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

                apiUtils.fireVideoStarted(videoId);
                break;

            case playerState.PAUSED:

                apiUtils.fireVideoPaused(videoId);
                break;

            case playerState.ENDED:

                apiUtils.fireVideoStopped(videoId);
                break;

            case playerState.BUFFERING:

                apiUtils.fireVideoBuffering(videoId);
                break;

            default:

                if (logger.on()) {

                    logger.log('Unknown YT event');
                    logger.dir(event);
                }

                //unknown event
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

            apiUtils.fireVideoError(videoId);
        },

        /**
         * Registers a YouTube player for use with the TubePress API.
         */
        registerYouTubeVideo = function (videoId) {

            /** Load 'er up. */
            loadYouTubeApi();

            /** This stuff will execute once the TubePress API is loaded. */
            var callback = function () {

                if (logger.on()) {

                    logger.log('Register YT video ' + videoId + ' with TubePress');
                }

                //noinspection JSUnresolvedFunction
                youTubePlayers[videoId] = new YT.Player(youTubePrefix + videoId, {

                    events: {

                        'onError'       : onYouTubeError,
                        'onStateChange' : onYouTubeStateChange
                    }
                });
            };

            apiUtils.callWhenTrue(callback, isYouTubeApiAvailable, 250);
        },

        onNewVideoRegistered = function (event, videoId) {

            if (isYouTubeVideoId(videoId)) {

                registerYouTubeVideo(videoId);
            }
        };

    TubePressBeacon.subscribe(TubePressEvents.EMBEDDED.EMBEDDED_LOAD, onNewVideoRegistered);

    return {

        onYouTubeStateChange : onYouTubeStateChange,
        onYouTubeError       : onYouTubeError
    };
}()),

TubePressVimeoPlayerApi = (function () {

    /** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
    'use strict';

    var

        /** These variable declarations aide in compression. */
        jquery      = jQuery,
        vimeoPrefix = 'tubepress-vimeo-player-',
        apiUtils    = TubePressPlayerApiUtils,

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

            return window.Froogaloop !== undefined;
        },

        /**
         * Load the Vimeo API, if necessary.
         */
        loadVimeoApi = function () {

            //noinspection JSUnresolvedVariable
            var scheme = TubePressGlobalJsConfig.https ? httpsScheme : httpScheme;

            if (! loadingVimeoApi && ! isVimeoApiAvailable()) {

                loadingVimeoApi = true;
                TubePress$cript(scheme + '://a.vimeocdn.com/js/froogaloop2.min.js', 'vimeoApi');
            }
        },

        /**
         * Vimeo will call then when a video starts.
         */
        onVimeoPlay = function (event) {

            var videoId = getVideoIdFromVimeoEvent(event);

            apiUtils.fireVideoStarted(videoId);
        },

        /**
         * Vimeo will call then when a video pauses.
         */
        onVimeoPause = function (event) {

            var videoId = getVideoIdFromVimeoEvent(event);

            apiUtils.fireVideoPaused(videoId);
        },

        /**
         * Vimeo will call then when a video ends.
         */
        onVimeoFinish = function (event) {

            var videoId = getVideoIdFromVimeoEvent(event);

            apiUtils.fireVideoStopped(videoId);
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
                iframe   = document.getElementById(playerId),

                callback = function () {

                    /** Create and save the player. */
                    var froog = new Froogaloop(iframe);

                    vimeoPlayers[playerId] = froog;

                    froog.addEvent('ready', onVimeoReady);
                };

            /** Execute it when Vimeo is ready. */
            apiUtils.callWhenTrue(callback, isVimeoApiAvailable, 500);
        },

        onNewVideoRegistered = function (event, videoId) {

            if (isVimeoVideoId(videoId)) {

                registerVimeoVideo(videoId);
            }
        };

    TubePressBeacon.subscribe(TubePressEvents.EMBEDDED.EMBEDDED_LOAD, onNewVideoRegistered);

    return {

        onVimeoReady  : onVimeoReady,
        onVimeoPlay   : onVimeoPlay,
        onVimeoPause  : onVimeoPause,
        onVimeoFinish : onVimeoFinish
    };
}()),

/**
 * Handles Ajax interactive searching.
 */
TubePressAjaxSearch = (function () {

    /** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
    'use strict';

    var performSearch = function (galleryInitJs, rawSearchTerms, galleryId) {

        /** These variable declarations aide in compression. */
        var jquery            = jQuery,
            gallery           = TubePressGallery,
            logger            = TubePressLogger,
            targetDomSelector = galleryInitJs.nvpMap.searchResultsDomId,

            /** Some vars we'll need later. */
            callback,
            ajaxResultSelector,
            finalAjaxContentDestination,

            urlParams = {

                action           : 'shortcode',
                tubepress_search : rawSearchTerms
            },

            /** The Ajax response results that we're interested in. */
            gallerySelector = '#tubepress_gallery_' + galleryId,

            /** Does a gallery with this ID already exist? */
            galleryExists = gallery.isRegistered(galleryId),

            /** Does the target DOM exist? */
            targetDomExists = targetDomSelector !== undefined && jquery(targetDomSelector).length > 0;

        /** We have three cases to handle... */
        if (galleryExists) {

            //CASE 1: gallery already exists

            /** Stick the thumbs into the existing thumb area. */
            finalAjaxContentDestination = TubePressThumbs.getThumbAreaSelector(galleryId);

            /** We want just the new thumbnails. */
            ajaxResultSelector = finalAjaxContentDestination + ' > *';

            /** Announce the new thumbs */
            callback = function () {

                TubePressBeacon.publish(TubePressEvents.THUMBGALLERY.NEW_THUMBS_LOADED, galleryId);
            };

        } else {

            if (targetDomExists) {

                //CASE 2: TARGET SELECTOR EXISTS AND GALLERY DOES NOT EXIST

                /** Stick the gallery into the target DOM. */
                finalAjaxContentDestination = targetDomSelector;

            } else {

                //CASE 3: TARGET SELECTOR DOES NOT EXIST AND GALLERY DOES NOT EXIST

                if (logger.on()) {

                    logger.log('Bad target selector and missing gallery');
                }

                return;
            }
        }

        if (logger.on()) {

            logger.log('Final dest: ' + finalAjaxContentDestination);
            logger.log('Ajax selector: ' + ajaxResultSelector);
        }

        jquery.extend(urlParams, galleryInitJs.nvpMap);

        TubePressAjax.loadAndStyle(

            galleryInitJs.jsMap.httpMethod,

            TubePressGlobalJsConfig.baseUrl + '/src/main/php/scripts/ajaxEndpoint.php?' + jquery.param(urlParams),

            finalAjaxContentDestination,

            ajaxResultSelector,

            null,

            callback
        );
    };

    return { performSearch : performSearch };

}());

//http://tmxcredit.com/tech-blog/understanding-javascript-asynchronous-apis/
(function() {

    var queue = window._beacon, entity,

        queueCall = function (callArray) {

            var method = callArray[0],
                args   = callArray.slice(1);

            TubePressBeacon[method].apply(this, args);
        };

    if (typeof queue !== 'undefined') {

        // loop through our existing queue, calling methods in order
        queue.reverse()

        while (queue.length) {

            entity = queue.pop();

            queueCall(entity);
        }
    }

    // over write the sampleQueue, replacing the push method with 'queueCall'
    // this creates a globally accessible interface to your API through sampleQueue.push()
    window._beacon = {

        push : queueCall
    };
})();