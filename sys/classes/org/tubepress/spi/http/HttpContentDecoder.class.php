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
 * Performs Content-Encoding decoding on HTTP entity bodies.
 */
interface org_tubepress_spi_http_HttpContentDecoder
{
    const _ = 'org_tubepress_spi_http_HttpContentDecoder';

    /**
     * Determines if this message needs to be decoded.
     *
     * @param org_tubepress_api_http_HttpResponse $response The HTTP response.
     *
     * @return boolean True if this response should be decoded. False otherwise.
     */
    function needsToBeDecoded(org_tubepress_api_http_HttpResponse $response);

    /**
     * Decodes transfer encoded data in the entity body of this response and re-assigns
     * the decoded entity to the response.
     *
     * @param org_tubepress_api_http_HttpResponse $response The HTTP response.
     *
     * @return void
     */
    function decode(org_tubepress_api_http_HttpResponse $response);

    /**
     * Get the Accept-Encoding header value to send with HTTP requests.
     *
     * @return string the Accept-Encoding header value to send with HTTP requests. May be null.
     */
    function getAcceptEncodingHeaderValue();
}
