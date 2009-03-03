function tubepress_shadowbox_player(galleryId, videoId) {

}

function tubepress_shadowbox_init(baseUrl) { 
    if (typeof tp_Shadowbox == "undefined") {
        jQuery.getScript(baseUrl + "/ui/players/shadowbox/lib/shadowbox-2.0.js");
    }
}
