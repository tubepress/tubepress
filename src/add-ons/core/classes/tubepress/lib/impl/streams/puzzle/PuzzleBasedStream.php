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
 * Delegating stream.
 */
class tubepress_lib_impl_streams_puzzle_PuzzleBasedStream implements tubepress_lib_api_streams_StreamInterface
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
     * Closes the stream and any underlying resources.
     */
    public function close()
    {
        $this->_delegate->close();
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     */
    public function detach()
    {
        $this->_delegate->detach();
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        return $this->_delegate->eof();
    }

    /**
     * Returns the remaining contents in a string, up to maxlength bytes.
     *
     * @param int $maxLength The maximum bytes to read. Defaults to -1 (read
     *                       all the remaining buffer).
     *
     * @return string
     */
    public function getContents($maxLength = -1)
    {
        return $this->_delegate->getContents($maxLength);
    }

    /**
     * Get the size of the stream if known
     *
     * @return int|null Returns the size in bytes if known, or null if unknown
     */
    public function getSize()
    {
        return $this->_delegate->getSize();
    }

    /**
     * Returns whether or not the stream is readable
     *
     * @return bool
     */
    public function isReadable()
    {
        return $this->_delegate->isReadable();
    }

    /**
     * Returns whether or not the stream is seekable
     *
     * @return bool
     */
    public function isSeekable()
    {
        return $this->_delegate->isSeekable();
    }

    /**
     * Returns whether or not the stream is writable
     *
     * @return bool
     */
    public function isWritable()
    {
        return $this->_delegate->isWritable();
    }

    /**
     * Read data from the stream
     *
     * @param int $length Read up to $length bytes from the object and return
     *                    them. Fewer than $length bytes may be returned if
     *                    underlying stream call returns fewer bytes.
     *
     * @return string     Returns the data read from the stream.
     */
    public function read($length)
    {
        return $this->_delegate->read($length);
    }

    /**
     * Seek to a position in the stream
     *
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *                    based on the seek offset. Valid values are identical
     *                    to the built-in PHP $whence values for `fseek()`.
     *                    SEEK_SET: Set position equal to offset bytes
     *                    SEEK_CUR: Set position to current location plus offset
     *                    SEEK_END: Set position to end-of-stream plus offset
     *
     * @return bool Returns TRUE on success or FALSE on failure
     * @link   http://www.php.net/manual/en/function.fseek.php
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        return $this->_delegate->seek($offset, $whence);
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int|bool Returns the position of the file pointer or false on error
     */
    public function tell()
    {
        return $this->_delegate->tell();
    }

    /**
     * Write data to the stream
     *
     * @param string $string The string that is to be written.
     *
     * @return int|bool Returns the number of bytes written to the stream on
     *                  success or FALSE on failure.
     */
    public function write($string)
    {
        return $this->_delegate->write($string);
    }

    /**
     * Attempts to seek to the beginning of the stream and reads all data into
     * a string until the end of the stream is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * @return string
     */
    public function toString()
    {
        return $this->__toString();
    }

    /**
     * Alias of toString()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_delegate->__toString();
    }

    /**
     * Flush the write buffers of the stream.
     *
     * @return bool Returns true on success and false on failure
     */
    public function flush()
    {
        return $this->_delegate->flush();
    }
}