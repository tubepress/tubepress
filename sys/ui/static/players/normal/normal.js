/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org) and is released 
 * under the General Public License (GPL) version 3
 */
var TubePressNormalPlayer = (function () {
	
	var getTitleId = function (gId) {
		return "#tubepress_embedded_title_" + gId;
	},
	
		/* this stuff helps compression */
		jquery	= jQuery,
		tpAjax	= TubePressAjax,
		events	= TubePressEvents,
		name	= 'normal',
		doc		= jquery(document),
	
		applyLoadingStyle = function (id) {
			tpAjax.applyLoadingStyle(id);
		},
		
		removeLoadingStyle = function (id) {
			tpAjax.removeLoadingStyle(id);
		},
	
		getEmbedId = function (gId) {
			return '#tubepress_embedded_object_' + gId;
		},
	
		invoke = function (e, videoId, galleryId, width, height) {

			var titleDivId = getTitleId(galleryId);
			
			applyLoadingStyle(titleDivId);
			applyLoadingStyle(getEmbedId(galleryId));

			jquery(titleDivId)[0].scrollIntoView(true);
		},
		
		populate = function (e, title, html, height, width, videoId, galleryId) {
			
			jquery('#tubepress_gallery_' + galleryId + ' div.tubepress_normal_embedded_wrapper:first').replaceWith(html);
			
			removeLoadingStyle(getTitleId(galleryId));
			removeLoadingStyle(getEmbedId(galleryId));
		};

	doc.bind(events.PLAYER_INVOKE + name, invoke);
	doc.bind(events.PLAYER_POPULATE + name, populate);
} ());


