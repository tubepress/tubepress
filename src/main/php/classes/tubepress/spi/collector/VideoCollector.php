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
 * Collects videos from providers.
 */
interface tubepress_spi_collector_VideoCollector
{
    const _ = 'tubepress_spi_collector_VideoCollector';

    /**
     * Collects a video gallery page.
     *
     * @return tubepress_api_video_VideoGalleryPage The video gallery page, never null.
     */
    function collectVideoGalleryPage();

    /**
     * Fetch a single video.
     *
     * @param string $customVideoId The video ID to fetch.
     *
     * @return tubepress_api_video_Video The video, or null if there's a problem.
     */
    function collectSingleVideo($customVideoId);
}
