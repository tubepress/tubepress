function tubepress_shadowbox_player_init(baseUrl) {
	var base = baseUrl + '/ui/players/shadowbox/';
	//jQuery.include(base + 'lib/shadowbox.css', function(){
		_tubepress_shadowbox_player_shadowboxjs(base);
	//});
}

function _tubepress_shadowbox_player_shadowboxjs(base)  {
	_tubepress_get_wait_call(base + 'lib/shadowbox.js',
			function() { return typeof Shadowbox != 'undefined'; },
			function() { _tubepress_shadowbox_player_shadowboxlang(base); }
		);
}

function _tubepress_shadowbox_player_shadowboxlang(base) {
	_tubepress_get_wait_call(base + 'lib/languages/shadowbox-en.js',
			function() { return typeof Shadowbox.lang != 'undefined'; },
			function() { _tubepress_shadowbox_player_shadowboxplayer(base); }
		);
}

function _tubepress_shadowbox_player_shadowboxplayer(base) {
	_tubepress_get_wait_call(base + 'lib/players/shadowbox-html.js',
			function() { return typeof Shadowbox.html != 'undefined'; },
			function() { _tubepress_shadowbox_player_shadowboxadapter(base); }
		);
}

function _tubepress_shadowbox_player_shadowboxadapter(base) {
	_tubepress_get_wait_call(base + 'lib/adapters/shadowbox-jquery.js',
			function() { return typeof Shadowbox.lib != 'undefined'; },
			function() { _tubepress_init_shadowbox(base); }
		);
}

function tubepress_shadowbox_player(galleryId, videoId) {
	var wrapperId = "#tubepress_embedded_object_" + galleryId,
		wrapper = jQuery(wrapperId),
		obj = jQuery(wrapperId + " > object"),
		params = obj.children("param"),
		videoTitleAnchor = jQuery("#tubepress_title_" + videoId + "_" + galleryId);
	Shadowbox.open({
	    player:       'html',
	    title:        videoTitleAnchor.html(),
	    content:      tubepress_deep_construct_object(wrapper, params),
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

