
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
(function (jquery) {

    var bus = jquery({}),

        subscribe = function () {

            bus.on.apply(bus, arguments);
        },

        unsubscribe = function () {

            bus.off.apply(bus, arguments);
        },

        publish = function () {

            bus.trigger.apply(bus, arguments);
        };

    window.tubePressBeacon = {

        subscribe   : subscribe,
        unsubscribe : unsubscribe,
        publish     : publish
    };

}(jQuery));
