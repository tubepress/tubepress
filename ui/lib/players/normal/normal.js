/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org) and is released 
 * under the General Public License (GPL) version 3
 *
 * Shrink your JS: http://developer.yahoo.com/yui/compressor/
 */
function tubepress_normal_player(galleryId, videoId) {
	
	var videoTitleAnchor	= jQuery('#tubepress_title_' + videoId + '_' + galleryId),
		embeddedTitleId		= '#tubepress_embedded_title_' + galleryId,
		mainTitleDiv		= jQuery(embeddedTitleId);
	
	mainTitleDiv.html(videoTitleAnchor.html());
	jQuery(embeddedTitleId)[0].scrollIntoView(true);
}

function tubepress_normal_player_init(baseUrl) { }
