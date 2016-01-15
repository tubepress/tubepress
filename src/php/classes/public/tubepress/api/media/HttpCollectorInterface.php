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
 * Collects items from providers.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_api_media_HttpCollectorInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_api_media_HttpCollectorInterface';

    /**
     * Collects a media gallery page.
     *
     * @param int                                              $pageNumber The page number.
     * @param tubepress_spi_media_HttpFeedHandlerInterface $feedHandler The feed handler.
     *
     * @return tubepress_api_media_MediaPage The media gallery page, never null.
     *
     * @api
     * @since 4.0.0
     */
    function collectPage($pageNumber, tubepress_spi_media_HttpFeedHandlerInterface $feedHandler);

    /**
     * Fetch a single media item.
     *
     * @param string                                           $id The media item ID to fetch.
     * @param tubepress_spi_media_HttpFeedHandlerInterface $feedHandler The feed handler.
     *
     * @return tubepress_api_media_MediaItem The media item, or null not found.
     *
     * @api
     * @since 4.0.0
     */
    function collectSingle($id, tubepress_spi_media_HttpFeedHandlerInterface $feedHandler);
}