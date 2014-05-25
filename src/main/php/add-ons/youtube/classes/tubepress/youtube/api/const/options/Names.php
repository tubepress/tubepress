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
 *
 */
interface tubepress_youtube_api_const_options_Names
{
    const AUTOHIDE         = 'autoHide';
    const CLOSED_CAPTIONS  = 'youtubeClosedCaptions';
    const DISABLE_KEYBOARD = 'youtubeDisableKeyboardControls';
    const FULLSCREEN       = 'fullscreen';
    const MODEST_BRANDING  = 'modestBranding';
    const SHOW_ANNOTATIONS = 'youtubeShowAnnotations';
    const SHOW_CONTROLS    = 'youtubeShowPlayerControls';
    const SHOW_RELATED     = 'showRelated';
    const THEME            = 'youtubePlayerTheme';

    const DEV_KEY         = 'developerKey';
    const EMBEDDABLE_ONLY = 'embeddableOnly';
    const FILTER          = 'filter_racy';



    /**
     * Standard feeds.
     *
     * https://developers.google.com/youtube/2.0/reference#Standard_feeds
     */
    const YOUTUBE_MOST_POPULAR_VALUE   = 'youtubeMostPopularValue';

    /**
     * Related/responses.
     */
    const YOUTUBE_RELATED_VALUE   = 'youtubeRelatedValue';

    /**
     * Users/playlist/search/favorites etc.
     */
    const YOUTUBE_PLAYLIST_VALUE  = 'playlistValue';
    const YOUTUBE_FAVORITES_VALUE = 'favoritesValue';
    const YOUTUBE_TAG_VALUE       = 'tagValue';
    const YOUTUBE_USER_VALUE      = 'userValue';


    const RATING  = 'rating';
    const RATINGS = 'ratings';
}
