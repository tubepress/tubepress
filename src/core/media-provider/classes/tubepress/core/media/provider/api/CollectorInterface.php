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
 * Collects items from providers.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_core_media_provider_api_CollectorInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_core_media_provider_api_CollectorInterface';

    /**
     * Collects a video gallery page.
     *
     * @return tubepress_core_media_provider_api_Page The video gallery page, never null.
     *
     * @api
     * @since 4.0.0
     */
    function collectPage();

    /**
     * Fetch a single video.
     *
     * @param string $id The video ID to fetch.
     *
     * @return tubepress_core_media_item_api_MediaItem The video, or null not found.
     *
     * @api
     * @since 4.0.0
     */
    function collectSingle($id);
}