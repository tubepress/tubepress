<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 * 
 * This file is part of TubePress (http://tubepress.com)
 * 
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Option values for YouTube video sources.
 */
class tubepress_addons_youtube_api_const_options_values_GallerySourceValue
{
    /**
     * Standard feeds.
     *
     * https://developers.google.com/youtube/2.0/reference#Standard_feeds
     */
    const YOUTUBE_MOST_POPULAR   = 'youtubeMostPopular';

    /**
     * Related/responses.
     */
    const YOUTUBE_RELATED   = 'youtubeRelated';

    /**
     * Users/playlist/search/favorites etc.
     */
    const YOUTUBE_PLAYLIST  = 'playlist';
    const YOUTUBE_FAVORITES = 'favorites';
    const YOUTUBE_SEARCH    = 'tag';
    const YOUTUBE_USER      = 'user';
}
