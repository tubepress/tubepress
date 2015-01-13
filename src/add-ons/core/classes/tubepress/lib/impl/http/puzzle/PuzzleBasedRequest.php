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
 * Puzzle-based HTTP request.
 */
class tubepress_lib_impl_http_puzzle_PuzzleBasedRequest extends tubepress_lib_impl_http_puzzle_AbstractMessage implements tubepress_lib_api_http_message_RequestInterface
{
    /**
     * @var puzzle_message_RequestInterface
     */
    private $_delegate;

    /**
     * @var tubepress_platform_api_url_UrlInterface
     */
    private $_url;

    public function __construct(puzzle_message_RequestInterface $delegate)
    {
        $this->_delegate = $delegate;
        $stringUrl       = $this->_delegate->getUrl();
        $puzzleUrl       = puzzle_Url::fromString($stringUrl);
        $this->_url      = new tubepress_platform_impl_url_puzzle_PuzzleBasedUrl($puzzleUrl);

        parent::__construct($this->_delegate);
    }

    /**
     * Get the request's configuration options
     *
     * @return array
     *
     * @api
     * @since 4.0.0
     */
    public function getConfig()
    {
        return $this->_delegate->getConfig()->toArray();
    }

    /**
     * Get the HTTP method of the request
     *
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    public function getMethod()
    {
        return $this->_delegate->getMethod();
    }

    /**
     * Gets the request URL.
     *
     * @return tubepress_platform_api_url_UrlInterface Returns the URL.
     *
     * @api
     * @since 4.0.0
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @param array $config The incoming configuration.
     *
     * @return tubepress_lib_api_http_message_RequestInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    public function setConfig(array $config)
    {
        $this->_delegate->getConfig()->clear();
        $this->_delegate->getConfig()->merge($config);
    }

    /**
     * Set the HTTP method of the request
     *
     * @param string $method HTTP method
     *
     * @return tubepress_lib_api_http_message_RequestInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    public function setMethod($method)
    {
        $this->_delegate->setMethod($method);

        return $this;
    }

    /**
     * Sets the request URL.
     *
     * @param tubepress_platform_api_url_UrlInterface $url Request URL.
     *
     * @return tubepress_lib_api_http_message_RequestInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    public function setUrl(tubepress_platform_api_url_UrlInterface $url)
    {
        $this->_url = $url;
    }
}