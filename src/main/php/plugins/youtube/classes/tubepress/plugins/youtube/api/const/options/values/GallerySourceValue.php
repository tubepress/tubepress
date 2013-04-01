<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 * 
 * This file is part of TubePress (http://tubepress.org)
 * 
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
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
