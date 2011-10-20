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
 * Decodes chunk-transfer encoded HTTP messages.
 */
interface org_tubepress_spi_http_HttpChunkedTransferMessageDecoder
{
    const _ = 'org_tubepress_spi_http_HttpChunkedTransferMessageDecoder';

    /**
     * Determines whether or not response contains a body that is chunk-transfer encoded.
     *
     * @param org_tubepress_api_http_HttpResponse $response The response to examine.
     *
     * @return bool True if this response contains a body that is chunk-transfer encoded. False otherwise.
     */
    function containsChunkedData(org_tubepress_api_http_HttpResponse $response);

    /**
     * Decodes chunked-transfer encoded data.
     *
     * @param string $data The chunked-transfer data.
     *
     * @return string The decoded data.
     */
    function dechunk($data);
}
