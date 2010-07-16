/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org) and is released 
 * under the General Public License (GPL) version 3
 *
 * Shrink your JS: http://developer.yahoo.com/yui/compressor/
 */
function tubepress_static_player_init(baseUrl) {
	TubePressUtils.getWaitCall(baseUrl + '/ui/players/static/lib/jQuery.query.js',
		_tubepress_static_player_readyTest,
		_tubepress_static_player_init);
	jQuery(document).bind('tubepressNewThumbnailsLoaded', function (x) {
		_tubepress_static_player_init();
	});
}

function _tubepress_static_player_readyTest() {
	return typeof jQuery.query != 'undefined';
}

function _tubepress_static_player_init() {
	jQuery("a[id^='tubepress_']").each(function() {
		var dis 	= jQuery(this),
		    rel_split 	= dis.attr('rel').split('_');
		
		if (TubePress.getPlayerNameFromRelSplit(rel_split) != 'static') {
			return;
		}
		var newId 	= TubePress.getVideoIdFromIdAttr(dis.attr("id")),
			page	= jQuery(dis).parents("div.tubepress_thumbnail_area:first > div.pagination:first > span.current").html(),
		    	newUrl 	= jQuery.query.set('tubepress_video', newId).set('tubepress_page', page).toString();
		dis.attr('href', newUrl);
		dis.unbind('click', TubePress.clickListener);
	});
}

function tubepress_static_player(galleryId, videoId) {
   //do nothing
}
