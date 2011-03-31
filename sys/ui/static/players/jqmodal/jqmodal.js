/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org) and is released 
 * under the General Public License (GPL) version 3
 *
 * Shrink your JS: http://developer.yahoo.com/yui/compressor/
 */
function tubepress_jqmodal_player(title, html, height, width, videoId) {
	var element = jQuery('<div style="visibility: none; height: ' + height + 'px; width: ' + width + 'px;">' + html + '</div>').appendTo('body');

	element.addClass('jqmWindow');	 
	element.jqm(); 
	element.jqmShow();
}

function tubepress_jqmodal_player_init(baseUrl) {
	var path = baseUrl + '/sys/ui/static/players/jqmodal/lib/jqModal.';
	jQuery.getScript(path + 'js', function () {}, true);
	TubePressJS.loadCss(path + 'css');
}
