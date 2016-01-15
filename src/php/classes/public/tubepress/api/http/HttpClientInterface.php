<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
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
 * HTTP client.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_api_http_HttpClientInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_api_http_HttpClientInterface';

    /**
     * Create and return a new {@see tubepress_api_http_message_RequestInterface} object.
     *
     * Use an absolute path to override the base path of the client, or a
     * relative path to append to the base path of the client. The URL can
     * contain the query string as well.
     *
     * @param string                                      $method  HTTP method
     * @param string|array|tubepress_api_url_UrlInterface $url     URL
     * @param array                                       $options Array of request options to apply.
     *
     * @return tubepress_api_http_message_RequestInterface
     *
     * @api
     * @since 4.0.0
     */
    function createRequest($method, $url = null, array $options = array());

    /**
     * Send a GET request
     *
     * @param string|tubepress_api_url_UrlInterface $url     URL
     * @param array                                 $options Array of request options to apply.
     *
     * @return tubepress_api_http_message_ResponseInterface
     * @throws tubepress_api_http_exception_RequestException When an error is encountered
     *
     * @api
     * @since 4.0.0
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
     *
     * @api
     * @since 4.0.0
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
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    function setDefaultOption($keyOrPath, $value);

    /**
     * Sends a single request
     *
     * @param tubepress_api_http_message_RequestInterface $request Request to send
     *
     * @return tubepress_api_http_message_ResponseInterface
     * @throws LogicException When the underlying implementation does not populate a response
     * @throws tubepress_api_http_exception_RequestException When an error is encountered
     *
     * @api
     * @since 4.0.0
     */
    function send(tubepress_api_http_message_RequestInterface $request);
}