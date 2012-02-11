/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org) and is released 
 * under the General Public License (GPL) version 3
 *
 * Shrink your JS: http://developer.yahoo.com/yui/compressor/
 */
var TubePressVimeoPlayer = (function () {
	
	/* this stuff helps compression */
	var events	= TubePressEvents,
		name	= 'vimeo',
		
		invoke = function (e, videoId, galleryId, width, height) {

			window.location = 'http://www.vimeo.com/' + videoId;
		};

	jQuery(document).bind(events.PLAYER_INVOKE + name, invoke);
}());