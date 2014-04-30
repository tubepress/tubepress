<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 * 
 * This file is part of TubePress (http://tubepress.com)
 * 
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * A URL object.
 *
 * @package TubePress\URL
 */
interface tubepress_api_url_UrlInterface
{
    /**
     * Add a relative path to the currently set path.
     *
     * @param string $relativePath Relative path to add
     *
     * @return tubepress_api_url_UrlInterface
     */
    function addPath($relativePath);

    /**
     * Get the authority part of the URL
     *
     * @return null|string
     */
    function getAuthority();

    /**
     * Get the fragment part of the URL
     *
     * @return null|string
     */
    function getFragment();

    /**
     * Get the host part of the URL
     *
     * @return string
     */
    function getHost();

    /**
     * Get the parts of the URL as an array
     *
     * @return array
     */
    function getParts();

    /**
     * Get the password part of the URL
     *
     * @return null|string
     */
    function getPassword();

    /**
     * Get the path part of the URL
     *
     * @return string
     */
    function getPath();

    /**
     * Get the path segments of the URL as an array
     *
     * @return array
     */
    function getPathSegments();

    /**
     * Get the port part of the URl.
     *
     * If no port was set, this method will return the default port for the
     * scheme of the URI.
     *
     * @return int|null
     */
    function getPort();

    /**
     * @return tubepress_api_url_QueryInterface
     */
    function getQuery();

    /**
     * Get the scheme part of the URL
     *
     * @return string
     */
    function getScheme();

    /**
     * Get the username part of the URl
     *
     * @return null|string
     */
    function getUsername();

    /**
     * Check if this is an absolute URL
     *
     * @return bool
     */
    function isAbsolute();

    /**
     * Removes dot segments from a URL
     *
     * @return tubepress_api_url_UrlInterface
     * @link http://tools.ietf.org/html/rfc3986#section-5.2.4
     */
    function removeDotSegments();

    /**
     * Set the fragment part of the URL
     *
     * @param string $fragment Fragment to set
     *
     * @return tubepress_api_url_UrlInterface
     */
    function setFragment($fragment);

    /**
     * Set the host of the request.
     *
     * @param string $host Host to set (e.g. www.yahoo.com, yahoo.com)
     *
     * @return tubepress_api_url_UrlInterface
     */
    function setHost($host);

    /**
     * Set the password part of the URL
     *
     * @param string $password Password to set
     *
     * @return tubepress_api_url_UrlInterface
     */
    function setPassword($password);

    /**
     * Set the path part of the URL
     *
     * @param string $path Path string to set
     *
     * @return tubepress_api_url_UrlInterface
     */
    function setPath($path);

    /**
     * Set the port part of the URL
     *
     * @param int $port Port to set
     *
     * @return tubepress_api_url_UrlInterface
     */
    function setPort($port);

    /**
     * Set the query part of the URL
     *
     * @param tubepress_api_url_QueryInterface|string|array $query Query string value to set. Can
     *     be a string that will be parsed into a tubepress_api_url_QueryInterface object, an array
     *     of key value pairs, or a tubepress_api_url_QueryInterface object.
     *
     * @return tubepress_api_url_UrlInterface
     * @throws InvalidArgumentException
     */
    function setQuery($query);

    /**
     * Set the scheme part of the URL (http, https, ftp, etc)
     *
     * @param string $scheme Scheme to set
     *
     * @return tubepress_api_url_UrlInterface
     */
    function setScheme($scheme);

    /**
     * Set the username part of the URL
     *
     * @param string $username Username to set
     *
     * @return tubepress_api_url_UrlInterface
     */
    function setUsername($username);

    /**
     * Returns the URL as a URL string
     *
     * @return string
     */
    function toString();

    /**
     * Alias of toString()
     *
     * @return string
     */
    function __toString();
}
