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
 * Describes a stream instance.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_api_streams_StreamInterface
{
    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    function close();

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    function detach();

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     *
     * @api
     * @since 4.0.0
     */
    function eof();

    /**
     * Flush the write buffers of the stream.
     *
     * @return bool Returns true on success and false on failure
     */
    function flush();

    /**
     * Returns the remaining contents in a string, up to maxlength bytes.
     *
     * @param int $maxLength The maximum bytes to read. Defaults to -1 (read
     *                       all the remaining buffer).
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    function getContents($maxLength = -1);

    /**
     * Get the size of the stream if known
     *
     * @return int|null Returns the size in bytes if known, or null if unknown
     *
     * @api
     * @since 4.0.0
     */
    function getSize();

    /**
     * Returns whether or not the stream is readable
     *
     * @return bool
     *
     * @api
     * @since 4.0.0
     */
    function isReadable();

    /**
     * Returns whether or not the stream is seekable
     *
     * @return bool
     *
     * @api
     * @since 4.0.0
     */
    function isSeekable();

    /**
     * Returns whether or not the stream is writable
     *
     * @return bool
     *
     * @api
     * @since 4.0.0
     */
    function isWritable();

    /**
     * Read data from the stream
     *
     * @param int $length Read up to $length bytes from the object and return
     *                    them. Fewer than $length bytes may be returned if
     *                    underlying stream call returns fewer bytes.
     *
     * @return string     Returns the data read from the stream.
     *
     * @api
     * @since 4.0.0
     */
    function read($length);

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
     *
     * @api
     * @since 4.0.0
     */
    function seek($offset, $whence = SEEK_SET);

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int|bool Returns the position of the file pointer or false on error
     *
     * @api
     * @since 4.0.0
     */
    function tell();

    /**
     * Attempts to seek to the beginning of the stream and reads all data into
     * a string until the end of the stream is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    function toString();

    /**
     * Write data to the stream
     *
     * @param string $string The string that is to be written.
     *
     * @return int|bool Returns the number of bytes written to the stream on
     *                  success or FALSE on failure.
     *
     * @api
     * @since 4.0.0
     */
    function write($string);

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