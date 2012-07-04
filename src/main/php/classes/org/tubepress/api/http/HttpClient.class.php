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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../impl/classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_http_HttpRequest',
    'org_tubepress_api_http_HttpResponseHandler',
));

/**
 * Handles HTTP client functionality.
 */
interface org_tubepress_api_http_HttpClient
{
    const _ = 'org_tubepress_api_http_HttpClient';

    /**
     * Execute a given HTTP request.
     *
     * @param org_tubepress_api_http_HttpRequest $request The HTTP request.
     *
     * @throws Exception If something goes wrong.
     *
     * @return org_tubepress_api_http_HttpResponse The HTTP response.
     */
    function execute(org_tubepress_api_http_HttpRequest $request);

    /**
     * Execute a given HTTP request.
     *
     * @param org_tubepress_api_http_HttpRequest         $request The HTTP request.
     * @param org_tubepress_api_http_HttpResponseHandler $handler The HTTP response handler.
     *
     * @throws Exception If something goes wrong.
     *
     * @return string The raw entity data in the response. May be empty or null.
     */
    function executeAndHandleResponse(org_tubepress_api_http_HttpRequest $request, org_tubepress_api_http_HttpResponseHandler $handler);
}

