/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org) and is released 
 * under the General Public License (GPL) version 3
 *
 * Shrink your JS: http://developer.yahoo.com/yui/compressor/
 */
function tubepress_shadowbox_player_init(baseUrl) {
	var url = baseUrl + '/ui/players/shadowbox/';
	
	TubePressUtils.loadCss(url + 'lib/shadowbox.css');
	_tubepress_shadowbox_player_shadowboxjs(url);
}

function _tubepress_shadowbox_player_shadowboxjs(base)  {
	TubePressUtils.getWaitCall(base + 'lib/shadowbox.js',
		function () { return typeof Shadowbox != 'undefined'; },
		function () { _tubepress_init_shadowbox(base); }
	);
}

function tubepress_shadowbox_player(galleryId, videoId) {
	var wrapperId 			= "#tubepress_embedded_object_" + galleryId,
		wrapper 		= jQuery(wrapperId),
		obj 			= jQuery(wrapperId + " > object"),
		params 			= obj.children("param"),
		videoTitleAnchor 	= jQuery("#tubepress_title_" + videoId + "_" + galleryId);
	
	Shadowbox.open({
	    player:       'html',
	    title:        videoTitleAnchor.html(),
	    content:      TubePress.deepConstructObject(wrapper, params),
	    height:       obj.css("height"),
	    width:        obj.css("width")
	});
}

function _tubepress_init_shadowbox(base) {
	Shadowbox.path = base + 'lib/';
	Shadowbox.init({
		initialHeight : 160,
		initialWidth: 320,
		skipSetup: true, 
		players: ["html"],
		useSizzle: false
	});
	Shadowbox.load();
}

