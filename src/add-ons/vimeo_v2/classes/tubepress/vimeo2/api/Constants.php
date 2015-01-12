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
 *
 */
interface tubepress_vimeo2_api_Constants
{
    const OPTION_PLAYER_COLOR = 'playerColor';
    const OPTION_VIMEO_KEY    = 'vimeoKey';
    const OPTION_VIMEO_SECRET = 'vimeoSecret';
    const OPTION_LIKES        = 'likes';

    const OPTION_VIMEO_ALBUM_VALUE      = 'vimeoAlbumValue';
    const OPTION_VIMEO_APPEARS_IN_VALUE = 'vimeoAppearsInValue';
    const OPTION_VIMEO_CHANNEL_VALUE    = 'vimeoChannelValue';
    const OPTION_VIMEO_CREDITED_VALUE   = 'vimeoCreditedToValue';
    const OPTION_VIMEO_GROUP_VALUE      = 'vimeoGroupValue';
    const OPTION_VIMEO_LIKES_VALUE      = 'vimeoLikesValue';
    const OPTION_VIMEO_SEARCH_VALUE     = 'vimeoSearchValue';
    const OPTION_VIMEO_UPLOADEDBY_VALUE = 'vimeoUploadedByValue';

    const GALLERYSOURCE_VIMEO_UPLOADEDBY = 'vimeoUploadedBy';
    const GALLERYSOURCE_VIMEO_LIKES      = 'vimeoLikes';
    const GALLERYSOURCE_VIMEO_APPEARS_IN = 'vimeoAppearsIn';
    const GALLERYSOURCE_VIMEO_SEARCH     = 'vimeoSearch';
    const GALLERYSOURCE_VIMEO_CREDITED   = 'vimeoCreditedTo';
    const GALLERYSOURCE_VIMEO_CHANNEL    = 'vimeoChannel';
    const GALLERYSOURCE_VIMEO_ALBUM      = 'vimeoAlbum';
    const GALLERYSOURCE_VIMEO_GROUP      = 'vimeoGroup';


    /**
     * @api
     * @since 4.0.0
     */
    const ORDER_BY_COMMENT_COUNT = 'commentCount';

    /**
     * @api
     * @since 4.0.0
     */
    const ORDER_BY_NEWEST = 'newest';

    /**
     * @api
     * @since 4.0.0
     */
    const ORDER_BY_DEFAULT = 'default';

    /**
     * @api
     * @since 4.0.0
     */
    const ORDER_BY_OLDEST = 'oldest';

    /**
     * @api
     * @since 4.0.0
     */
    const ORDER_BY_RANDOM = 'random';

    /**
     * @api
     * @since 4.0.0
     */
    const ORDER_BY_RATING = 'rating';

    /**
     * @api
     * @since 4.0.0
     */
    const ORDER_BY_RELEVANCE = 'relevance';

    /**
     * @api
     * @since 4.0.0
     */
    const ORDER_BY_VIEW_COUNT = 'viewCount';
}
