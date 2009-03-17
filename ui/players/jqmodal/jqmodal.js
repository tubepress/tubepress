function tubepress_jqmodal_player(galleryId, videoId) {
   jQuery("#tubepress_embedded_object_" + galleryId).jqmShow(); 
}

function tubepress_jqmodal_player_init(baseUrl) {
    var base = baseUrl + '/ui/players/jqmodal/lib/';
	jQuery.include(base + 'jqModal.css');
	_tubepress_get_wait_call(base + 'jqModal.js',
		function() { return typeof jQuery.fn.jqm == 'function'; },
		function() { _tubepress_jqmodal_init(); }
	);
}

function _tubepress_jqmodal_init() {
	jQuery("div[id^='tubepress_embedded_object_']").each(function() {
        jQuery(this).addClass('jqmWindow');     
        jQuery(this).jqm(); 
    });
}
