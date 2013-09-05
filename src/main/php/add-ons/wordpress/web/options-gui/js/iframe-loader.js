var TubePressIframeLoader = (function () {

    var loadIframe = function (rawBox) {

            var box     = jQuery(rawBox),
                url     = box.data('src'),
                iframes = jQuery('<iframe />'),
                iframe  = iframes[0],
                doc     = null;

            box.append(iframe);

            doc = iframe.contentWindow.document;

            doc.open().write('<body onload="var d = document;d.getElementsByTagName(\'head\')[0].appendChild(d.createElement(\'script\')).src=\'' + url.replace(/\//g, '\\/') + '\'">');

            doc.close();
        },

        init = function () {

            var boxes = jQuery('div.has-iframe');

            jQuery.each(boxes, function (index, value) {

                loadIframe(value);
            });
        };

    return { init : init };

}());

jQuery(function () {

    TubePressIframeLoader.init();
});