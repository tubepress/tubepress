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

/*global jQuery, TubePressAjax, TubePressEvents, getTubePressBaseUrl, TubePressCss, Shadowbox */
/*jslint sloppy: true, white: true, onevar: true, undef: true, newcap: true, nomen: true, regexp: true, plusplus: true, bitwise: true, continue: true, browser: true, maxerr: 50, indent: 4 */


var TubePressShadowboxPlayer = (function () {
	
	/* this stuff helps compression */
	var events	= TubePressEvents,
		name	= 'shadowbox',
		jquery	= jQuery,
		doc		= jquery(document),
		url		= getTubePressBaseUrl() + '/sys/ui/static/players/shadowbox/',

		boot	= function () {

			if (typeof Shadowbox === 'undefined') {
				setTimeout(boot, 400);
				return;
			}
		
			Shadowbox.path = url + 'lib/';
			Shadowbox.init({
				initialHeight	: 160,
				initialWidth	: 320,
				skipSetup		: true, 
				players			: ['html'],
				useSizzle		: false
			});
			Shadowbox.load();
		},
		
		invoke = function (e, videoId, galleryId, width, height) {
			Shadowbox.open({
				player:		'html',
				height:		height,
				width:		width,
				content:	'&nbsp;'
			});
		},
		
		populate = function (e, title, html, height, width, videoId, galleryId) {
			
			if (!jquery('#sb-player').length) {
				setTimeout( function () { populate(e, title, html, height, width, videoId, galleryId); }, 200);
				return;
			}
			
			jquery('#sb-player').html(html);
		};
		
	jQuery.getScript(url + 'lib/shadowbox.js', function () {}, true);
	TubePressCss.load(url + 'lib/shadowbox.css');

	boot();
	
	doc.bind(events.PLAYER_INVOKE + name, invoke);
	doc.bind(events.PLAYER_POPULATE + name, populate);
}());