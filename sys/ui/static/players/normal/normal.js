/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org) and is released 
 * under the General Public License (GPL) version 3
 *
 * Shrink your JS: http://developer.yahoo.com/yui/compressor/
 */
function tubepress_normal_player(title, html, height, width, videoId, galleryId) {

	var	titleElement = jQuery("#tubepress_embedded_title_" + galleryId),
		content = jQuery("#tubepress_embedded_object_" + galleryId);
	
	content.empty();
	content.html(html);
	titleElement.html(title);
	titleElement[0].scrollIntoView(true);
}

function tubepress_normal_player_init(baseUrl) { }
