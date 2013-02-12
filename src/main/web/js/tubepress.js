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

/*global jQuery, TubePressGlobalJsConfig, YT, Froogaloop, console */
/*jslint devel: true, browser: true, sloppy: false, white: true, maxerr: 50, indent: 4 */

var TubePress = (function (jquery, win) {

    /** http://ejohn.org/blog/ecmascript-5-strict-mode-json-and-more/ */
    'use strict';

    /**
     * Let's start with some variable declarations to help us with compression.
     */
    var text_tubepress = 'tubepress',
        windowLocation = win.location,
        dokument       = win.document,
        troo           = true,
        coreJsPrefix   = 'src/main/web/js',

        /**
         * Random language utilities.
         */
        langUtils = (function () {

            var isDefined = function (obj) {

                return typeof obj !== 'undefined';
            },

                getParameterByName = function (name) {

                    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");

                    var regexS  = "[\\?&]" + name + "=([^&#]*)",
                        regex   = new RegExp(regexS),
                        results = regex.exec(windowLocation.search);

                    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
                },

                parseIntOrZero = function (candidate) {

                    var firstPass = parseInt(candidate, 10);

                    return isNaN(firstPass) ? 0 : firstPass;
                },

                /**
                 * Utility to wait for test() to be true, then call callback()
                 */
                callWhenTrue = function (callback, test, delay) {

                    /** It's ready... */
                    if (test() === troo) {

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

                isDefined          : isDefined,
                getParameterByName : getParameterByName,
                parseIntOrZero     : parseIntOrZero,
                callWhenTrue       : callWhenTrue
            };
        }()),

        /**
         * Logger!
         */
        logger = (function () {

            /**
             * Is the log on?
             */
            var isLoggingRequested = windowLocation.search.indexOf(text_tubepress + '_debug=true') !== -1,
                windowConsole      = win.console,
                isLoggingAvailable = langUtils.isDefined(windowConsole),

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

                on  : isLoggingOn,
                log : log,
                dir : dir
            };
        }()),

        /**
         * Details about our environment.
         */
        environment = (function () {

            var alreadyCalculatedBaseUrl = false,
                cachedBaseUrl,

                getBaseUrl = function () {

                    if (!alreadyCalculatedBaseUrl) {

                        //http://stackoverflow.com/questions/2161159/get-script-path
                        var scripts = dokument.getElementsByTagName('script'),
                            x       = 0,
                            scriptSrc;

                        for (x; x < scripts.length; x += 1) {

                            scriptSrc = scripts[x].src;

                            if (scriptSrc.indexOf('/' + text_tubepress + '.js') !== -1) {

                                cachedBaseUrl = scriptSrc.substr(0, scriptSrc.lastIndexOf('/')).split('?')[0].replace(coreJsPrefix, '');
                                break;
                            }
                        }
                    }

                    return cachedBaseUrl;
                };

            return {

                getBaseUrl : getBaseUrl
            };
        }()),

        /**
         * Events that TubePress fires.
         */
        events = (function () {

            /*
             * These variable declarations aide in compression.
             */
            var xdot          = '.',
                xembedded     = text_tubepress + xdot + 'embedded'     + xdot,
                xplayers      = text_tubepress + xdot + 'players'      + xdot,
                xajax         = text_tubepress + xdot + 'ajax'         + xdot,
                xsearch       = text_tubepress + xdot + 'search'       + xdot,
                xrequest      = 'request';

            return {

                AJAX : {

                    BEFORE   : xajax + 'before',

                    SUCCESS  : xajax + 'success',

                    ERROR    : xajax + 'error',

                    COMPLETE : xajax + 'complete'
                },

                EMBEDDED : {

                    /** An embedded video has been loaded. */
                    EMBEDDED_LOAD      : xembedded + 'load',

                    /** Playback of a video started. */
                    PLAYBACK_STARTED   : xembedded + 'start',

                    /** Playback of a video stopped. */
                    PLAYBACK_STOPPED   : xembedded + 'stop',

                    /** Playback of a video is buffering. */
                    PLAYBACK_BUFFERING : xembedded + 'buffer',

                    /** Playback of a video is paused. */
                    PLAYBACK_PAUSED    : xembedded + 'pause',

                    /** Playback of a video has errored out. */
                    PLAYBACK_ERROR     : xembedded + 'error'
                },

                PLAYERS : {

                    /** A TubePress player is being invoked. */
                    PLAYER_INVOKE   : xplayers + 'invoke',

                    /** A TubePress player is being populated. */
                    PLAYER_POPULATE : xplayers + 'populate'
                },

                SEARCH : {

                    STATIC_SEARCH_REQUESTED : xsearch + xrequest + 'static',

                    AJAX_SEARCH_REQUESTED : xsearch + xrequest + 'ajax'
                }
            };
        }()),

        /**
         * Lightweight event bus for TubePress.
         *
         * https://gist.github.com/cowboy/661855
         *
         * jQuery Tiny Pub/Sub - v0.3 - 11/4/2010
         * http://benalman.com/
         *
         * Copyright (c) 2010 "Cowboy" Ben Alman
         * Dual licensed under the MIT and GPL licenses.
         * http://benalman.com/about/license/
         */
        beacon = (function () {

            var bus = jquery({}),

                subscribe = function () {

                    bus.bind.apply(bus, arguments);
                },

                unsubscribe = function () {

                    bus.unbind.apply(bus, arguments);
                },

                publish = function () {

                    if (logger.on()) {

                        var args = arguments;

                        logger.log('Firing "' + args[0]);
                        logger.dir(args[1]);
                    }

                    bus.trigger.apply(bus, arguments);
                };

            return {

                subscribe   : subscribe,
                unsubscribe : unsubscribe,
                publish     : publish
            };
        }()),

        /**
         * Injects JS and CSS into the DOM.
         */
        domInjector = (function () {

            var filesAlreadyLoaded = [],

                alreadyLoaded = function (path) {

                    return filesAlreadyLoaded[path] === troo;
                },

                convertToAbsoluteUrl = function (url) {

                    if (url.indexOf('http') === 0) {

                        //already absolute
                        return url;
                    }

                    return environment.getBaseUrl() + url;
                },

                doLog = function (path, type) {

                    if (logger.on()) {

                        logger.log('Injecting ' + type + ': ' + path);
                    }
                },

                appendToHead = function (element) {

                    dokument.getElementsByTagName('head')[0].appendChild(element);
                },

                loadCss = function (path) {

                    path = convertToAbsoluteUrl(path);

                    if (alreadyLoaded(path)) {

                        return;
                    }

                    filesAlreadyLoaded[path] = troo;

                    var fileref   = dokument.createElement('link');

                    fileref.rel  = 'stylesheet';
                    fileref.type = 'text/css';
                    fileref.ref  = path;

                    doLog(path, 'CSS');

                    appendToHead(fileref);
                },

                loadJs = function (path) {

                    path = convertToAbsoluteUrl(path);

                    if (alreadyLoaded(path)) {

                        return;
                    }

                    filesAlreadyLoaded[path] = troo;

                    doLog(path, 'JS');

                    var script = dokument.createElement('script');

                    script.type  = 'text/javascript';
                    script.src   = path;
                    script.async = troo;

                    dokument.getElementsByTagName('head')[0].appendChild(script);
                },

                loadGalleryJs = function () {

                    loadJs(coreJsPrefix + '/' + text_tubepress + '/gallery.js');
                },

                loadedEmbeddedApiJs = function () {

                    loadJs(coreJsPrefix + '/' + text_tubepress + '/embedded.js');
                };

            return {

                loadJs            : loadJs,
                loadCss           : loadCss,
                loadGalleryJs     : loadGalleryJs,
                loadEmbeddedApiJs : loadedEmbeddedApiJs
            };
        }()),

        /**
         * Core of our asychronous APIs.
         */
        asyncConverter = (function () {

            //http://tmxcredit.com/tech-blog/understanding-javascript-asynchronous-apis/
            var convertQueueToFunctionCalls = function (asyncObjectName, delegate) {

                var queue = win[asyncObjectName],

                    queueCall = function (callArray) {

                        var method = callArray[0],
                            args   = callArray.slice(1);

                        delegate[method].apply(this, args);
                    };

                if (langUtils.isDefined(queue)) {

                    // loop through our existing queue, calling methods in order
                    queue.reverse();

                    while (queue.length) {

                        queueCall(queue.pop());
                    }
                }

                // over write the sampleQueue, replacing the push method with 'queueCall'
                // this creates a globally accessible interface to your API through sampleQueue.push()
                win[asyncObjectName] = {

                    push : queueCall
                };
            };

            return {

                processQueueCalls : convertQueueToFunctionCalls
            };
        }());

    /**
     * Stuff we expose to everyone else.
     */
    return {

        LangUtils   : langUtils,
        Logger      : logger,
        Events      : events,
        Beacon      : beacon,
        DomInjector : domInjector,
        Environment : environment,
        AsyncUtil   : asyncConverter
    };

}(jQuery, window));

TubePress.AsyncUtil.processQueueCalls('tubePressDomInjector', TubePress.DomInjector);