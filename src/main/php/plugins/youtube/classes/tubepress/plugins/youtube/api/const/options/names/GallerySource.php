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
 * Option names related to YouTube video sources.
 */
class tubepress_plugins_youtube_api_const_options_names_GallerySource
{
    /**
     * Standard feeds.
     *
     * https://developers.google.com/youtube/2.0/reference#Standard_feeds
     */
    const YOUTUBE_TOP_RATED_VALUE      = 'top_ratedValue';
    const YOUTUBE_TOP_FAVORITES_VALUE  = 'youtubeTopFavoritesValue';
    const YOUTUBE_MOST_SHARED_VALUE    = 'youtubeMostSharedValue';
    const YOUTUBE_MOST_POPULAR_VALUE   = 'youtubeMostPopularValue';
    const YOUTUBE_MOST_RECENT_VALUE    = 'most_recentValue';
    const YOUTUBE_MOST_DISCUSSED_VALUE = 'most_discussedValue';
    const YOUTUBE_MOST_RESPONDED_VALUE = 'most_respondedValue';
    const YOUTUBE_FEATURED_VALUE       = 'recently_featuredValue';
    const YOUTUBE_TRENDING_VALUE       = 'youtubeTrendingValue';

    /**
     * Related/responses.
     */
    const YOUTUBE_RELATED_VALUE   = 'youtubeRelatedValue';
    const YOUTUBE_RESPONSES_VALUE = 'youtubeResponsesValue';

    /**
     * Users/playlist/search/favorites etc.
     */
    const YOUTUBE_PLAYLIST_VALUE  = 'playlistValue';
    const YOUTUBE_FAVORITES_VALUE = 'favoritesValue';
    const YOUTUBE_TAG_VALUE       = 'tagValue';
    const YOUTUBE_USER_VALUE      = 'userValue';
}
