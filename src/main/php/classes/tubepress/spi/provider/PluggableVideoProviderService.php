<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
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
     * @param string $name The name of the option to test.
     *
     * @return boolean True if this provider provides the given option, false otherwise.
     */
    function isOptionApplicable($name);

    /**
     * @param string $name The name of the gallery source to test.
     *
     * @return boolean True if this provider supplies the given gallery source, false otherwise.
     */
    function providesGallerySource($name);

    /**
     * @return string The human-readable name of this video provider.
     */
    function getFriendlyName();
}
