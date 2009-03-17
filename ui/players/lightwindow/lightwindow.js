function tubepress_lightwindow_player_init(baseUrl) {
	jQuery.include(baseUrl + '/ui/players/lightwindow/lib/css/lightWindow.css');
	if (typeof Prototype == 'undefined') {
		_tubepress_lightwindow_load_prototype(baseUrl);
		return;
	} 
	if (typeof Effects == 'undefined') {
		_tubepress_lightwindow_load_scriptaculous(baseUrl);
		return;
	}
	_tubepress_lightwindow_load_lightwindow(baseUrl);
}

function _tubepress_lightwindow_load_prototype(baseUrl) {
	_tubepress_get_wait_call(baseUrl + '/ui/players/lightwindow/lib/javascript/prototype.js',
			function() { return typeof Prototype != 'undefined'; },
			function() { _tubepress_lightwindow_load_scriptaculous(baseUrl); }
	);
}

function _tubepress_lightwindow_load_scriptaculous(baseUrl) {
	_tubepress_get_wait_call(baseUrl + '/ui/players/lightwindow/lib/javascript/effects.js',
			function() { return typeof Effect != 'undefined'; },
			function() { _tubepress_lightwindow_load_lightwindow(baseUrl); }
	);
}

function _tubepress_lightwindow_load_lightwindow(baseUrl) {
	var base = baseUrl + '/ui/players/lightwindow/lib/';
	_tubepress_get_wait_call(base + 'javascript/lightWindow.js',
			function() { return typeof lightwindowInit == 'function'; },
			function() { _tubepress_lightwindow_init_lightwindow(base); }
	);
}

function _tubepress_lightwindow_init_lightwindow(base) {
	var options = {
            overlay : {
	    		image : base + 'css/images/black.png',
	    		presetImage : base + 'css/images/black-70.png' 
            }	
    	}
    lightwindowInit(options);
    myLightWindow.options.skin.loading = myLightWindow.options.skin.loading.replace('images', base + 'css/images');
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
