function tubepress_lightwindow_player_init(baseUrl) {
	if (typeof Prototype == 'undefined') {
		_tubepress_lightwindow_load_prototype(baseUrl);
	} else {
		_tubepress_lightwindow_load_scriptaculous(baseUrl);
	}
}

function _tubepress_lightwindow_load_prototype(baseUrl) {
	jQuery.include([baseUrl + '/ui/players/lightwindow/lib/javascript/prototype.js'], function() {
		_tubepress_lightwindow_load_scriptaculous(baseUrl);
	});
}

function _tubepress_lightwindow_load_scriptaculous(baseUrl) {
	jQuery.include([baseUrl + '/ui/players/lightwindow/lib/javascript/effects.js'], function() {
		_tubepress_lightwindow_load_lightwindow(baseUrl);
	});
}

function _tubepress_lightwindow_load_lightwindow(baseUrl) {
	var base = baseUrl + '/ui/players/lightwindow/lib/';
	jQuery.include([base + 'javascript/lightWindow.js', base + 'css/lightWindow.css'], function() {
        var options = {
            overlay : {
	    		image : base + 'css/images/black.png',
	    		presetImage : base + 'css/images/black-70.png' 
            }	
    	}
        lightwindowInit(options);
        myLightWindow.options.skin.loading = myLightWindow.options.skin.loading.replace('images', base + 'css/images');
	});
}

function tubepress_lightwindow_player(galleryId, videoId) {
    var embeddedId = '#tubepress_embedded_object_' + galleryId;
    var embeddedObject = jQuery(embeddedId + " > object");
	myLightWindow.activateWindow({
		href: embeddedId,
		title: jQuery("#tubepress_image_" + videoId + "_" + galleryId + " > img").attr("alt"),
        type: 'inline',
        height: embeddedObject.css("height"),
        width: embeddedObject.css("width")
	});
}
