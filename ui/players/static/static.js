function tubepress_static_player(galleryId, videoId) {alert('hey');}

function tubepress_static_player_init(baseUrl) {
	tubepress_get_wait_call(baseUrl + '/ui/players/static/lib/jQuery.query.js',
			_tubepress_static_player_readyTest,
			_tubepress_static_player_init);
}

function _tubepress_static_player_readyTest() {
	return typeof jQuery.query != 'undefined';
}

function _tubepress_static_player_init() {
	jQuery("a[id^='tubepress_']").each(function() {
		var dis = jQuery(this),
		    split = dis.attr('rel').split('_');
		if (split[2] != 'static') {
			return;
		}
		var newId = split[2],
		    newUrl = jQuery.query.set('tubepress_video', newId).toString();
		dis.attr('href', newUrl);
		dis.unbind('click', tubepress_click_listener);
	});
}