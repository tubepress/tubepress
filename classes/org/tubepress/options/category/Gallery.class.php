<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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

/**
 * Option names defining which videos to show in the gallery
 *
 */
class org_tubepress_options_category_Gallery
{
    const MODE = "mode";
    
    const TEMPLATE = "template";

    const VIDEO = "video";
    
    const FAVORITES_VALUE        = "favoritesValue";
    const MOST_VIEWED_VALUE      = "most_viewedValue";
    const PLAYLIST_VALUE         = "playlistValue";
    const TAG_VALUE              = "tagValue";
    const TOP_RATED_VALUE        = "top_ratedValue";
    const USER_VALUE             = "userValue";
    const VIMEO_UPLOADEDBY_VALUE = 'vimeoUploadedByValue';
    const VIMEO_LIKES_VALUE      = 'vimeoLikesValue';
    const VIMEO_APPEARS_IN_VALUE = 'vimeoAppearsInValue';
    const VIMEO_SEARCH_VALUE     = 'vimeoSearchValue';
    const VIMEO_CREDITED_VALUE   = 'vimeoCreditedToValue';
    const VIMEO_CHANNEL_VALUE    = 'vimeoChannelValue';
    const VIMEO_ALBUM_VALUE      = 'vimeoAlbumValue';
    const VIMEO_GROUP_VALUE      = 'vimeoGroupValue';
}
