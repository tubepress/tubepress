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
 * Builds URLs to send out to a remote provider
 *
 */
interface org_tubepress_api_feed_UrlBuilder
{
    const _ = 'org_tubepress_api_feed_UrlBuilder';

    /**
     * Builds a URL for a list of videos
     *
     * @param int $currentPage The current page number of the gallery.
     *
     * @throws Exception If there was a problem.
     *
     * @return string The request URL for this gallery
     */
    function buildGalleryUrl($currentPage);

    /**
     * Builds a request url for a single video
     *
     * @param string $id The video ID to search for
     *
     * @throws Exception If there was a problem.
     *
     * @return string The URL for the single video given.
     */
    function buildSingleVideoUrl($id);
}