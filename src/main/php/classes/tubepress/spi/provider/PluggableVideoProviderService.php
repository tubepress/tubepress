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
 * Video provider interface.
 */
interface tubepress_spi_provider_PluggableVideoProviderService
{
    const _ = 'tubepress_spi_provider_PluggableVideoProviderService';

    /**
     * Ask this video provider if it recognizes the given video ID.
     *
     * @param string $videoId The globally unique video identifier.
     *
     * @return boolean True if this provider recognizes the given video ID, false otherwise.
     */
    function recognizesVideoId($videoId);

    /**
     * Fetch a video gallery page.
     *
     * @param int $currentPage The requested page number of the gallery.
     *
     * @return tubepress_api_video_VideoGalleryPage The video gallery page for this page. May be empty, never null.
     */
    function fetchVideoGalleryPage($currentPage);

    /**
     * Fetch a single video.
     *
     * @param string $videoId The video ID to fetch.
     *
     * @return tubepress_api_video_Video The video, or null if unable to retrive.
     */
    function fetchSingleVideo($videoId);

    /**
     * @return array An array of the valid option values for the "mode" option.
     */
    function getGallerySourceNames();

    /**
     * @return string The name of this video provider. Never empty or null. All lowercase alphanumerics and dashes.
     */
    function getName();

    /**
     * @return string The human-readable name of this video provider.
     */
    function getFriendlyName();

    /**
     * @return array An array of meta names
     */
    function getAdditionalMetaNames();
}
