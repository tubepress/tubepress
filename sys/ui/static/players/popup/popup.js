/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org) and is released 
 * under the General Public License (GPL) version 3
 *
 * Shrink your JS: http://developer.yahoo.com/yui/compressor/
 */

/*global jQuery, TubePressEvents */
/*jslint white: true, onevar: true, undef: true, newcap: true, nomen: true, regexp: true, plusplus: true, bitwise: true, continue: true, browser: true, maxerr: 50, indent: 4 */

var TubePressPopupPlayer = (function () {
	
	/* this stuff helps compression */
	var events	= TubePressEvents,
		name	= 'popup',
		doc		= jQuery(document),
		windows = {},
		
		invoke = function (e, videoId, galleryId, width, height) {

			var top		= (screen.height / 2) - (height / 2),
				left	= (screen.width / 2) - (width / 2);
			
			windows[galleryId + videoId] = window.open('', '', 'location=0,directories=0,menubar=0,scrollbars=0,status=0,toolbar=0,width=' + width + 'px,height=' + height + 'px,top=' + top + ',left=' + left);
		},
		
		populate = function (e, title, html, height, width, videoId, galleryId) {
			
			var preamble	= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">\n<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html;charset=utf-8" /><title>' + title + '</title></head><body style="margin: 0pt; background-color: black;">',
				js			= '<script type="text/javascript">var TubePressPlayerApi = window.opener.TubePressPlayerApi;</script>',
				postAmble	= '</body></html>',
				wind		= windows[galleryId + videoId].document;

			wind.write(preamble + js + html + postAmble);
			wind.close();

		};

	doc.bind(events.PLAYER_INVOKE + name, invoke);
	doc.bind(events.PLAYER_POPULATE + name, populate);
}());