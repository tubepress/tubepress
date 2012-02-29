/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/*global jQuery, TubePressAjax, TubePressEvents */
/*jslint sloppy: true, white: true, onevar: true, undef: true, newcap: true, nomen: true, regexp: true, plusplus: true, bitwise: true, continue: true, browser: true, maxerr: 50, indent: 4 */

var TubePressNormalPlayer = (function () {
	
	var prefix  = 'tubepress_',
	
		getTitleId = function (gId) {
			return '#' + prefix + 'embedded_title_' + gId;
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
			return '#' + prefix + 'embedded_object_' + gId;
		},
	
		invoke = function (e, videoId, galleryId, width, height) {

			var titleDivId = getTitleId(galleryId);
			
			applyLoadingStyle(titleDivId);
			applyLoadingStyle(getEmbedId(galleryId));

			jquery(titleDivId)[0].scrollIntoView(true);
		},
		
		populate = function (e, title, html, height, width, videoId, galleryId) {
			
			jquery('#' + prefix + 'gallery_' + galleryId + ' div.' + prefix + 'normal_embedded_wrapper:first').replaceWith(html);
			
			removeLoadingStyle(getTitleId(galleryId));
			removeLoadingStyle(getEmbedId(galleryId));
		};

	doc.bind(events.PLAYER_INVOKE + name, invoke);
	doc.bind(events.PLAYER_POPULATE + name, populate);
} ());


