function tubepress_shadowbox_player_init(baseUrl) {
	var shadowboxBase = baseUrl + '/ui/players/shadowbox/';
	jQuery.include([shadowboxBase + 'lib/shadowbox-2.0.js'], function() {
		jQuery.include([shadowboxBase + 'lib/skin/classic/skin.js', shadowboxBase + 'lib/skin/classic/skin.css'], function() {
			Shadowbox.init({skipSetup: true});
		});
	});
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
