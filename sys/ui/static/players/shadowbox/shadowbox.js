/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org) and is released 
 * under the General Public License (GPL) version 3
 *
 * Shrink your JS: http://developer.yahoo.com/yui/compressor/
 */
var TubePressShadowboxPlayer = (function () {
	
	/* this stuff helps compression */
	var events	= TubePressEvents,
		name	= 'shadowbox',
		jquery	= jquery,
		doc		= jquery(document),
		sb	= Shadowbox,
		url		= getTubePressBaseUrl() + '/sys/ui/static/players/shadowbox/',

		boot	= function () {

			if (typeof sb === 'undefined') {
				setTimeout(boot, 400);
				return;
			}
		
			sb.path = url + 'lib/';
			sb.init({
				initialHeight	: 160,
				initialWidth	: 320,
				skipSetup		: true, 
				players			: ['html'],
				useSizzle		: false
			});
			sb.load();
		},
		
		invoke = function (e, videoId, galleryId, width, height) {
			sb.open({
				player:		'html',
				height:		height,
				width:		width,
				content:	'&nbsp;'
			});
		},
		
		populate = function (e, title, html, height, width, videoId, galleryId) {
			
			if (!jquery('#sb-player').length) {
				setTimeout( function () { populate(e, title, html, height, width, videoId, galleryId); }, 200);
				console.log('waiting');
				return;
			}
			
			jquery('#sb-player').html(html);
		};
		
	jquery.getScript(url + 'lib/shadowbox.js', function () {}, true);
	TubePressCss.load(url + 'lib/shadowbox.css');
	boot();
	
	doc.bind(events.PLAYER_INVOKE + name, invoke);
	doc.bind(events.PLAYER_POPULATE + name, populate);
}());
