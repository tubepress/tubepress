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
 * Deflates data according to RFC 1952. Simulation instead of native.
 */
class org_tubepress_impl_http_contentencoding_SimulatedGzipDecompressor extends org_tubepress_impl_http_contentencoding_AbstractDecompressorCommand
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
        $decompressed = $this->_gzdecode($compressed);

        if ($decompressed === false) {

            throw new Exception('Could not decompress data with simulated gzdecode()');
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
        return 'Simulated gzip';
    }

    /**
     * Determines if this compression is available on the host system.
     *
     * @return boolean True if compression is available on the host system, false otherwise.
     */
    protected function isAvailiable()
    {
        return true;
    }

    /**
     * Get the Content-Encoding header value that this command can handle.
     *
     * @return string The Content-Encoding header value that this command can handle.
     */
    protected function getExpectedContentEncodingHeaderValue()
    {
        return 'gzip';
    }

    /**
     * http://us2.php.net/manual/en/function.gzdecode.php#82930
     */
    private function _gzdecode($message)
    {
        $messageLength = strlen($message);

        /* make sure this is actually gzipped */
        if ($messageLength < 18 || strcmp(substr($message, 0, 2), "\x1f\x8b")) {

            org_tubepress_impl_log_Log::log($this->logPrefix(), 'Not in GZIP format.');
            return false;
        }

        /* grab the compression method and flags */
        $compressionMethod = ord(substr($message, 2, 1));
        $compressionFlags  = ord(substr($message, 3, 1));

        if ($compressionFlags & 31 != $compressionFlags) {

            org_tubepress_impl_log_Log::log($this->logPrefix(), 'Reserved bits not allowed.');
            return false;
        }

        /* NOTE: $mtime may be negative (PHP integer limitations) */
        $mtime        = unpack('V', substr($message, 4, 4));
        $mtime        = $mtime[1];
        $xfl          = substr($message, 8, 1);
        $os           = substr($message, 8, 1);
        $headerLength = 10;
        $extraLength  = 0;
        $extra        = '';

        if ($compressionFlags & 4) {

            /* 2-byte length prefixed EXTRA data in header */
            if ($messageLength - $headerLength - 2 < 8) {

                org_tubepress_impl_log_Log::log($this->logPrefix(), '2-byte length prefixed EXTRA data in header');
                return false;
            }

            $extraLength = unpack('v', substr($message, 8, 2));
            $extraLength = $extraLength[1];

            if ($messageLength - $headerLength - 2 - $extraLength < 8) {

                org_tubepress_impl_log_Log::log($this->logPrefix(), 'Invalid extra length');
                return false;
            }

            $extra         = substr($message, 10, $extraLength);
            $headerLength += 2 + $extraLength;
        }

        $filenameLength = 0;
        $filename       = '';

        /* If FNAME is set, an original file name is present, terminated by a zero byte. */
        if ($compressionFlags & 8) {

            if ($messageLength - $headerLength - 1 < 8) {

                org_tubepress_impl_log_Log::log($this->logPrefix(), 'C-Style string');
                return false;
            }

            $filenameLength = strpos(substr($message, $headerLength), chr(0));

            if ($filenameLength === false || $messageLength - $headerLength - $filenameLength - 1 < 8) {

                org_tubepress_impl_log_Log::log($this->logPrefix(), 'Invalid filename length');
                return false;
            }

            $filename      = substr($message, $headerLength, $filenameLength);
            $headerLength += $filenameLength + 1;
        }

        $commentlen = 0;
        $comment    = '';

        /* If FCOMMENT is set, a zero-terminated file comment is present */
        if ($compressionFlags & 16) {

            if ($messageLength - $headerLength - 1 < 8) {

                org_tubepress_impl_log_Log::log($this->logPrefix(), 'C-Style string COMMENT data in header');
                return false;
            }

            $commentlen = strpos(substr($message, $headerLength), chr(0));

            if ($commentlen === false || $messageLength - $headerLength - $commentlen - 1 < 8) {

                org_tubepress_impl_log_Log::log($this->logPrefix(), 'Invalid comment length');
                return false;
            }

            $comment       = substr($message, $headerLength, $commentlen);
            $headerLength += $commentlen + 1;
        }

        $headercrc = '';

        /* If FHCRC is set, a CRC16 for the gzip header is present, immediately before the compressed data */
        if ($compressionFlags & 2) {

            if ($messageLength - $headerLength - 2 < 8) {

                org_tubepress_impl_log_Log::log($this->logPrefix(), '2-bytes (lowest order) of CRC32 on header present');
                return false;
            }

            $calccrc   = crc32(substr($message, 0, $headerLength)) & 0xffff;
            $headercrc = unpack('v', substr($message, $headerLength, 2));
            $headercrc = $headercrc[1];

            if ($headercrc != $calccrc) {

                org_tubepress_impl_log_Log::log($this->logPrefix(), 'Header checksum failed.');
            }

            $headerLength += 2;
        }

        $datacrc = unpack('V', substr($message, -8, 4));
        $datacrc = sprintf('%u', $datacrc[1] & 0xFFFFFFFF);
        $isize   = unpack('V', substr($message, -4));
        $isize   = $isize[1];

        $bodyLength = $messageLength - $headerLength - 8;

        if ($bodyLength < 1) {

            org_tubepress_impl_log_Log::log($this->logPrefix(), 'Negative body length');
            return false;
        }

        $compressedBody = substr($message, $headerLength, $bodyLength);
        $decompressed   = '';

        if ($bodyLength > 0) {

            switch ($compressionMethod) {

            case 8:
                // Currently the only supported compression method:
                $decompressed = gzinflate($compressedBody, null);
                break;

            default:
                org_tubepress_impl_log_Log::log($this->logPrefix(), 'Unknown compression method.');
                return false;
            }
        }

        $crc   = sprintf("%u", crc32($decompressed));
        $crcOK = $crc == $datacrc;
        $lenOK = $isize == strlen($decompressed);

        if (!$lenOK || !$crcOK) {

            org_tubepress_impl_log_Log::log($this->logPrefix(), $lenOK ? '' : 'Length check FAILED. ') . ( $crcOK ? '' : 'Checksum FAILED.');
            return false;
        }

        return $decompressed;
    }
}
