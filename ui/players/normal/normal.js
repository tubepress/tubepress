function tubepress_normal_player(galleryId, videoId) {
    var videoTitleAnchor = jQuery("#tubepress_title_" + videoId + "_" + galleryId);
    var mainTitleDiv = jQuery("#tubepress_embedded_title_" + galleryId);
    mainTitleDiv.html(videoTitleAnchor.html());
    jQuery("#tubepress_gallery_" + galleryId + "_anchor")[0].scrollIntoView(true);
}

function tubepress_normal_player_init(baseUrl) { }
