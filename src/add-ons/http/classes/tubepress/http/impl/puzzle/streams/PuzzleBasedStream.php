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
 * Delegating stream.
 */
class tubepress_http_impl_puzzle_streams_PuzzleBasedStream implements tubepress_api_streams_StreamInterface
{
    /**
     * @var puzzle_stream_StreamInterface
     */
    private $_delegate;

    public function __construct(puzzle_stream_StreamInterface $delegate)
    {
        $this->_delegate = $delegate;
    }

    /**
     * @return puzzle_stream_StreamInterface
     */
    public function getUnderlyingPuzzleStream()
    {
        return $this->_delegate;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $this->_delegate->close();
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {
        $this->_delegate->detach();
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        return $this->_delegate->eof();
    }

    /**
     * {@inheritdoc}
     */
    public function getContents($maxLength = -1)
    {
        return $this->_delegate->getContents($maxLength);
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->_delegate->getSize();
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable()
    {
        return $this->_delegate->isReadable();
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable()
    {
        return $this->_delegate->isSeekable();
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable()
    {
        return $this->_delegate->isWritable();
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        return $this->_delegate->read($length);
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        return $this->_delegate->seek($offset, $whence);
    }

    /**
     * {@inheritdoc}
     */
    public function tell()
    {
        return $this->_delegate->tell();
    }

    /**
     * {@inheritdoc}
     */
    public function write($string)
    {
        return $this->_delegate->write($string);
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        return $this->__toString();
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
    public function flush()
    {
        return $this->_delegate->flush();
    }
}
