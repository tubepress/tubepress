<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @api
 * @since 4.0.0
 */
interface tubepress_app_api_options_Names
{
    /**
     * @api
     * @since 4.0.0
     */
    const CACHE_CLEANING_FACTOR = 'cacheCleaningFactor';

    /**
     * @api
     * @since 4.0.0
     */
    const CACHE_DIRECTORY = 'cacheDirectory';

    /**
     * @api
     * @since 4.0.0
     */
    const CACHE_ENABLED = 'cacheEnabled';

    /**
     * @api
     * @since 4.0.0
     */
    const CACHE_LIFETIME_SECONDS = 'cacheLifetimeSeconds';

    /**
     * @api
     * @since 4.1.10
     */
    const CACHE_HTML_CLEANING_FACTOR = 'htmlCacheCleaningFactor';

    /**
     * @api
     * @since 4.1.10
     */
    const CACHE_HTML_CLEANING_KEY = 'htmlCacheCleaningKey';

    /**
     * @api
     * @since 4.1.10
     */
    const CACHE_HTML_DIRECTORY = 'htmlCacheDirectory';

    /**
     * @api
     * @since 4.1.10
     */
    const CACHE_HTML_ENABLED = 'htmlCacheEnabled';

    /**
     * @api
     * @since 4.1.10
     */
    const CACHE_HTML_LIFETIME_SECONDS = 'htmlCacheLifetimeSeconds';

    /**
     * @api
     * @since 4.0.0
     */
    const DEBUG_ON = 'debugging_enabled';

    /**
     * @api
     * @since 4.0.0
     */
    const EMBEDDED_AUTOPLAY = 'autoplay';

    /**
     * @api
     * @since 4.0.0
     */
    const EMBEDDED_HEIGHT = 'embeddedHeight';

    /**
     * @api
     * @since 4.0.0
     */
    const EMBEDDED_WIDTH = 'embeddedWidth';

    /**
     * @api
     * @since 4.0.0
     */
    const EMBEDDED_LAZYPLAY = 'lazyPlay';

    /**
     * @api
     * @since 4.0.0
     */
    const EMBEDDED_LOOP = 'loop';

    /**
     * @api
     * @since 4.0.0
     */
    const EMBEDDED_PLAYER_IMPL = 'playerImplementation';

    /**
     * @api
     * @since 4.0.9
     */
    const EMBEDDED_SCROLL_DURATION = 'embeddedScrollDuration';

    /**
     * @api
     * @since 4.0.9
     */
    const EMBEDDED_SCROLL_OFFSET = 'embeddedScrollOffset';

    /**
     * @api
     * @since 4.0.9
     */
    const EMBEDDED_SCROLL_ON = 'embeddedScrollOn';

    /**
     * @api
     * @since 4.0.0
     */
    const EMBEDDED_SHOW_INFO = 'showInfo';

    /**
     * @api
     * @since 4.0.0
     */
    const FEED_ORDER_BY = 'orderBy';

    /**
     * @api
     * @since 4.0.0
     */
    const FEED_PER_PAGE_SORT = 'perPageSort';

    /**
     * @api
     * @since 4.0.0
     */
    const FEED_RESULT_COUNT_CAP = 'resultCountCap';

    /**
     * @api
     * @since 4.0.0
     */
    const FEED_RESULTS_PER_PAGE = 'resultsPerPage';

    /**
     * @api
     * @since 4.1.9
     */
    const FEED_ADJUSTED_RESULTS_PER_PAGE = 'adjustedResultsPerPage';

    /**
     * @api
     * @since 4.0.0
     */
    const FEED_ITEM_ID_BLACKLIST = 'videoBlacklist';

    /**
     * @api
     * @since 4.0.0
     */
    const GALLERY_AUTONEXT = 'autoNext';

    /**
     * @api
     * @since 4.0.0
     */
    const GALLERY_SOURCE = 'mode';

    /**
     * @api
     * @since 4.0.0
     */
    const GALLERY_AJAX_PAGINATION = 'ajaxPagination';

    /**
     * @api
     * @since 4.0.0
     */
    const GALLERY_FLUID_THUMBS = 'fluidThumbs';

    /**
     * @api
     * @since 4.0.0
     */
    const GALLERY_HQ_THUMBS = 'hqThumbs';

    /**
     * @api
     * @since 4.0.0
     */
    const GALLERY_PAGINATE_ABOVE = 'paginationAbove';

    /**
     * @api
     * @since 4.0.0
     */
    const GALLERY_PAGINATE_BELOW = 'paginationBelow';

    /**
     * @api
     * @since 4.0.0
     */
    const GALLERY_RANDOM_THUMBS = 'randomize_thumbnails';

    /**
     * @api
     * @since 4.0.0
     */
    const GALLERY_THUMB_HEIGHT = 'thumbHeight';

    /**
     * @api
     * @since 4.0.0
     */
    const GALLERY_THUMB_WIDTH = 'thumbWidth';

    /**
     * @api
     * @since 4.0.0
     */
    const HTML_OUTPUT = 'output';

    /**
     * @api
     * @since 4.0.0
     */
    const HTTP_METHOD = 'httpMethod';

    /**
     * @api
     * @since 4.0.0
     */
    const HTML_HTTPS = 'https';

    /**
     * @api
     * @since 4.0.0
     */
    const HTML_GALLERY_ID = 'galleryId';

    /**
     * @api
     * @since 4.0.0
     */
    const META_DISPLAY_AUTHOR = 'author';

    /**
     * @api
     * @since 4.0.0
     */
    const META_DISPLAY_CATEGORY = 'category';

    /**
     * @api
     * @since 4.0.0
     */
    const META_DISPLAY_DESCRIPTION = 'description';

    /**
     * @api
     * @since 4.0.0
     */
    const META_DISPLAY_ID = 'id';

    /**
     * @api
     * @since 4.0.0
     */
    const META_DISPLAY_KEYWORDS = 'tags';

    /**
     * @api
     * @since 4.0.0
     */
    const META_DISPLAY_LENGTH = 'length';

    /**
     * @api
     * @since 4.0.0
     */
    const META_DISPLAY_TITLE = 'title';

    /**
     * @api
     * @since 4.0.0
     */
    const META_DISPLAY_UPLOADED = 'uploaded';

    /**
     * @api
     * @since 4.0.0
     */
    const META_DISPLAY_URL = 'url';

    /**
     * @api
     * @since 4.0.0
     */
    const META_DISPLAY_VIEWS = 'views';

    /**
     * @api
     * @since 4.0.0
     */
    const META_DATEFORMAT = 'dateFormat';

    /**
     * @api
     * @since 4.0.0
     */
    const META_DESC_LIMIT = 'descriptionLimit';

    /**
     * @api
     * @since 4.0.0
     */
    const META_RELATIVE_DATES = 'relativeDates';

    /**
     * @api
     * @since 4.0.0
     */
    const OPTIONS_UI_DISABLED_FIELD_PROVIDERS = 'disabledFieldProviderNames';

    /**
     * @api
     * @since 4.0.0
     */
    const PLAYER_LOCATION  = 'playerLocation';

    /**
     * @api
     * @since 4.0.5
     */
    const RESPONSIVE_EMBEDS = 'responsiveEmbeds';

    /**
     * @api
     * @since 4.0.0
     */
    const SEARCH_ONLY_USER = 'searchResultsRestrictedToUser';

    /**
     * @api
     * @since 4.0.0
     */
    const SEARCH_PROVIDER = 'searchProvider';

    /**
     * @api
     * @since 4.0.0
     */
    const SEARCH_RESULTS_ONLY = 'searchResultsOnly';

    /**
     * @api
     * @since 4.0.0
     */
    const SEARCH_RESULTS_URL = 'searchResultsUrl';

    /**
     * @api
     * @since 4.0.0
     */
    const SHORTCODE_KEYWORD = 'keyword';

    /**
     * @api
     * @since 4.0.0
     */
    const SINGLE_MEDIA_ITEM_ID = 'video';

    /**
     * @api
     * @since 4.1.11
     */
    const SOURCES = 'sources';

    /**
     * @api
     * @since 4.0.0
     */
    const TEMPLATE_CACHE_AUTORELOAD = 'templateCacheAutoreload';

    /**
     * @api
     * @since 4.0.0
     */
    const TEMPLATE_CACHE_ENABLED = 'templateCacheEnabled';

    /**
     * @api
     * @since 4.0.0
     */
    const TEMPLATE_CACHE_DIR = 'templateCacheDirectory';

    /**
     * @api
     * @since 4.0.0
     */
    const THEME = 'theme';

    /**
     * @api
     * @since 4.0.0
     */
    const THEME_ADMIN = 'adminTheme';
}