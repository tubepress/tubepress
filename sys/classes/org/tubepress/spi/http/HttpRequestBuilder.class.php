<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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
 * Builds HTTP requests.
 */
interface org_tubepress_spi_http_HttpRequestBuilder
{
    /**
     * Builds a request for a list of videos
     *
     * @param int $currentPage The current page number of the gallery.
     *
     * @throws Exception If there was a problem.
     *
     * @return org_tubepress_api_http_HttpRequest The HTTP request for this gallery
     */
    function buildGalleryRequest($currentPage);

    /**
     * Builds a request for a single video
     *
     * @param string $id The video ID to search for
     *
     * @throws Exception If there was a problem.
     *
     * @return org_tubepress_api_http_HttpRequest The HTTP request for the single video given.
     */
    function buildSingleVideoRequest($id);
}
