function tubepress_colorbox_player(galleryId, videoId) {
	//colorbox({contentInline:"#tubepress_embedded_object_" + galleryId});
}

function tubepress_colorbox_player_init(baseUrl) {
	var base = baseUrl + '/ui/players/colorbox/lib/';
	jQuery.include([base + 'colorbox.css', base + 'colorbox-custom.css']);
	_tubepress_get_wait_call(base + 'jquery.colorbox.js',
		function() { return typeof jQuery.fn.colorbox != 'undefined'; },
		function() { _tubepress_colorbox_add_listeners(); }
	);
}

function _tubepress_colorbox_add_listeners() {
	jQuery("a[id^='tubepress_']").each(function () {
		var rel_split    = jQuery(this).attr("rel").split("_");
		var playerName   = rel_split[2];
		if (playerName != 'colorbox') {
			return;
		}
		var galleryId    	= rel_split[3];
		var embeddedObject 	= jQuery('#tubepress_embedded_object_' + galleryId + ' > object');
		var height = embeddedObject.css('height');
		var width = embeddedObject.css('width');
		jQuery(this).colorbox({
			contentInline	:"#tubepress_embedded_object_" + galleryId,
			preloading		: false,
			contentWidth	: width,
			contentHeight	: height
			initialWidth	: ceil(width / 2),
			initialHeight	: ceil(height / 2)
		});
    });
}