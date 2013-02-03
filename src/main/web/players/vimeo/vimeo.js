/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
var TubePressVimeoPlayer = (function () {
	
	/* this stuff helps compression */
	var events	= TubePressEvents,
		name	= 'vimeo',
		
		invoke = function (e, videoId, galleryId, width, height) {

			window.location = 'http://www.vimeo.com/' + videoId;
		};

	jQuery(document).bind(events.PLAYER_INVOKE + name, invoke);
}());