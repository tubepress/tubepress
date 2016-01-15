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
 * Represents an HTTP response message.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_api_http_message_ResponseInterface extends tubepress_api_http_message_MessageInterface
{
    /**
     * Get the effective URL that resulted in this response (e.g. the last
     * redirect URL).
     *
     * @return tubepress_api_url_UrlInterface
     *
     * @api
     * @since 4.0.0
     */
    function getEffectiveUrl();

    /**
     * Get the response reason phrase- a human readable version of the numeric
     * status code
     *
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    function getReasonPhrase();

    /**
     * Get the response status code (e.g. "200", "404", etc)
     *
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    function getStatusCode();

    /**
     * Set the effective URL that resulted in this response (e.g. the last
     * redirect URL).
     *
     * @param tubepress_api_url_UrlInterface $url Effective URL
     *
     * @return tubepress_api_http_message_ResponseInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    function setEffectiveUrl(tubepress_api_url_UrlInterface $url);

    /**
     * Parse the JSON response body and return the JSON decoded data.
     *
     * @param array $config Associative array of configuration settings used
     *     to control how the JSON data is parsed. Concrete implementations MAY
     *     add further configuration settings as needed, but they MUST implement
     *     functionality for the following options:
     *
     *     - object: Set to true to parse JSON objects as PHP objects rather
     *       than associative arrays. Defaults to false.
     *     - big_int_strings: When set to true, large integers are converted to
     *       strings rather than floats. Defaults to false.
     *
     *     Implementations are free to add further configuration settings as
     *     needed.
     *
     * @return mixed Returns the JSON decoded data based on the provided
     *     parse settings.
     * @throws RuntimeException if the response body is not in JSON format
     *
     * @api
     * @since 4.0.0
     */
    function toJson(array $config = array());
}
