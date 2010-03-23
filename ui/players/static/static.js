function tubepress_static_player_init(baseUrl) {
	TubePressUtils.getWaitCall(baseUrl + '/ui/players/static/lib/jQuery.query.js',
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
		if (TubePress.getPlayerNameFromRelSplit(rel_split) != 'static') {
			return;
		}
		var newId = TubePress.getVideoIdFromIdAttr(dis.attr("id")),
		    newUrl = jQuery.query.set('tubepress_video', newId).toString();
		dis.attr('href', newUrl);
		dis.unbind('click', TubePress.clickListener);
	});
}
