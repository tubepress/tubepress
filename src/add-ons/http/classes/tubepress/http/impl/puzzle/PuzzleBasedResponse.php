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
 * Puzzle-based HTTP response.
 */
class tubepress_http_impl_puzzle_PuzzleBasedResponse extends tubepress_http_impl_puzzle_AbstractMessage implements tubepress_api_http_message_ResponseInterface
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
            $this->_effectiveUrl = new tubepress_url_impl_puzzle_PuzzleBasedUrl($puzzleUrl);
        }

        parent::__construct($this->_delegate);
    }

    /**
     * {@inheritdoc}
     */
    public function getEffectiveUrl()
    {
        return $this->_effectiveUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase()
    {
        return $this->_delegate->getReasonPhrase();
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->_delegate->getStatusCode();
    }

    /**
     * {@inheritdoc}
     */
    public function setEffectiveUrl(tubepress_api_url_UrlInterface $url)
    {
        $this->_effectiveUrl = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function toJson(array $config = array())
    {
        $body = (string) $this->getBody();

        if (!$body) {

            return;
        }

        return $this->_delegate->json($config);
    }
}
