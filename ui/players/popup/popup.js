function tubepress_popup_player(galleryId, videoId) {
    var win = new tp_PopupWindow(document.getElementById("#tubepress_embedded_object_" + galleryId));
    win.showPopup("tubepress_gallery_" + galleryId);
}

function tubepress_popup_init(baseUrl) { 
    if (typeof tp_PopupWindow != 'function') {
        jQuery.getScript(baseUrl + "/ui/players/popup.js");
    }
}
