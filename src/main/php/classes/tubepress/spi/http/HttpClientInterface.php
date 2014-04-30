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
 *
 */
interface tubepress_spi_http_HttpClientInterface
{
    const _ = 'tubepress_spi_http_HttpClientInterface';

    /**
     * Create and return a new {@see tubepress_api_http_RequestInterface} object.
     *
     * Use an absolute path to override the base path of the client, or a
     * relative path to append to the base path of the client. The URL can
     * contain the query string as well.
     *
     * @param string                                      $method  HTTP method
     * @param string|array|tubepress_api_url_UrlInterface $url     URL
     * @param array                                       $options Array of request options to apply.
     *
     * @return tubepress_api_http_RequestInterface
     */
    function createRequest($method, $url = null, array $options = array());

    /**
     * Send a GET request
     *
     * @param string|tubepress_api_url_UrlInterface $url     URL
     * @param array                                 $options Array of request options to apply.
     *
     * @return tubepress_api_http_ResponseInterface
     * @throws tubepress_spi_http_RequestException When an error is encountered
     */
    function get($url = null, $options = array());

    /**
     * Get default request options of the client.
     *
     * @param string|null $keyOrPath The Path to a particular default request
     *     option to retrieve or pass null to retrieve all default request
     *     options. The syntax uses "/" to denote a path through nested PHP
     *     arrays. For example, "headers/content-type".
     *
     * @return mixed
     */
    function getDefaultOption($keyOrPath = null);

    /**
     * Set a default request option on the client so that any request created
     * by the client will use the provided default value unless overridden
     * explicitly when creating a request.
     *
     * @param string|null $keyOrPath The Path to a particular configuration
     *     value to set. The syntax uses a path notation that allows you to
     *     specify nested configuration values (e.g., 'headers/content-type').
     * @param mixed $value Default request option value to set
     */
    function setDefaultOption($keyOrPath, $value);

    /**
     * Sends a single request
     *
     * @param tubepress_api_http_RequestInterface $request Request to send
     *
     * @return tubepress_api_http_ResponseInterface
     * @throws LogicException When the underlying implementation does not populate a response
     * @throws tubepress_spi_http_RequestException When an error is encountered
     */
    function send(tubepress_api_http_RequestInterface $request);
}