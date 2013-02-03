/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
var TubePressSoloPlayer = (function () {

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
				
				if (TubePressGallery.getPlayerLocationName(galleryId) !== 'solo') {
					return;
				}
				
				newId		= anchors.getVideoIdFromIdAttr(dis.attr('id'));
				page		= anchors.getCurrentPageNumber(galleryId);
				newUrl		= jquery.query.set('tubepress_video', newId).set('tubepress_page', page).toString();
	
				dis.attr('href', newUrl);
				dis.unbind('click');
			});
		};

	jquery.getScript(TubePressGlobalJsConfig.baseUrl + '/src/main/web/players/solo/lib/jQuery.query.js', scanAndModifyThumbs, true);
	doc.bind(events.NEW_THUMBS_LOADED, scanAndModifyThumbs);
	
}());