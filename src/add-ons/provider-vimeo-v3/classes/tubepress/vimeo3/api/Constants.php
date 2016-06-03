<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
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
interface tubepress_vimeo3_api_Constants
{
    const OPTION_PLAYER_COLOR = 'playerColor';
    const OPTION_LIKES        = 'likes';

    /**
     * https://developer.vimeo.com/api/endpoints/categories#/{category}/videos.
     *
     * @api
     *
     * @since 4.2.0
     */
    const GALLERYSOURCE_VIMEO_CATEGORY = 'vimeoCategory';
    const OPTION_VIMEO_CATEGORY_VALUE  = 'vimeoCategoryValue';

    /**
     * https://developer.vimeo.com/api/endpoints/channels#/{channel_id}/videos.
     *
     * @api
     *
     * @since 4.2.0
     */
    const GALLERYSOURCE_VIMEO_CHANNEL = 'vimeoChannel';
    const OPTION_VIMEO_CHANNEL_VALUE  = 'vimeoChannelValue';

    /**
     * https://developer.vimeo.com/api/endpoints/groups#/{group_id}/videos.
     *
     * @api
     *
     * @since 4.2.0
     */
    const GALLERYSOURCE_VIMEO_GROUP = 'vimeoGroup';
    const OPTION_VIMEO_GROUP_VALUE  = 'vimeoGroupValue';

    /**
     * https://developer.vimeo.com/api/endpoints/videos.
     *
     * @api
     *
     * @since 4.2.0
     */
    const GALLERYSOURCE_VIMEO_SEARCH = 'vimeoSearch';
    const OPTION_VIMEO_SEARCH_VALUE  = 'vimeoSearchValue';

    /**
     * https://developer.vimeo.com/api/endpoints/tags#/{word}/videos.
     *
     * @api
     *
     * @since 4.2.0
     */
    const GALLERYSOURCE_VIMEO_TAG = 'vimeoTag';
    const OPTION_VIMEO_TAG_VALUE  = 'vimeoTagValue';

    /**
     * https://developer.vimeo.com/api/endpoints/users#/{user_id}/appearances.
     *
     * @api
     *
     * @since 4.2.0
     */
    const GALLERYSOURCE_VIMEO_APPEARS_IN = 'vimeoAppearsIn';
    const OPTION_VIMEO_APPEARS_IN_VALUE  = 'vimeoAppearsInValue';

    /**
     * https://developer.vimeo.com/api/endpoints/users#/{user_id}/likes.
     *
     * @api
     *
     * @since 4.2.0
     */
    const GALLERYSOURCE_VIMEO_LIKES = 'vimeoLikes';
    const OPTION_VIMEO_LIKES_VALUE  = 'vimeoLikesValue';

    /**
     * https://developer.vimeo.com/api/endpoints/users#/{user_id}/videos.
     *
     * @api
     *
     * @since 4.2.0
     */
    const GALLERYSOURCE_VIMEO_UPLOADEDBY = 'vimeoUploadedBy';
    const OPTION_VIMEO_UPLOADEDBY_VALUE  = 'vimeoUploadedByValue';

    /**
     * https://developer.vimeo.com/api/endpoints/users#/{user_id}/albums.
     *
     * @api
     *
     * @since 4.2.0
     */
    const GALLERYSOURCE_VIMEO_ALBUM = 'vimeoAlbum';
    const OPTION_VIMEO_ALBUM_VALUE  = 'vimeoAlbumValue';

    /**
     * @api
     *
     * @since 4.0.0
     */
    const ORDER_BY_DEFAULT = 'default';

    /**
     * @api
     *
     * @since 4.0.0
     */
    const ORDER_BY_NEWEST = 'newest';

    /**
     * @api
     *
     * @since 4.0.0
     */
    const ORDER_BY_OLDEST = 'oldest';

    /**
     * @api
     *
     * @since 4.0.0
     */
    const ORDER_BY_RELEVANCE = 'relevance';

    /**
     * @api
     *
     * @since 4.2.0
     */
    const ORDER_BY_ALPHABETICAL_A_Z = 'alphabetical';

    /**
     * @api
     *
     * @since 4.2.0
     */
    const ORDER_BY_ALPHABETICAL_Z_A = 'reverseAlphabetical';

    /**
     * @api
     *
     * @since 4.0.0
     */
    const ORDER_BY_VIEW_COUNT = 'viewCount';

    /**
     * @api
     *
     * @since 4.2.0
     */
    const ORDER_BY_LIKES = 'likes';

    /**
     * @api
     *
     * @since 4.2.0
     */
    const ORDER_BY_SHORTEST = 'shortest';

    /**
     * @api
     *
     * @since 4.2.0
     */
    const ORDER_BY_LONGEST = 'longest';
}
