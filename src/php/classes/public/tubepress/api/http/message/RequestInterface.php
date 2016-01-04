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
 * Generic HTTP request interface.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_api_http_message_RequestInterface extends tubepress_api_http_message_MessageInterface
{
    const METHOD_CONNECT = 'CONNECT';
    const METHOD_DELETE  = 'DELETE';
    const METHOD_GET     = 'GET';
    const METHOD_HEAD    = 'HEAD';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_PATCH   = 'PATCH';
    const METHOD_POST    = 'POST';
    const METHOD_PUT     = 'PUT';
    const METHOD_TRACE   = 'TRACE';

    /**
     * Get the request's configuration options
     *
     * @return array
     *
     * @api
     * @since 4.0.0
     */
    function getConfig();

    /**
     * Get the HTTP method of the request
     *
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    function getMethod();

    /**
     * Gets the request URL.
     *
     * @return tubepress_api_url_UrlInterface Returns the URL.
     *
     * @api
     * @since 4.0.0
     */
    function getUrl();

    /**
     * @param array $config The incoming configuration.
     *
     * @return tubepress_api_http_message_RequestInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    function setConfig(array $config);

    /**
     * Set the HTTP method of the request
     *
     * @param string $method HTTP method
     *
     * @return tubepress_api_http_message_RequestInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    function setMethod($method);

    /**
     * Sets the request URL.
     *
     * @param tubepress_api_url_UrlInterface $url Request URL.
     *
     * @return tubepress_api_http_message_RequestInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    function setUrl(tubepress_api_url_UrlInterface $url);
}