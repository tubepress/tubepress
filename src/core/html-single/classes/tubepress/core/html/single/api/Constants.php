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
interface tubepress_core_html_single_api_Constants
{
    /**
     * This event is fired when TubePress builds HTML for a single media item (not inside a gallery).
     *
     * @subject `string` The HTML for the single item.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_SINGLE_ITEM_HTML = 'tubepress.core.html.single.html';

    /**
     * This event is fired when TubePress builds the PHP/HTML template for a single media item (not inside a gallery)
     *
     * @subject `tubepress_core_template_api_TemplateInterface` The template.
     *
     * @argument <var>item</var> (`{@link tubepress_core_media_item_api_MediaItem}`): The media item.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_SINGLE_ITEM_TEMPLATE = 'tubepress.core.html.single.template';

    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_MEDIA_ITEM_ID = 'video';

    /**
     * @api
     * @since 4.0.0
     */
    const TEMPLATE_VAR_MEDIA_ITEM = 'mediaItem';
}