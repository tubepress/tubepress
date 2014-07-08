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
 * @api
 * @since 4.0.0
 */
interface tubepress_app_media_provider_api_Constants
{
    /**
     * This event is fired when a TubePress collects a new media item.
     *
     * @subject {@link tubepress_app_media_item_api_MediaItem} The media item.
     *
     * @argument <var>provider</var> (`tubepress_app_media_provider_api_MediaProviderInterface`) The media provider that collected the item.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_NEW_MEDIA_ITEM = 'tubepress.core.provider.event.collect.mediaItem';

    /**
     * This event is fired when a TubePress collects a new tubepress_app_media_provider_api_Page.
     *
     * @subject {@tubepress_app_media_provider_api_Page} The page being built.
     *
     * @argument <var>pageNumber</var> (`int`) The page number of this page.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_NEW_MEDIA_PAGE = 'tubepress.core.provider.event.construct.page';

    /**
     * This event is fired when TubePress constructs the URL for a single media item.
     *
     * @subject {@tubepress_lib_url_api_UrlInterface} The URL for the media item.
     *
     * @argument <var>itemId</var> (`string`) The item ID.
     */
    const EVENT_URL_MEDIA_ITEM = 'tubepress.core.provider.event.url.mediaItem';

    /**
     * This event is fired when TubePress constructs the URL for a single media item.
     *
     * @subject {@tubepress_lib_url_api_UrlInterface} The URL for the media page.
     *
     * @argument <var>pageNumber</var> (`int`) The page number.
     */
    const EVENT_URL_MEDIA_PAGE = 'tubepress.core.provider.event.url.page';

    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_GALLERY_SOURCE = 'mode';

    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_ORDER_BY = 'orderBy';

    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_PER_PAGE_SORT = 'perPageSort';

    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_RESULT_COUNT_CAP = 'resultCountCap';

    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_RESULTS_PER_PAGE = 'resultsPerPage';

    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_ITEM_ID_BLACKLIST = 'videoBlacklist';

    /**
     * @api
     * @since 3.1.2
     */
    const ORDER_BY_DEFAULT = 'default';

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

    const OPTIONS_UI_CATEGORY_GALLERY_SOURCE = 'gallery-source-category';
    const OPTIONS_UI_CATEGORY_FEED           = 'feed-category';
}