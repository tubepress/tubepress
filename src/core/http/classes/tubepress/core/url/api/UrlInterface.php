<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 *
 *
 *
 * This is based on Guzzle, whose copyright follows:
 *
 * Copyright (c) 2014 Michael Dowling, https://github.com/mtdowling <mtdowling@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * A URL object.
 *
 * @package TubePress\URL
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_core_url_api_UrlInterface
{
    /**
     * Add a relative path to the currently set path.
     *
     * @param string $relativePath Relative path to add
     *
     * @return tubepress_core_url_api_UrlInterface
     *
     * @api
     * @since 4.0.0
     */
    function addPath($relativePath);

    /**
     * Clones the given URL.
     *
     * @return tubepress_core_url_api_UrlInterface
     *
     * @api
     * @since 4.0.0
     */
    function getClone();

    /**
     * Get the authority part of the URL
     *
     * @return null|string
     *
     * @api
     * @since 4.0.0
     */
    function getAuthority();

    /**
     * Get the fragment part of the URL
     *
     * @return null|string
     *
     * @api
     * @since 4.0.0
     */
    function getFragment();

    /**
     * Get the host part of the URL
     *
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    function getHost();

    /**
     * Get the parts of the URL as an array
     *
     * @return array
     *
     * @api
     * @since 4.0.0
     */
    function getParts();

    /**
     * Get the password part of the URL
     *
     * @return null|string
     *
     * @api
     * @since 4.0.0
     */
    function getPassword();

    /**
     * Get the path part of the URL
     *
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    function getPath();

    /**
     * Get the path segments of the URL as an array
     *
     * @return array
     *
     * @api
     * @since 4.0.0
     */
    function getPathSegments();

    /**
     * Get the port part of the URl.
     *
     * If no port was set, this method will return the default port for the
     * scheme of the URI.
     *
     * @return int|null
     *
     * @api
     * @since 4.0.0
     */
    function getPort();

    /**
     * @return tubepress_core_url_api_QueryInterface
     *
     * @api
     * @since 4.0.0
     */
    function getQuery();

    /**
     * Get the scheme part of the URL
     *
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    function getScheme();

    /**
     * Get the username part of the URl
     *
     * @return null|string
     *
     * @api
     * @since 4.0.0
     */
    function getUsername();

    /**
     * Check if this is an absolute URL
     *
     * @return bool
     *
     * @api
     * @since 4.0.0
     */
    function isAbsolute();

    /**
     * Removes dot segments from a URL
     *
     * @return tubepress_core_url_api_UrlInterface
     * @link http://tools.ietf.org/html/rfc3986#section-5.2.4
     *
     * @api
     * @since 4.0.0
     */
    function removeDotSegments();

    /**
     * Set the fragment part of the URL
     *
     * @param string $fragment Fragment to set
     *
     * @return tubepress_core_url_api_UrlInterface
     *
     * @api
     * @since 4.0.0
     */
    function setFragment($fragment);

    /**
     * Set the host of the request.
     *
     * @param string $host Host to set (e.g. www.yahoo.com, yahoo.com)
     *
     * @return tubepress_core_url_api_UrlInterface
     *
     * @api
     * @since 4.0.0
     */
    function setHost($host);

    /**
     * Set the password part of the URL
     *
     * @param string $password Password to set
     *
     * @return tubepress_core_url_api_UrlInterface
     *
     * @api
     * @since 4.0.0
     */
    function setPassword($password);

    /**
     * Set the path part of the URL
     *
     * @param string $path Path string to set
     *
     * @return tubepress_core_url_api_UrlInterface
     *
     * @api
     * @since 4.0.0
     */
    function setPath($path);

    /**
     * Set the port part of the URL
     *
     * @param int $port Port to set
     *
     * @return tubepress_core_url_api_UrlInterface
     *
     * @api
     * @since 4.0.0
     */
    function setPort($port);

    /**
     * Set the query part of the URL
     *
     * @param tubepress_core_url_api_QueryInterface|string|array $query Query string value to set. Can
     *     be a string that will be parsed into a tubepress_core_url_api_QueryInterface object, an array
     *     of key value pairs, or a tubepress_core_url_api_QueryInterface object.
     *
     * @return tubepress_core_url_api_UrlInterface
     * @throws InvalidArgumentException
     *
     * @api
     * @since 4.0.0
     */
    function setQuery($query);

    /**
     * Set the scheme part of the URL (http, https, ftp, etc)
     *
     * @param string $scheme Scheme to set
     *
     * @return tubepress_core_url_api_UrlInterface
     *
     * @api
     * @since 4.0.0
     */
    function setScheme($scheme);

    /**
     * Set the username part of the URL
     *
     * @param string $username Username to set
     *
     * @return tubepress_core_url_api_UrlInterface
     *
     * @api
     * @since 4.0.0
     */
    function setUsername($username);

    /**
     * Returns the URL as a URL string
     *
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    function toString();

    /**
     * Alias of toString()
     *
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    function __toString();
}
