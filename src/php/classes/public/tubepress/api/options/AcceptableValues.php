<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License; v. 2.0. If a copy of the MPL was not distributed with this
 * file; You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @api
 * @since 4.0.0
 */
interface tubepress_api_options_AcceptableValues
{
    /**
     * @api
     * @since 4.0.0
     */
    const EMBEDDED_IMPL_PROVIDER_BASED = 'provider_based';

    /**
     * @api
     * @since 3.1.2
     */
    const ORDER_BY_DEFAULT = 'default';

    /**
     * @api
     * @since 4.0.0
     */
    const OUTPUT_AJAX_SEARCH_INPUT = 'ajaxSearchInput';

    /**
     * @api
     * @since 4.0.0
     */
    const OUTPUT_PLAYER = 'player';

    /**
     * @api
     * @since 4.0.0
     */
    const OUTPUT_SEARCH_INPUT = 'searchInput';

    /**
     * @api
     * @since 4.0.0
     */
    const OUTPUT_SEARCH_RESULTS = 'searchResults';

    /**
     * @api
     * @since 4.0.0
     */
    const PER_PAGE_SORT_NONE = 'none';

    /**
     * @api
     * @since 4.0.0
     */
    const PER_PAGE_SORT_RANDOM = 'random';

    /**
     * @api
     * @since 4.0.0
     */
    const PER_PAGE_SORT_COMMENT_COUNT = 'commentCount';

    /**
     * @api
     * @since 4.0.0
     */
    const PER_PAGE_SORT_DURATION = 'duration';

    /**
     * @api
     * @since 4.0.0
     */
    const PER_PAGE_SORT_NEWEST = 'newest';

    /**
     * @api
     * @since 4.0.0
     */
    const PER_PAGE_SORT_OLDEST = 'oldest';

    /**
     * @api
     * @since 4.0.0
     */
    const PER_PAGE_SORT_RATING = 'rating';

    /**
     * @api
     * @since 4.0.0
     */
    const PER_PAGE_SORT_TITLE = 'title';

    /**
     * @api
     * @since 4.0.0
     */
    const PER_PAGE_SORT_VIEW_COUNT = 'viewCount';

    /**
     * @api
     * @since 4.0.0
     */
    const PLAYER_LOC_DETACHED = 'detached';

    /**
     * @api
     * @since 4.0.0
     */
    const PLAYER_LOC_FANCYBOX = 'fancybox';

    /**
     * @api
     * @since 4.1.11
     */
    const PLAYER_LOC_FANCYBOX2 = 'fancybox2';

    /**
     * @api
     * @since 4.0.0
     */
    const PLAYER_LOC_JQMODAL = 'jqmodal';

    /**
     * @api
     * @since 4.0.0
     */
    const PLAYER_LOC_NORMAL = 'normal';

    /**
     * @api
     * @since 4.0.0
     */
    const PLAYER_LOC_POPUP = 'popup';

    /**
     * @api
     * @since 4.0.0
     */
    const PLAYER_LOC_SHADOWBOX = 'shadowbox';

    /**
     * @api
     * @since 4.0.0
     */
    const PLAYER_LOC_SOLO = 'solo';

    /**
     * @api
     * @since 4.0.0
     */
    const PLAYER_LOC_STATIC = 'static';

    /**
     * @api
     * @since 4.0.0
     */
    const PLAYER_LOC_TINYBOX = 'tinybox';
}