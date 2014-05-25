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
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_core_api_provider_VideoProviderInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_core_api_provider_VideoProviderInterface';

    /**
     * Ask this video provider if it recognizes the given video ID.
     *
     * @param string $videoId The globally unique video identifier.
     *
     * @return boolean True if this provider recognizes the given video ID, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    function recognizesVideoId($videoId);

    /**
     * Fetch a video gallery page.
     *
     * @param int $currentPage The requested page number of the gallery.
     *
     * @return tubepress_core_api_video_VideoGalleryPage The video gallery page for this page. May be empty, never null.
     *
     * @api
     * @since 4.0.0
     */
    function fetchVideoGalleryPage($currentPage);

    /**
     * Fetch a single video.
     *
     * @param string $videoId The video ID to fetch.
     *
     * @return tubepress_core_api_video_Video The video, or null if unable to retrive.
     *
     * @api
     * @since 4.0.0
     */
    function fetchSingleVideo($videoId);

    /**
     * @return array An array of the valid option values for the "mode" option.
     *
     * @api
     * @since 4.0.0
     */
    function getGallerySourceNames();

    /**
     * @return string The name of this video provider. Never empty or null. All lowercase alphanumerics and dashes.
     *
     * @api
     * @since 4.0.0
     */
    function getName();

    /**
     * @return string The human-readable name of this video provider.
     *
     * @api
     * @since 4.0.0
     */
    function getFriendlyName();

    /**
     * @return array An array of meta names
     *
     * @api
     * @since 4.0.0
     */
    function getAdditionalMetaNames();
}
