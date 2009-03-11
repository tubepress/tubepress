/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org)
 * 
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
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
	console.info("Loading");
	var base = baseUrl + '/ui/players/lightwindow/lib/';
	jQuery.include([base + 'javascript/lightWindow.js', base + 'css/lightWindow.css'], function() {
		console.info("Loaded");
	});
}

function tubepress_lightwindow_player(galleryId, videoId) {
	window.tubepressLightWindow.activateWindow({
		href: 'http://stickmanlabs.com/images/kevin_vegas.jpg', 
		title: 'Waiting for the show to start in Las Vegas', 
		author: 'Jazzmatt', 
		caption: 'Mmmmmm Margaritas! And yes, this is me...', 
		left: 300
	});
}