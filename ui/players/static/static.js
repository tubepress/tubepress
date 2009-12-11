function tubepress_static_player_init(baseUrl) {
	TubePress.getWaitCall(baseUrl + '/ui/players/static/lib/jQuery.query.js',
			_tubepress_static_player_readyTest,
			_tubepress_static_player_init);
}

function _tubepress_static_player_readyTest() {
	return typeof jQuery.query != 'undefined';
}

function _tubepress_static_player_init() {
	jQuery("a[id^='tubepress_']").each(function() {
		var dis = jQuery(this),
		    rel_split = dis.attr('rel').split('_');
		if (rel_split[2] != 'static') {
			return;
		}
		var newId = dis.attr('id').split('_')[2],
		    newUrl = jQuery.query.set('tubepress_video', newId).toString();
		dis.attr('href', newUrl);
		dis.unbind('click', TubePress.clickListener);
	});
}
