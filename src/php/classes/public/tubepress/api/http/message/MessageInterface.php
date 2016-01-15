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
 * Request and response message interface
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_api_http_message_MessageInterface
{
    /**
     * Appends a header value to any existing values associated with the
     * given header name.
     *
     * @param string $header Header name to add
     * @param string $value  Value of the header
     *
     * @return tubepress_api_http_message_MessageInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    function addHeader($header, $value);

    /**
     * Merges in an associative array of headers.
     *
     * Each array key MUST be a string representing the case-insensitive name
     * of a header. Each value MUST be either a string or an array of strings.
     * For each value, the value is appended to any existing header of the same
     * name, or, if a header does not already exist by the given name, then the
     * header is added.
     *
     * @param array $headers Associative array of headers to add to the message
     *
     * @return tubepress_api_http_message_MessageInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    function addHeaders(array $headers);

    /**
     * Get the body of the message
     *
     * @return tubepress_api_streams_StreamInterface|null
     *
     * @api
     * @since 4.0.0
     */
    function getBody();

    /**
     * Retrieve a header by the given case-insensitive name.
     *
     * By default, this method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma. Because some header should not be concatenated together using a
     * comma, this method provides a Boolean argument that can be used to
     * retrieve the associated header values as an array of strings.
     *
     * @param string $header  Case-insensitive header name.
     * @param bool   $asArray Set to true to retrieve the header value as an
     *                        array of strings.
     *
     * @return array|string
     *
     * @api
     * @since 4.0.0
     */
    function getHeader($header, $asArray = false);

    /**
     * Gets all message headers.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ": " . implode(", ", $values);
     *     }
     *
     * @return array Returns an associative array of the message's headers.
     *
     * @api
     * @since 4.0.0
     */
    function getHeaders();

    /**
     * Get the HTTP protocol version of the message
     *
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    function getProtocolVersion();

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $header Case-insensitive header name.
     *
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     *
     * @api
     * @since 4.0.0
     */
    function hasHeader($header);

    /**
     * Remove a specific header by case-insensitive name.
     *
     * @param string $header Case-insensitive header name.
     *
     * @return tubepress_api_http_message_MessageInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    function removeHeader($header);

    /**
     * Sets the body of the message.
     *
     * The body MUST be a tubepress_api_streams_StreamInterface object. Setting the body to null MUST
     * remove the existing body.
     *
     * @param tubepress_api_streams_StreamInterface|null $body Body.
     *
     * @return tubepress_api_http_message_MessageInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    function setBody(tubepress_api_streams_StreamInterface $body = null);

    /**
     * Sets a header, replacing any existing values of any headers with the
     * same case-insensitive name.
     *
     * The header values MUST be a string or an array of strings.
     *
     * @param string       $header Header name
     * @param string|array $value  Header value(s)
     *
     * @return tubepress_api_http_message_MessageInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    function setHeader($header, $value);

    /**
     * Sets headers, replacing any headers that have already been set on the
     * message.
     *
     * The array keys MUST be a string. The array values must be either a
     * string or an array of strings.
     *
     * @param array $headers Headers to set.
     *
     * @return tubepress_api_http_message_MessageInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    function setHeaders(array $headers);

    /**
     * Get a string representation of the message
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