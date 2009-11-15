function tubepress_normal_player(galleryId, videoId) {
    var videoTitleAnchor = jQuery("#tubepress_title_" + videoId + "_" + galleryId),
        embeddedTitleId = "#tubepress_embedded_title_" + galleryId,
        mainTitleDiv = jQuery(embeddedTitleId);
    mainTitleDiv.html(videoTitleAnchor.html());
    jQuery(embeddedTitleId)[0].scrollIntoView(true);
}

function tubepress_normal_player_init(baseUrl) { }
