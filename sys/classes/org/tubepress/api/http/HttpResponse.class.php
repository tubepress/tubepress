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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_http_HttpMessage',
));

/**
 * An HTTP response.
 */
class org_tubepress_api_http_HttpResponse extends org_tubepress_api_http_HttpMessage
{
    const _ = 'org_tubepress_api_http_HttpResponse';

    const HTTP_STATUS_CODE_OK = 200;

    const HTTP_HEADER_TRANSFER_ENCODING = 'Transfer-Encoding';

    private $_statusCode;

    /**
     * Gets the HTTP status code.
     *
     * @return int The HTTP status code.
     */
    function getStatusCode()
    {
        return $this->_statusCode;
    }

    /**
     * Sets the HTTP status code.
     *
     * @param int $code The HTTP status code.
     *
     * @throws Exception If the given code is not an integer between 100 and 599.
     *
     * @return void
     */
    function setStatusCode($code)
    {
        if (! is_numeric($code)) {

            throw new Exception('Status code must be an integer (' . $code . ')');
        }

        $code = intval($code);

        if ($code < 100 || $code > 599) {

            throw new Exception('Status code must be in the range of 100 - 599');
        }

        $this->_statusCode = $code;
    }
}
