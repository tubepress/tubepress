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

abstract class tubepress_http_impl_puzzle_AbstractMessage implements tubepress_api_http_message_MessageInterface
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
     * {@inheritdoc}
     */
    public function addHeader($header, $value)
    {
        $this->_delegate->addHeader($header, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addHeaders(array $headers)
    {
        $this->_delegate->addHeaders($headers);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        $puzzleBody = $this->_delegate->getBody();

        if (!$puzzleBody) {

            return null;
        }

        if ($puzzleBody instanceof tubepress_http_impl_puzzle_streams_PuzzleStream) {

            return $puzzleBody->getUnderlyingTubePressStream();
        }

        return new tubepress_http_impl_puzzle_streams_PuzzleBasedStream($puzzleBody);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($header, $asArray = false)
    {
        return $this->_delegate->getHeader($header, $asArray);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->_delegate->getHeaders();
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {
        return $this->_delegate->getProtocolVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader($header)
    {
        return $this->_delegate->hasHeader($header);
    }

    /**
     * {@inheritdoc}
     */
    public function removeHeader($header)
    {
        $this->_delegate->removeHeader($header);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setBody(tubepress_api_streams_StreamInterface $tubePressBody = null)
    {
        $puzzleBody = null;

        if ($tubePressBody) {

            if ($tubePressBody instanceof tubepress_http_impl_puzzle_streams_PuzzleBasedStream) {

                $puzzleBody = $tubePressBody->getUnderlyingPuzzleStream();

            } else {

                $puzzleBody = new tubepress_http_impl_puzzle_streams_PuzzleStream($tubePressBody);
            }
        }

        $this->_delegate->setBody($puzzleBody);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setHeader($header, $value)
    {
        $this->_delegate->setHeader($header, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setHeaders(array $headers)
    {
        $this->_delegate->setHeaders($headers);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->_delegate->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        return $this->__toString();
    }
}
