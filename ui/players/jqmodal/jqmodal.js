function tubepress_jqmodal_player(galleryId, videoId) {
    var element = jQuery("#tubepress_embedded_object_" + galleryId);
    element.addClass('jqmWindow');     
    element.jqm(); 
    element.jqmShow();
}

function tubepress_jqmodal_player_init(baseUrl) {
    jQuery.getScript(baseUrl + '/ui/players/jqmodal/lib/jqModal.js', function() {}, true);
    TubePressUtils.loadCss(baseUrl + '/ui/players/jqmodal/lib/jqModal.css');
}
