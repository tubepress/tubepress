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
 * Puzzle-based HTTP response.
 */
class tubepress_addons_puzzle_impl_message_PuzzleBasedResponse extends tubepress_addons_puzzle_impl_message_AbstractMessage implements tubepress_api_http_ResponseInterface
{
    /**
     * @var puzzle_message_ResponseInterface
     */
    private $_delegate;

    /**
     * @var tubepress_api_url_UrlInterface
     */
    private $_effectiveUrl;

    public function __construct(puzzle_message_ResponseInterface $delegate)
    {
        $this->_delegate = $delegate;
        $urlString       = $this->_delegate->getEffectiveUrl();

        if ($urlString !== null) {

            $puzzleUrl           = puzzle_Url::fromString($urlString);
            $this->_effectiveUrl = new tubepress_addons_puzzle_impl_url_PuzzleBasedUrl($puzzleUrl);
        }

        parent::__construct($this->_delegate);
    }

    /**
     * Get the effective URL that resulted in this response (e.g. the last
     * redirect URL).
     *
     * @return tubepress_api_url_UrlInterface
     */
    public function getEffectiveUrl()
    {
        return $this->_effectiveUrl;
    }

    /**
     * Get the response reason phrase- a human readable version of the numeric
     * status code
     *
     * @return string
     */
    public function getReasonPhrase()
    {
        return $this->_delegate->getReasonPhrase();
    }

    /**
     * Get the response status code (e.g. "200", "404", etc)
     *
     * @return string
     */
    public function getStatusCode()
    {
        return $this->_delegate->getStatusCode();
    }

    /**
     * Set the effective URL that resulted in this response (e.g. the last
     * redirect URL).
     *
     * @param tubepress_api_url_UrlInterface $url Effective URL
     *
     * @return tubepress_api_http_ResponseInterface Self.
     */
    public function setEffectiveUrl(tubepress_api_url_UrlInterface $url)
    {
        $this->_effectiveUrl = $url;
    }

    /**
     * Parse the JSON response body and return the JSON decoded data.
     *
     * @param array $config Associative array of configuration settings used
     *     to control how the JSON data is parsed. Concrete implementations MAY
     *     add further configuration settings as needed, but they MUST implement
     *     public functionality for the following options:
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
     */
    public function toJson(array $config = array())
    {
        return $this->_delegate->json($config);
    }
}