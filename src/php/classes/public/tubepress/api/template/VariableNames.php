<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
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
interface tubepress_api_template_VariableNames
{
    /**
     * tubepress_api_url_UrlInterface
     *
     * @api
     * @since 4.0.0
     */
    const EMBEDDED_DATA_URL = 'dataURL';

    /**
     * int
     *
     * @api
     * @since 4.0.0
     */
    const EMBEDDED_HEIGHT_PX = 'embeddedHeightPx';

    /**
     * string
     *
     * @api
     * @since 4.0.0
     */
    const EMBEDDED_SOURCE = 'embeddedSource';

    /**
     * int
     *
     * @api
     * @since 4.0.0
     */
    const EMBEDDED_WIDTH_PX = 'embeddedWidthPx';

    /**
     * string
     *
     * @api
     * @since 4.0.0
     */
    const GALLERY_PAGINATION_HTML = 'paginationHTML';

    /**
     * int
     *
     * @api
     * @since 4.0.0
     */
    const GALLERY_PAGINATION_CURRENT_PAGE_NUMBER = 'paginationCurrentPageNumber';

    /**
     * int
     *
     * @api
     * @since 4.0.0
     */
    const GALLERY_PAGINATION_TOTAL_ITEMS = 'paginationTotalItems';

    /**
     * int
     *
     * @api
     * @since 4.0.0
     */
    const GALLERY_PAGINATION_RESULTS_PER_PAGE = 'paginationResultsPerPage';

    /**
     * bool
     *
     * @api
     * @since 4.0.0
     */
    const GALLERY_PAGINATION_SHOW_BOTTOM = 'paginationShowBottom';

    /**
     * bool
     *
     * @api
     * @since 4.0.0
     */
    const GALLERY_PAGINATION_SHOW_TOP = 'paginationShowTop';

    /**
     * string
     *
     * @api
     * @since 4.0.0
     */
    const GALLERY_PAGINATION_HREF_FORMAT = 'paginationHrefFormat';

    /**
     * int
     *
     * @api
     * @since 4.0.0
     */
    const GALLERY_THUMBNAIL_HEIGHT_PX = 'thumbHeightPx';

    /**
     * int
     *
     * @api
     * @since 4.0.0
     */
    const GALLERY_THUMBNAIL_WIDTH_PX = 'thumbWidthPx';

    /**
     * string
     *
     * @api
     * @since 4.0.0
     */
    const HTML_WIDGET_ID = 'widgetId';

    /**
     * tubepress_api_media_MediaItem
     *
     * @api
     * @since 4.0.0
     */
    const MEDIA_ITEM = 'mediaItem';

    /**
     * string
     *
     * @api
     * @since 4.0.0
     */
    const MEDIA_ITEM_ID = 'itemId';

    /**
     * tubepress_api_media_MediaPage
     *
     * @api
     * @since 4.0.0
     */
    const MEDIA_PAGE = 'mediaPage';

    /**
     * string[]
     *
     * @api
     * @since 4.0.0
     */
    const MEDIA_ITEM_ATTRIBUTES_TO_SHOW = 'attributesToShow';

    /**
     * string[] (associative)
     *
     * @api
     * @since 4.0.0
     */
    const MEDIA_ITEM_ATTRIBUTE_LABELS = 'attributeLabels';

    /**
     * string
     *
     * @api
     * @since 4.0.0
     */
    const PLAYER_HTML = 'playerHTML';

    /**
     * tubepress_api_url_UrlInterface
     *
     * @api
     * @since 4.0.0
     */
    const SEARCH_HANDLER_URL = 'searchHandlerURL';

    /**
     * @api
     * @since 4.0.0
     */
    const SEARCH_HIDDEN_INPUTS = 'searchHiddenInputs';

    /**
     * @api
     * @since 4.0.0
     */
    const SEARCH_TARGET_DOM_ID = 'searchTargetDomId';

    /**
     * @api
     * @since 4.0.0
     */
    const SEARCH_TERMS = 'searchTerms';
}