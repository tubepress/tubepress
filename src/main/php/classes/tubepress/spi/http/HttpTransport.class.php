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
 * Underlying HTTP transport.
 */
interface org_tubepress_spi_http_HttpTransport
{
    const _ = 'org_tubepress_spi_http_HttpTransport';

    /**
     * Determines whether or not this transport is available on the system.
     *
     * @return bool True if this transport is available on the system. False otherwise.
     */
    function isAvailable();

    /**
     * Determines if this transport can handle the given request.
     *
     * @param org_tubepress_api_http_HttpRequest $request The request to handle.
     *
     * @return bool True if this transport can handle the given request. False otherwise.
     */
    function canHandle(org_tubepress_api_http_HttpRequest $request);

    /**
     * Execute the given HTTP request.
     *
     * @param org_tubepress_api_http_HttpRequest $request The request to execute.
     *
     * @return org_tubepress_api_http_HttpResponse The HTTP response.
     */
    function handle(org_tubepress_api_http_HttpRequest $request);
}
