/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org) and is released 
 * under the General Public License (GPL) version 3
 *
 * Shrink your JS: http://developer.yahoo.com/yui/compressor/
 */
function tubepress_static_player_readyTest() {
	return typeof jQuery.query !== 'undefined';
}

function tubepress_static_player_initt() {
	jQuery("a[id^='tubepress_']").each(function () {
		
		var dis = jQuery(this), rel_split = dis.attr('rel').split('_'),
			page, newId, newUrl, galleryId;
		
		if (TubePressAnchors.getPlayerNameFromRelSplit(rel_split) !== 'static') {
			return;
		}
		
		galleryId	= TubePressAnchors.getGalleryIdFromRelSplit(rel_split);
		newId		= TubePressAnchors.getVideoIdFromIdAttr(dis.attr('id'));
		page		= TubePressGallery.getCurrentPageNumber(galleryId);
		newUrl		= jQuery.query.set('tubepress_video', newId).set('tubepress_page', page).toString();

		dis.attr('href', newUrl);
		dis.unbind('click');
	});
}

function tubepress_static_player_init(baseUrl) {
	TubePressJS.getWaitCall(baseUrl + '/sys/ui/static/players/static/lib/jQuery.query.js',
		tubepress_static_player_readyTest,
		tubepress_static_player_initt);
	jQuery(document).bind(TubePressEvents.NEW_THUMBS_LOADED, function (x) {
		tubepress_static_player_initt();
	});
}

function tubepress_static_player(galleryId, videoId) {
   //do nothing
}
