<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
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
 * Option values for YouTube video sources.
 */
class tubepress_plugins_youtube_api_const_options_values_GallerySourceValue
{
    /**
     * Standard feeds.
     *
     * https://developers.google.com/youtube/2.0/reference#Standard_feeds
     */
    const YOUTUBE_TOP_RATED      = 'top_rated';
    const YOUTUBE_TOP_FAVORITES  = 'youtubeTopFavorites';
    const YOUTUBE_MOST_SHARED    = 'youtubeMostShared';
    const YOUTUBE_MOST_POPULAR   = 'youtubeMostPopular';
    const YOUTUBE_MOST_RECENT    = 'most_recent';
    const YOUTUBE_MOST_DISCUSSED = 'most_discussed';
    const YOUTUBE_MOST_RESPONDED = 'most_responded';
    const YOUTUBE_FEATURED       = 'recently_featured';
    const YOUTUBE_TRENDING       = 'youtubeTrending';

    /**
     * Related/responses.
     */
    const YOUTUBE_RELATED   = 'youtubeRelated';
    const YOUTUBE_RESPONSES = 'youtubeResponses';

    /**
     * Users/playlist/search/favorites etc.
     */
    const YOUTUBE_PLAYLIST  = 'playlist';
    const YOUTUBE_FAVORITES = 'favorites';
    const YOUTUBE_SEARCH    = 'tag';
    const YOUTUBE_USER      = 'user';
}
