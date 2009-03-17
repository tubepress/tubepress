function tubepress_shadowbox_player_init(baseUrl) {
	var shadowboxBase = baseUrl + '/ui/players/shadowbox/';
	jQuery.include(shadowboxBase + 'lib/skin/classic/skin.css');
	_tubepress_load_shadowbox_base(shadowboxBase);
}

function tubepress_shadowbox_player(galleryId, videoId) {
    var flashObj = jQuery("#tubepress_embedded_object_" + galleryId + " > object");
	Shadowbox.open({
	    player:       'html',
	    title:        jQuery("#tubepress_image_" + videoId + "_" + galleryId + " > img").attr("alt"),
	    content:      jQuery("#tubepress_embedded_object_" + galleryId).html(),
	    height:       flashObj.css("height"),
	    width:        flashObj.css("width")
	});
}

function _tubepress_load_shadowbox_skin(base) {
	_tubepress_get_wait_call(base + 'lib/skin/classic/skin.js',
		function() { return typeof Shadowbox.SKIN != 'undefined'; },
		function() { Shadowbox.init({skipSetup: true}); }
	);
}

function _tubepress_load_shadowbox_base(base) {
	_tubepress_get_wait_call(base + 'lib/shadowbox-2.0.js',
		function() { return typeof Shadowbox.init == 'function'; },
		function() { _tubepress_load_shadowbox_skin(base); }
	);
}