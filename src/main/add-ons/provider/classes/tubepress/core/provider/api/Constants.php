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
interface tubepress_core_provider_api_Constants
{
    /**
     * This event is fired when a TubePress collects a new media item.
     *
     * @subject {@link tubepress_core_provider_api_MediaItem} The media item.
     *
     * @argument <var>provider</var> (`tubepress_core_provider_api_MediaProviderInterface`) The media provider that collected the item.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_NEW_MEDIA_ITEM = 'tubepress.core.provider.event.collect.mediaItem';

    /**
     * This event is fired when a TubePress collects a new tubepress_core_provider_api_Page.
     *
     * @subject {@tubepress_core_provider_api_Page} The page being built.
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
     * @subject {@tubepress_core_url_api_UrlInterface} The URL for the media item.
     *
     * @argument <var>itemId</var> (`string`) The item ID.
     */
    const EVENT_URL_MEDIA_ITEM = 'tubepress.core.provider.event.url.mediaItem';

    /**
     * This event is fired when TubePress constructs the URL for a single media item.
     *
     * @subject {@tubepress_core_url_api_UrlInterface} The URL for the media page.
     *
     * @argument <var>pageNumber</var> (`int`) The page number.
     */
    const EVENT_URL_MEDIA_PAGE = 'tubepress.core.provider.event.url.page';




    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_ORDER_BY = 'orderBy';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_PER_PAGE_SORT = 'perPageSort';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_RESULT_COUNT_CAP = 'resultCountCap';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_RESULTS_PER_PAGE = 'resultsPerPage';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_VIDEO_BLACKLIST = 'videoBlacklist';




    /**
     * @api
     * @since 3.1.2
     */
    const ORDER_BY_DEFAULT = 'default';



    /**
     * @api
     * @since 3.1.0
     */
    const PER_PAGE_SORT_NONE = 'none';

    /**
     * @api
     * @since 3.1.0
     */
    const PER_PAGE_SORT_RANDOM = 'random';



    const ATTRIBUTE_AUTHOR_DISPLAY_NAME      = 'authorDisplayName';
    const ATTRIBUTE_AUTHOR_USER_ID           = 'authorUid';
    const ATTRIBUTE_CATEGORY_DISPLAY_NAME    = 'category';
    const ATTRIBUTE_COMMENT_COUNT            = 'commentCount';
    const ATTRIBUTE_DESCRIPTION              = 'description';
    const ATTRIBUTE_DURATION_FORMATTED       = 'duration';
    const ATTRIBUTE_DURATION_SECONDS         = 'durationInSeconds';
    const ATTRIBUTE_HOME_URL                 = 'homeUrl';
    const ATTRIBUTE_ID                       = 'id';
    const ATTRIBUTE_KEYWORD_ARRAY            = 'keywords';
    const ATTRIBUTE_LIKES_COUNT              = 'likesCount';
    const ATTRIBUTE_PROVIDER_NAME            = 'providerName';
    const ATTRIBUTE_PROVIDER                 = 'provider';
    const ATTRIBUTE_RATING_AVERAGE           = 'ratingAverage';
    const ATTRIBUTE_RATING_COUNT             = 'ratingCount';
    const ATTRIBUTE_THUMBNAIL_URL            = 'thumbnailUrl';
    const ATTRIBUTE_TIME_UPDATED_FORMATTED   = 'timeLastUpdatedFormatted';
    const ATTRIBUTE_TIME_PUBLISHED_FORMATTED = 'timePublishedFormatted';
    const ATTRIBUTE_TIME_PUBLISHED_UNIXTIME  = 'timePublishedUnixTime';
    const ATTRIBUTE_TITLE                    = 'title';
    const ATTRIBUTE_VIEW_COUNT               = 'viewCount';
}