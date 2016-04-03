<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
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
class tubepress_http_impl_puzzle_PuzzleBasedRequest extends tubepress_http_impl_puzzle_AbstractMessage implements tubepress_api_http_message_RequestInterface
{
    /**
     * @var puzzle_message_RequestInterface
     */
    private $_delegate;

    /**
     * @var tubepress_api_url_UrlInterface
     */
    private $_url;

    public function __construct(puzzle_message_RequestInterface $delegate)
    {
        $this->_delegate = $delegate;
        $stringUrl       = $this->_delegate->getUrl();
        $puzzleUrl       = puzzle_Url::fromString($stringUrl);
        $this->_url      = new tubepress_url_impl_puzzle_PuzzleBasedUrl($puzzleUrl);

        parent::__construct($this->_delegate);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->_delegate->getConfig()->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->_delegate->getMethod();
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(array $config)
    {
        $this->_delegate->getConfig()->clear();
        $this->_delegate->getConfig()->merge($config);
    }

    /**
     * {@inheritdoc}
     */
    public function setMethod($method)
    {
        $this->_delegate->setMethod($method);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl(tubepress_api_url_UrlInterface $url)
    {
        $this->_url = $url;
    }
}
