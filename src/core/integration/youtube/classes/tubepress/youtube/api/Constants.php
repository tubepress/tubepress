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
 * YouTube-specific event names.
 */
class tubepress_youtube_api_Constants
{
    const OPTION_AUTOHIDE         = 'autoHide';
    const OPTION_CLOSED_CAPTIONS  = 'youtubeClosedCaptions';
    const OPTION_DISABLE_KEYBOARD = 'youtubeDisableKeyboardControls';
    const OPTION_FULLSCREEN       = 'fullscreen';
    const OPTION_MODEST_BRANDING  = 'modestBranding';
    const OPTION_SHOW_ANNOTATIONS = 'youtubeShowAnnotations';
    const OPTION_SHOW_CONTROLS    = 'youtubeShowPlayerControls';
    const OPTION_SHOW_RELATED     = 'showRelated';
    const OPTION_THEME            = 'youtubePlayerTheme';

    const OPTION_DEV_KEY         = 'developerKey';
    const OPTION_EMBEDDABLE_ONLY = 'embeddableOnly';
    const OPTION_FILTER          = 'filter_racy';



    /**
     * Standard feeds.
     *
     * https://developers.google.com/youtube/2.0/reference#Standard_feeds
     */
    const OPTION_YOUTUBE_MOST_POPULAR_VALUE   = 'youtubeMostPopularValue';

    /**
     * Related/responses.
     */
    const OPTION_YOUTUBE_RELATED_VALUE   = 'youtubeRelatedValue';

    /**
     * Users/playlist/search/favorites etc.
     */
    const OPTION_YOUTUBE_PLAYLIST_VALUE  = 'playlistValue';
    const OPTION_YOUTUBE_FAVORITES_VALUE = 'favoritesValue';
    const OPTION_YOUTUBE_TAG_VALUE       = 'tagValue';
    const OPTION_YOUTUBE_USER_VALUE      = 'userValue';


    const OPTION_RATING  = 'rating';
    const OPTION_RATINGS = 'ratings';


    /**
     * Standard feeds.
     *
     * https://developers.google.com/youtube/2.0/reference#Standard_feeds
     */
    const GALLERYSOURCE_YOUTUBE_MOST_POPULAR   = 'youtubeMostPopular';

    /**
     * Related/responses.
     */
    const GALLERYSOURCE_YOUTUBE_RELATED   = 'youtubeRelated';

    /**
     * Users/playlist/search/favorites etc.
     */
    const GALLERYSOURCE_YOUTUBE_PLAYLIST  = 'playlist';
    const GALLERYSOURCE_YOUTUBE_FAVORITES = 'favorites';
    const GALLERYSOURCE_YOUTUBE_SEARCH    = 'tag';
    const GALLERYSOURCE_YOUTUBE_USER      = 'user';



    const AUTOHIDE_SHOW_BOTH              = 'fadeNone';
    const AUTOHIDE_HIDE_BOTH              = 'fadeBoth';
    const AUTOHIDE_HIDE_BAR_SHOW_CONTROLS = 'fadeOnlyProgressBar';

    const CONTROLS_HIDE                 = 'hide';
    const CONTROLS_SHOW_IMMEDIATE_FLASH = 'showImmediate';
    const CONTROLS_SHOW_DELAYED_FLASH   = 'showDelayed';

    const TIMEFRAME_TODAY      = 'today';
    const TIMEFRAME_ALL_TIME   = 'all_time';

    const PLAYER_THEME_DARK  = 'dark';
    const PLAYER_THEME_LIGHT = 'light';

    const SAFESEARCH_NONE     = 'none';
    const SAFESEARCH_MODERATE = 'moderate';
    const SAFESEARCH_STRICT   = 'strict';

    /**
     * @api
     * @since 4.0.0
     */
    const ORDER_BY_DURATION      = 'duration';

    /**
     * @api
     * @since 3.1.2
     */
    const ORDER_BY_DEFAULT = 'default';

    /**
     * @api
     * @since 4.0.0
     */
    const ORDER_BY_POSITION      = 'position';

    /**
     * @api
     * @since 4.0.0
     */
    const ORDER_BY_NEWEST        = 'newest';

    /**
     * @api
     * @since 4.0.0
     */
    const ORDER_BY_REV_POSITION  = 'reversedPosition';

    /**
     * @api
     * @since 4.0.0
     */
    const ORDER_BY_TITLE         = 'title';

    /**
     * @api
     * @since 4.0.0
     */
    const ORDER_BY_COMMENT_COUNT = 'commentCount';

    /**
     * @api
     * @since 4.0.0
     */
    const ORDER_BY_RATING        = 'rating';

    /**
     * @api
     * @since 4.0.0
     */
    const ORDER_BY_RELEVANCE     = 'relevance';

    /**
     * @api
     * @since 4.0.0
     */
    const ORDER_BY_VIEW_COUNT    = 'viewCount';

    /**
     * @api
     * @since 4.0.0
     */
    const PER_PAGE_SORT_COMMENT_COUNT = 'commentCount';

    /**
     * @api
     * @since 4.0.0
     */
    const PER_PAGE_SORT_DURATION       = 'duration';

    /**
     * @api
     * @since 4.0.0
     */
    const PER_PAGE_SORT_NEWEST         = 'newest';

    /**
     * @api
     * @since 4.0.0
     */
    const PER_PAGE_SORT_OLDEST         = 'oldest';

    /**
     * @api
     * @since 4.0.0
     */
    const PER_PAGE_SORT_RATING         = 'rating';

    /**
     * @api
     * @since 4.0.0
     */
    const PER_PAGE_SORT_TITLE          = 'title';

    /**
     * @api
     * @since 4.0.0
     */
    const PER_PAGE_SORT_VIEW_COUNT     = 'viewCount';
}
