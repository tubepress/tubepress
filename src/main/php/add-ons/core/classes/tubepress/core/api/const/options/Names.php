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
 * Advanced option names.
 *
 * @package TubePress\Const\Options
 */
interface tubepress_core_api_const_options_Names
{

    //ADVANCED

    /**
     * @api
     * @since 3.1.0
     */
    const DEBUG_ON    = 'debugging_enabled';

    /**
     * @api
     * @since 3.1.0
     */
    const GALLERY_ID  = 'galleryId';

    /**
     * @api
     * @since 3.1.0
     */
    const HTTP_METHOD = 'httpMethod';

    /**
     * @api
     * @since 3.1.0
     */
    const HTTPS       = 'https';

    /**
     * @api
     * @since 3.1.0
     */
    const KEYWORD     = 'keyword';




    //CACHE


    /**
     * @api
     * @since 3.1.0
     */
    const CACHE_CLEAN_FACTOR     = 'cacheCleaningFactor';

    /**
     * @api
     * @since 3.1.0
     */
    const CACHE_DIR              = 'cacheDirectory';

    /**
     * @api
     * @since 3.1.0
     */
    const CACHE_ENABLED          = 'cacheEnabled';

    /**
     * @api
     * @since 3.1.0
     */
    const CACHE_LIFETIME_SECONDS = 'cacheLifetimeSeconds';




    //EMBEDDED

    /**
     * @api
     * @since 3.1.0
     */
    const AUTONEXT         = 'autoNext';

    /**
     * @api
     * @since 3.1.0
     */
    const AUTOPLAY         = 'autoplay';

    /**
     * @api
     * @since 3.1.0
     */
    const EMBEDDED_HEIGHT  = 'embeddedHeight';

    /**
     * @api
     * @since 3.1.0
     */
    const EMBEDDED_WIDTH   = 'embeddedWidth';

    /**
     * @api
     * @since 3.1.0
     */
    const ENABLE_JS_API    = 'enableJsApi';

    /**
     * @api
     * @since 3.1.0
     */
    const LAZYPLAY         = 'lazyPlay';

    /**
     * @api
     * @since 3.1.0
     */
    const LOOP             = 'loop';

    /**
     * @api
     * @since 3.1.0
     */
    const PLAYER_IMPL      = 'playerImplementation';

    /**
     * @api
     * @since 3.1.0
     */
    const PLAYER_LOCATION  = 'playerLocation';

    /**
     * @api
     * @since 3.1.0
     */
    const SEQUENCE         = 'sequence';

    /**
     * @api
     * @since 3.1.0
     */
    const SHOW_INFO        = 'showInfo';




    //FEED

    /**
     * @api
     * @since 3.1.0
     */
    const ORDER_BY         = 'orderBy';

    /**
     * @api
     * @since 3.1.0
     */
    const PER_PAGE_SORT    = 'perPageSort';

    /**
     * @api
     * @since 3.1.0
     */
    const RESULT_COUNT_CAP = 'resultCountCap';

    /**
     * @api
     * @since 3.1.0
     */
    const SEARCH_ONLY_USER = 'searchResultsRestrictedToUser';

    /**
     * @api
     * @since 3.1.0
     */
    const VIDEO_BLACKLIST  = 'videoBlacklist';




    //SEARCH

    /**
     * @api
     * @since 3.1.0
     */
    const SEARCH_PROVIDER       = 'searchProvider';

    /**
     * @api
     * @since 3.1.0
     */
    const SEARCH_RESULTS_ONLY   = 'searchResultsOnly';

    /**
     * @api
     * @since 3.1.0
     */
    const SEARCH_RESULTS_URL    = 'searchResultsUrl';



    //META

    /**
     * @api
     * @since 3.1.0
     */
    const AUTHOR      = 'author';

    /**
     * @api
     * @since 3.1.0
     */
    const CATEGORY    = 'category';

    /**
     * @api
     * @since 3.1.0
     */
    const DESCRIPTION = 'description';

    /**
     * @api
     * @since 3.1.0
     */
    const ID          = 'id';

    /**
     * @api
     * @since 3.1.0
     */
    const KEYWORDS    = 'tags';

    /**
     * @api
     * @since 3.1.0
     */
    const LENGTH      = 'length';

    /**
     * @api
     * @since 3.1.0
     */
    const TITLE       = 'title';

    /**
     * @api
     * @since 3.1.0
     */
    const UPLOADED    = 'uploaded';

    /**
     * @api
     * @since 3.1.0
     */
    const URL         = 'url';

    /**
     * @api
     * @since 3.1.0
     */
    const VIEWS       = 'views';

    /**
     * @api
     * @since 3.1.0
     */
    const DATEFORMAT     = 'dateFormat';

    /**
     * @api
     * @since 3.1.0
     */
    const DESC_LIMIT     = 'descriptionLimit';

    /**
     * @api
     * @since 3.1.0
     */
    const RELATIVE_DATES = 'relativeDates';

    //DEPRECATED:
    /**
     * @deprecated
     * @api
     * @since 3.1.0
     */
    const TAGS    = 'tags';

    /**
     * @deprecated
     * @api
     * @since 3.1.0
     */
    const LIKES   = 'likes'; //this has been moved to vimeo

    /**
     * @deprecated
     * @api
     * @since 3.1.0
     */
    const RATING  = 'rating';//this has been moved to YouTube

    /**
     * @deprecated
     * @api
     * @since 3.1.0
     */
    const RATINGS = 'ratings';//this has been moved to YouTube




    //OPTIONS UI

    /**
     * @api
     * @since 3.1.0
     */
    const DISABLED_OPTIONS_PAGE_PARTICIPANTS = 'disabledOptionsPageParticipants';





    //OUTPUT

    /**
     * @api
     * @since 3.1.0
     */
    const GALLERY_SOURCE = 'mode';

    /**
     * @api
     * @since 3.1.0
     */
    const OUTPUT         = 'output';

    /**
     * @api
     * @since 3.1.0
     */
    const VIDEO          = 'video';




    //THUMBS

    /**
     * @api
     * @since 3.1.0
     */
    const AJAX_PAGINATION  = 'ajaxPagination';

    /**
     * @api
     * @since 3.1.0
     */
    const FLUID_THUMBS     = 'fluidThumbs';

    /**
     * @api
     * @since 3.1.0
     */
    const HQ_THUMBS        = 'hqThumbs';

    /**
     * @api
     * @since 3.1.0
     */
    const PAGINATE_ABOVE   = 'paginationAbove';

    /**
     * @api
     * @since 3.1.0
     */
    const PAGINATE_BELOW   = 'paginationBelow';

    /**
     * @api
     * @since 3.1.0
     */
    const RANDOM_THUMBS    = 'randomize_thumbnails';

    /**
     * @api
     * @since 3.1.0
     */
    const RESULTS_PER_PAGE = 'resultsPerPage';

    /**
     * @api
     * @since 3.1.0
     */
    const THEME            = 'theme';

    /**
     * @api
     * @since 3.1.0
     */
    const THUMB_HEIGHT     = 'thumbHeight';

    /**
     * @api
     * @since 3.1.0
     */
    const THUMB_WIDTH      = 'thumbWidth';
}
