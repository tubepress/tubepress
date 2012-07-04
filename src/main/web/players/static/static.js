/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org) and is released 
 * under the General Public License (GPL) version 3
 *
 * Shrink your JS: http://developer.yahoo.com/yui/compressor/
 */
var TubePressStaticPlayer = (function () {

	/* this stuff helps compression */
	var events	= TubePressEvents,
		doc		= jQuery(document),
		anchors = TubePressThumbs,
		jquery	= jQuery,
		
		scanAndModifyThumbs = function () {
		
			jquery("a[id^='tubepress_']").each(function () {
				
				var dis 		= jquery(this),
					rel_split 	= dis.attr('rel').split('_'),
					page, newId, newUrl, galleryId;
				
				galleryId	= anchors.getGalleryIdFromRelSplit(rel_split);
				
				if (TubePressGallery.getPlayerLocationName(galleryId) !== 'static') {
					return;
				}
				
				newId		= anchors.getVideoIdFromIdAttr(dis.attr('id'));
				page		= anchors.getCurrentPageNumber(galleryId);
				newUrl		= jquery.query.set('tubepress_video', newId).set('tubepress_page', page).toString();
	
				dis.attr('href', newUrl);
				dis.unbind('click');
			});
		};

	jquery.getScript(getTubePressBaseUrl() + '/sys/ui/static/players/static/lib/jQuery.query.js', scanAndModifyThumbs, true);	
	doc.bind(events.NEW_THUMBS_LOADED, scanAndModifyThumbs);
	
}());