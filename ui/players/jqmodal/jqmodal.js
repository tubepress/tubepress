function tubepress_jqmodal_player(galleryId, videoId) {
   jQuery("#tubepress_embedded_object_" + galleryId).jqmShow(); 
}

function tubepress_jqmodal_player_init(baseUrl) {
    var base = baseUrl + '/ui/players/jqmodal/lib/';
	jQuery.include([base + 'jqModal.js', base + 'jqModal.css'], function() {
        jQuery("div[id^='tubepress_embedded_object_']").each(function() {
            jQuery(this).addClass('jqmWindow');     
            jQuery(this).jqm(); 
        });
    });
}
