function tubepress_shadowbox_player(galleryId, videoId) {

}

function tubepress_shadowbox_preload(baseUrl) { 
    if (typeof tp_Shadowbox == "undefined") {
        jQuery.getScript(baseUrl + "/ui/players/shadowbox/lib/shadowbox-2.0.js", function() {
            Shadowbox.loadSkin('classic', baseUrl + '/ui/players/shadowbox/lib/skin');
        });
    }
}

function tubepress_shadowbox_postload(baseUrl) {

}
