function tubepress_popup_player(galleryId, videoId) {
    var win = new tp_PopupWindow("tubepress_embedded_object_" + galleryId);
    win.autoHide(); 
    win.showPopup("tubepress_image_" + videoId + "_" + galleryId);
}

function tubepress_popup_init(baseUrl) { 
    if (typeof tp_PopupWindow != 'function') {
        jQuery.getScript(baseUrl + "/ui/players/popup.js");
    }
}
