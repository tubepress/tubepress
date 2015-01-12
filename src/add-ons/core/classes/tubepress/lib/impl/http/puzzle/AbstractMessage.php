<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Puzzle-based stream.
 */
abstract class tubepress_lib_impl_http_puzzle_AbstractMessage implements tubepress_lib_api_http_message_MessageInterface
{
    /**
     * @var puzzle_message_MessageInterface
     */
    private $_delegate;

    public function __construct(puzzle_message_MessageInterface $delegate)
    {
        $this->_delegate = $delegate;
    }

    /**
     * Appends a header value to any existing values associated with the
     * given header name.
     *
     * @param string $header Header name to add
     * @param string $value  Value of the header
     *
     * @return tubepress_lib_api_http_message_MessageInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    public function addHeader($header, $value)
    {
        $this->_delegate->addHeader($header, $value);

        return $this;
    }

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
     * @return tubepress_lib_api_http_message_MessageInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    public function addHeaders(array $headers)
    {
        $this->_delegate->addHeaders($headers);

        return $this;
    }

    /**
     * Get the body of the message
     *
     * @return tubepress_lib_api_streams_StreamInterface|null
     *
     * @api
     * @since 4.0.0
     */
    public function getBody()
    {
        $puzzleBody = $this->_delegate->getBody();

        if (!$puzzleBody) {

            return null;
        }

        if ($puzzleBody instanceof tubepress_lib_impl_streams_puzzle_PuzzleStream) {

            return $puzzleBody->getUnderlyingTubePressStream();
        }

        return new tubepress_lib_impl_streams_puzzle_PuzzleBasedStream($puzzleBody);
    }

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
    public function getHeader($header, $asArray = false)
    {
        return $this->_delegate->getHeader($header, $asArray);
    }

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
    public function getHeaders()
    {
        return $this->_delegate->getHeaders();
    }

    /**
     * Get the HTTP protocol version of the message
     *
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    public function getProtocolVersion()
    {
        return $this->_delegate->getProtocolVersion();
    }

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
    public function hasHeader($header)
    {
        return $this->_delegate->hasHeader($header);
    }

    /**
     * Remove a specific header by case-insensitive name.
     *
     * @param string $header Case-insensitive header name.
     *
     * @return tubepress_lib_api_http_message_MessageInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    public function removeHeader($header)
    {
        $this->_delegate->removeHeader($header);

        return $this;
    }

    /**
     * Sets the body of the message.
     *
     * The body MUST be a tubepress_lib_api_streams_StreamInterface object. Setting the body to null MUST
     * remove the existing body.
     *
     * @param tubepress_lib_api_streams_StreamInterface|null $tubePressBody Body.
     *
     * @return tubepress_lib_api_http_message_MessageInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    public function setBody(tubepress_lib_api_streams_StreamInterface $tubePressBody = null)
    {
        $puzzleBody = null;

        if ($tubePressBody) {

            if ($tubePressBody instanceof tubepress_lib_impl_streams_puzzle_PuzzleBasedStream) {

                $puzzleBody = $tubePressBody->getUnderlyingPuzzleStream();

            } else {

                $puzzleBody = new tubepress_lib_impl_streams_puzzle_PuzzleStream($tubePressBody);
            }
        }

        $this->_delegate->setBody($puzzleBody);

        return $this;
    }

    /**
     * Sets a header, replacing any existing values of any headers with the
     * same case-insensitive name.
     *
     * The header values MUST be a string or an array of strings.
     *
     * @param string       $header Header name
     * @param string|array $value  Header value(s)
     *
     * @return tubepress_lib_api_http_message_MessageInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    public function setHeader($header, $value)
    {
        $this->_delegate->setHeader($header, $value);

        return $this;
    }

    /**
     * Sets headers, replacing any headers that have already been set on the
     * message.
     *
     * The array keys MUST be a string. The array values must be either a
     * string or an array of strings.
     *
     * @param array $headers Headers to set.
     *
     * @return tubepress_lib_api_http_message_MessageInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    public function setHeaders(array $headers)
    {
        $this->_delegate->setHeaders($headers);

        return $this;
    }

    /**
     * Get a string representation of the message
     *
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    public function __toString()
    {
        return $this->_delegate->__toString();
    }

    /**
     * Alias of toString()
     *
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    public function toString()
    {
        return $this->__toString();
    }
}