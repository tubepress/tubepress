/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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

test('TubePressEvents', function () {

	equal(TubePressEvents.NEW_THUMBS_LOADED  , 'tubepressNewThumbnailsLoaded');
	equal(TubePressEvents.NEW_GALLERY_LOADED , 'tubepressNewGalleryLoaded');
	equal(TubePressEvents.THUMBNAIL_CLICKED  , 'tubepressThumbnailClicked');
	equal(TubePressEvents.PLAYER_INVOKE      , 'tubepressPlayerInvoke');
	equal(TubePressEvents.PLAYER_POPULATE    , 'tubepressPlayerPopulate');

});
