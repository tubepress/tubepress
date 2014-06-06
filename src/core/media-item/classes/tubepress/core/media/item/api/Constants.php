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
interface tubepress_core_media_item_api_Constants
{
    const IOC_TAG_EASY_ATTRIBUTE_FORMATTER = 'sdf';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_AUTHOR = 'author';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_CATEGORY = 'category';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_DESCRIPTION = 'description';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_ID = 'id';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_KEYWORDS = 'tags';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_LENGTH = 'length';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_TITLE = 'title';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_UPLOADED = 'uploaded';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_URL = 'url';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_VIEWS = 'views';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_DATEFORMAT = 'dateFormat';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_DESC_LIMIT = 'descriptionLimit';

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_RELATIVE_DATES = 'relativeDates';

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
    const ATTRIBUTE_PROVIDER                 = 'provider';
    const ATTRIBUTE_RATING_AVERAGE           = 'ratingAverage';
    const ATTRIBUTE_RATING_COUNT             = 'ratingCount';
    const ATTRIBUTE_THUMBNAIL_URL            = 'thumbnailUrl';
    const ATTRIBUTE_TIME_PUBLISHED_FORMATTED = 'timePublishedFormatted';
    const ATTRIBUTE_TIME_PUBLISHED_UNIXTIME  = 'timePublishedUnixTime';
    const ATTRIBUTE_TITLE                    = 'title';
    const ATTRIBUTE_VIEW_COUNT               = 'viewCount';
}