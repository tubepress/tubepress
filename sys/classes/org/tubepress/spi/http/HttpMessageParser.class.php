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
    'org_tubepress_api_http_HttpMessage',
));

/**
 * Parses out HTTP messages.
 */
interface org_tubepress_spi_http_HttpMessageParser
{
    const _ = 'org_tubepress_spi_http_HttpMessageParser';

    /**
     * Gets a string representation of the headers of the given HTTP message.
     *
     * @param org_tubepress_api_http_HttpMessage $message The HTTP message.
     *
     * @return string The string representation of the HTTP headers. May be null or empty.
     */
    function getHeaderArrayAsString(org_tubepress_api_http_HttpMessage $message);

    /**
     * Given a raw string of headers, return an associative array of the headers.
     *
     * @param string $rawHeaderString The header string.
     *
     * @return array An associative array of headers with name => value. Maybe null or empty.
     */
    function getArrayOfHeadersFromRawHeaderString($rawHeaderString);

    /**
     * Give the raw string of an HTTP message, return just the header part of the message.
     *
     * @param string $message The raw HTTP message as string.
     *
     * @return string Just the HTTP headers part of the message. May be null or empty.
     */
    function getHeadersStringFromRawHttpMessage($message);

    /**
     * Give the raw string of an HTTP message, return just the body part of the message.
     *
     * @param string $message The raw HTTP message as string.
     *
     * @return string Just the HTTP body part of the message. May be null or empty.
     */
    function getBodyStringFromRawHttpMessage($message);
}
