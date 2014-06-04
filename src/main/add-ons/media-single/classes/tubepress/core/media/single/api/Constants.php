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
interface tubepress_core_media_single_api_Constants
{
    /**
     * This event is fired when TubePress builds HTML for a single media item (not inside a gallery).
     *
     * @subject `string` The HTML for the single item.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_SINGLE_ITEM_HTML = 'tubepress.core.media.single.html';

    /**
     * This event is fired when TubePress builds the PHP/HTML template for a single media item (not inside a gallery)
     *
     * @subject `tubepress_core_template_api_TemplateInterface` The template.
     *
     * @argument <var>item</var> (`{@link tubepress_core_provider_api_MediaItem}`): The media item.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_SINGLE_ITEM_TEMPLATE = 'tubepress.core.media.single.template';



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

    /**
     * @api
     * @since 3.1.0
     */
    const OPTION_VIDEO = 'video';
}