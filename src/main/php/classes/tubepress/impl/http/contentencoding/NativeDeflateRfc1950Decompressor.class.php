<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../../../impl/classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_impl_http_contentencoding_AbstractDecompressorCommand',
));

/**
 * Deflates data according to RFC 1950.
 */
class org_tubepress_impl_http_contentencoding_NativeDeflateRfc1950Decompressor extends org_tubepress_impl_http_contentencoding_AbstractDecompressorCommand
{
    /**
     * Get the uncompressed version of the given data.
     *
     * @param string $compressed The compressed data.
     *
     * @return string The uncompressed data.
     */
    protected function getUncompressed($compressed)
    {
        $decompressed = @gzuncompress($compressed);

        if ($decompressed === false) {

            throw new Exception('Could not decompress data with gzuncompress()');
        }

        return $decompressed;
    }

    /**
     * Get the "friendly" name for logging purposes.
     *
     * @return string The "friendly" name of this compression.
     */
    protected function getDecompressionName()
    {
        return 'RFC 1950';
    }

    /**
     * Determines if this compression is available on the host system.
     *
     * @return boolean True if compression is available on the host system, false otherwise.
     */
    protected function isAvailiable()
    {
        return function_exists('gzuncompress');
    }

    /**
     * Get the Content-Encoding header value that this command can handle.
     *
     * @return string The Content-Encoding header value that this command can handle.
     */
    protected function getExpectedContentEncodingHeaderValue()
    {
        return 'deflate';
    }
}
