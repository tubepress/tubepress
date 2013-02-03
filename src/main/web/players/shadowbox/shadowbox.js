/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/*global jQuery, TubePressAjax, TubePressEvents, getTubePressBaseUrl, TubePressCss, Shadowbox */
/*jslint sloppy: true, white: true, onevar: true, undef: true, newcap: true, nomen: true, regexp: true, plusplus: true, bitwise: true, continue: true, browser: true, maxerr: 50, indent: 4 */


var TubePressShadowboxPlayer = (function () {
	
	/* this stuff helps compression */
	var events	= TubePressEvents,
		name	= 'shadowbox',
		jquery	= jQuery,
		doc		= jquery(document),
		url		= TubePressGlobalJsConfig.baseUrl + '/src/main/web/players/shadowbox/',

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