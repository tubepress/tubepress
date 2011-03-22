/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org) and is released 
 * under the General Public License (GPL) version 3
 *
 * Shrink your JS: http://developer.yahoo.com/yui/compressor/
 */
function tubepress_init_shadowbox(base) {
	Shadowbox.path = base + 'lib/';
	Shadowbox.init({
		initialHeight	: 160,
		initialWidth	: 320,
		skipSetup		: true, 
		players			: ['html'],
		useSizzle		: false
	});
	Shadowbox.load();
}

function tubepress_shadowbox_player_shadowboxjs(base)  {
	TubePressJS.getWaitCall(base + 'lib/shadowbox.js',
		function () {
			return typeof Shadowbox !== 'undefined';
		},
		function () {
			tubepress_init_shadowbox(base);
		}
	);
}

function tubepress_shadowbox_player_init(baseUrl) {
	var url = baseUrl + '/sys/ui/static/players/shadowbox/';
	
	TubePressJS.loadCss(url + 'lib/shadowbox.css');
	tubepress_shadowbox_player_shadowboxjs(url);
}

function tubepress_shadowbox_player(galleryId, videoId) {
	var html				= TubePressEmbedded.getHtmlForCurrentEmbed(galleryId),
		videoTitleAnchor	= jQuery('#tubepress_title_' + videoId + '_' + galleryId),
		embedWidth			= TubePressEmbedded.getWidthOfCurrentEmbed(galleryId),
		embedHeight			= TubePressEmbedded.getHeightOfCurrentEmbed(galleryId);

	Shadowbox.open({
		player:		'html',
		title:		videoTitleAnchor.html(),
		content:	html,
		height:		embedHeight,
		width:		embedWidth
	});
}
