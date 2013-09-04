var TubePressBoxes = [];

TubePressBoxHandler = (function () {

    'use strict';

    //http://www.aaronpeters.nl/blog/iframe-loading-techniques-performance

    var boxParentDiv = '#tubepress-box-holder',

        createAndAppendIframe = function (title) {

            var titleDiv = jQuery('<div/>', {

                    'class' : 'ui-widget ui-widget-header tubepress-participant-header',
                    'style' : 'margin-bottom: 0'

                }).append(jQuery('<span/>').html(title)),

                iframe = jQuery('<iframe/>');

            jQuery('<div/>', {

                'class' : 'ui-corner-all ui-widget-content tubepress-box'

            }).append(titleDiv).append(iframe).appendTo(boxParentDiv);

            return iframe[0];
        },

        writeUrlInIframe = function (iframeElement, url) {

            var doc = iframeElement.contentWindow.document;

            doc.open().write('<body onload="var d = document;d.getElementsByTagName(\'head\')[0].appendChild(d.createElement(\'script\')).src=\'' + url.replace(/\//g, '\\/') + '\'">');

            doc.close();
        },

        load = function (box) {

            var iframe = createAndAppendIframe(box.title);

            writeUrlInIframe(iframe, box.url);
        },

        init = function () {

            if (window.TubePressBoxes === undefined) {

                return;
            }

            var i;

            for (i = 0; i < TubePressBoxes.length; i += 1) {

                load(TubePressBoxes[i]);
            }
        };

    return {

        init : init
    };

}());

jQuery(document).ready(function () {

    'use strict';

    TubePressBoxHandler.init();
});