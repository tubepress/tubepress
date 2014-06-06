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
interface tubepress_core_html_gallery_api_Constants
{
    /**
     * This event is fired after TubePress builds the HTML for gallery pagination.
     *
     * @subject `string` The pagination HTML.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_HTML_PAGINATION = 'tubepress.core.media.html.event.html.pagination';

    /**
     * This event is fired after TubePress builds the HTML for a media gallery.
     *
     * @subject `string` The HTML for the thumbnail gallery.
     *
     * @argument <var>page</var> (`{@link tubepress_core_media_provider_api_Page}`): The backing {@link tubepress_core_media_provider_api_Page}.
     * @argument <var>pageNumber</var> (`integer`): The page number.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_HTML_THUMBNAIL_GALLERY = 'tubepress.core.media.html.event.html.gallery';

    /**
     * This event is fired after TubePress builds the pagination HTML template.
     *
     * @subject `tubepress_core_template_api_TemplateInterface` The template for the pagination.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_TEMPLATE_PAGINATION = 'tubepress.core.media.html.event.template.pagination';

    /**
     * This event is fired after TubePress builds the PHP/HTML template for a media gallery.
     *
     * @subject `tubepress_core_template_api_TemplateInterface` The template.
     *
     * @argument <var>page</var> (`{@link tubepress_core_media_provider_api_Page}`): The backing {@link tubepress_core_media_provider_api_Page}
     * @argument <var>pageNumber</var> (`integer`): The page number.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_TEMPLATE_THUMBNAIL_GALLERY = 'tubepress.core.media.html.event.template.gallery';

    /**
     * This event is fired after TubePress builds the gallery initialization JSON, which is inserted immediately
     * after each gallery as it appears in the HTML.
     *
     * @subject `array` An associative `array` that will be converted into JSON and applied as
     *                  init code for the gallery in JavaScript.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_GALLERY_INIT_JS = 'tubepress.core.media.html.event.galleryInitJs';


    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_AUTONEXT = 'autoNext';

    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_GALLERY_SOURCE = 'mode';

    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_SEQUENCE = 'sequence';

    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_AJAX_PAGINATION = 'ajaxPagination';

    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_FLUID_THUMBS = 'fluidThumbs';

    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_HQ_THUMBS = 'hqThumbs';

    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_PAGINATE_ABOVE = 'paginationAbove';

    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_PAGINATE_BELOW = 'paginationBelow';

    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_RANDOM_THUMBS = 'randomize_thumbnails';

    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_THUMB_HEIGHT = 'thumbHeight';

    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_THUMB_WIDTH = 'thumbWidth';
}