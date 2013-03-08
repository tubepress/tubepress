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

/*global jQuery, TubePressGlobalJsConfig, YT, Froogaloop, console */
/*jslint devel: true, browser: true, sloppy: false, white: true, maxerr: 50, indent: 4 */

/**
 * This file serves as the core of TubePress's JavaScript presence. It contains
 *
 *  1. Utility functions, mostly to help with language and runtime tasks.
 *  2. An event bus.
 *  3. Functions to asychronously load other TubePress scripts as needed.
 */

var TubePress = (function (jquery, win) {

    /** http://ejohn.org/blog/ecmascript-5-strict-mode-json-and-more/ */
    'use strict';

    /**
     * Let's start with some variable declarations to help us with compression.
     */
    var text_tubepress    = 'tubepress',
        windowLocation    = win.location,
        dokument          = win.document,
        troo              = true,
        coreJsPrefix      = 'src/main/web/js',
        jquery_isFunction = jquery.isFunction,

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
                    fileref.href = path;

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

                    loadJs(coreJsPrefix + '/gallery.js');
                },

                loadPlayerApiJs = function () {

                    loadJs(coreJsPrefix + '/playerApi.js');
                },

                loadAjaxSearchJs = function () {

                    loadJs(coreJsPrefix + '/ajaxSearch.js');
                };

            return {

                loadJs           : loadJs,
                loadCss          : loadCss,
                loadGalleryJs    : loadGalleryJs,
                loadPlayerApiJs  : loadPlayerApiJs,
                loadAjaxSearchJs : loadAjaxSearchJs
            };
        }()),

        /**
         * Core of our asychronous APIs.
         */
        asyncConverter = (function () {

            //http://tmxcredit.com/tech-blog/understanding-javascript-asynchronous-apis/
            var convertQueueToFunctionCalls = function (asyncObjectName, delegate) {

                var loggerOn = logger.on(),
                    queueLength,

                    queue = win[asyncObjectName],

                    queueCall = function (callArray) {

                        var method = callArray[0],
                            args   = callArray.slice(1);

                        delegate[method].apply(this, args);
                    };

                if (langUtils.isDefined(queue)) {

                    queueLength = queue.length;

                    if (loggerOn) {

                        logger.log('Running ' + queueLength + ' queue items for ' + asyncObjectName);
                    }

                    // loop through our existing queue, calling methods in order
                    queue.reverse();

                    while (queue.length) {

                        queueCall(queue.pop());
                    }
                }

                if (loggerOn) {

                    logger.log(asyncObjectName + ' is now connected');
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
             * https://github.com/jquery/jquery/blob/1.8-stable/src/ajax.js#L203
             */
            var load = function (method, url, targetDiv, selector, preLoadFunction, postLoadFunction) {

                var rscript = /<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi,

                    completeCallback = function (res) {

                        var responseText = res.responseText,
                            html         = selector ? jquery('<div>').append(responseText.replace(rscript, '')).find(selector) : responseText;

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
        }());

    /**
     * Convert any queued calls to their real counterparts.
     */
    (function () {

        var textCamelCaseTubePress = 'tubePress';

        asyncConverter.processQueueCalls(textCamelCaseTubePress + 'DomInjector', domInjector);
        asyncConverter.processQueueCalls(textCamelCaseTubePress + 'Beacon', beacon);

    }());

    /**
     * Stuff we expose to everyone else.
     */
    return {

        Ajax : {

            Executor   : ajaxExecutor,
            LoadStyler : loadStyler
        },

        AsyncUtil   : asyncConverter,
        Beacon      : beacon,
        DomInjector : domInjector,
        Environment : environment,

        Lang : {

            Utils      : langUtils,
            JsonParser : jsonParser
        },

        Logger : logger
    };

}(jQuery, window));
