<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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

/**
 * Lifted from http://core.trac.wordpress.org/browser/tags/3.0.4/wp-includes/class-http.php
 *
 * Implementation for deflate and gzip transfer encodings.
 *
 * Includes RFC 1950, RFC 1951, and RFC 1952.
 */
class org_tubepress_impl_http_clientimpl_Encoding
{
    /**
     * Compress raw string using the deflate format.
     *
     * Supports the RFC 1951 standard.
     *
     * @param string $raw      String to compress.
     * @param int    $level    Optional, default is 9. Compression level, 9 is highest.
     * @param string $supports Optional, not used. When implemented it will choose the right compression based on what the server supports.
     *
     * @return string|bool False on failure.
     */
    public static function compress($raw, $level = 9, $supports = null)
    {
        return gzdeflate($raw, $level);
    }

    /**
     * Decompression of deflated string.
     *
     * Will attempt to decompress using the RFC 1950 standard, and if that fails
     * then the RFC 1951 standard deflate will be attempted. Finally, the RFC
     * 1952 standard gzip decode will be attempted. If all fail, then the
     * original compressed string will be returned.
     *
     * @param string $compressed String to decompress.
     * @param int    $length     The optional length of the compressed data.
     *
     * @return string|bool False on failure.
     */
    public static function decompress($compressed, $length = null)
    {
        if (empty($compressed)) {
            return $compressed;
        }

        if (false !== ($decompressed = @gzinflate($compressed))) {
            return $decompressed;
        }

        if (false !== ($decompressed = self::simulatedGzInflate($compressed))) {
            return $decompressed;
        }

        if (false !== ($decompressed = @gzuncompress($compressed))) {
            return $decompressed;
        }

        if (function_exists('gzdecode')) {
            $decompressed = @gzdecode($compressed);

            if (false !== $decompressed) {
                return $decompressed;
            }
        }

        return $compressed;
    }

    /**
     * Decompression of deflated string while staying compatible with the majority of servers.
     *
     * Certain Servers will return deflated data with headers which PHP's gziniflate()
     * function cannot handle out of the box. The following function lifted from
     * http://au2.php.net/manual/en/function.gzinflate.php#77336 will attempt to deflate
     * the various return forms used.
     *
     * See http://au2.php.net/manual/en/function.gzinflate.php#77336
     *
     * @param string $gzData String to decompress.
     *
     * @return string|bool False on failure.
     */
    public static function simulatedGzInflate($gzData)
    {
        if (substr($gzData, 0, 3) == "\x1f\x8b\x08") {
            $i   = 10;
            $flg = ord(substr($gzData, 3, 1));
            if ($flg > 0) {
                if ($flg & 4) {
                    list($xlen) = unpack('v', substr($gzData, $i, 2));
                    $i          = $i + 2 + $xlen;
                }
                if ($flg & 8) {
                    $i = strpos($gzData, "\0", $i) + 1;
                }
                if ($flg & 16) {
                    $i = strpos($gzData, "\0", $i) + 1;
                }
                if ($flg & 2) {
                    $i = $i + 2;
                }
            }
            return gzinflate(substr($gzData, $i, -8));
        } else {
            return false;
        }
    }

    /**
     * What encoding types to accept and their priority values.
     *
     * @return string Types of encoding to accept.
     */
    public static function getAcceptEncodingString()
    {
        $type = array();
        if (function_exists('gzinflate')) {
            $type[] = 'deflate;q=1.0';
        }

        if (function_exists('gzuncompress')) {
            $type[] = 'compress;q=0.5';
        }

        if (function_exists('gzdecode')) {
            $type[] = 'gzip;q=0.5';
        }

        return implode(', ', $type);
    }

    /**
     * What enconding the content used when it was compressed to send in the headers.
     *
     * @return string Content-Encoding string to send in the header.
     */
    public static function getContentEncodingString()
    {
        return 'deflate';
    }

    /**
     * Whether the content be decoded based on the headers.
     *
     * @param array|string $headers All of the available headers.
     *
     * @return bool
     */
    public static function shouldDecode($headers)
    {
        if (is_array($headers)) {
            if (array_key_exists('content-encoding', $headers) && ! empty($headers['content-encoding'])) {
                return true;
            }
        } else if (is_string($headers)) {
            return (stripos($headers, 'content-encoding:') !== false);
        }

        return false;
    }

    /**
     * Whether decompression and compression are supported by the PHP version.
     *
     * Each function is tested instead of checking for the zlib extension, to
     * ensure that the functions all exist in the PHP version and aren't
     * disabled.
     *
     * @return bool
     */
    public static function isCompressionAvailable()
    {
        return (function_exists('gzuncompress') || function_exists('gzdeflate') || function_exists('gzinflate'));
    }

        /**
     * Decodes chunk transfer-encoding, based off the HTTP 1.1 specification.
     *
     * Based off the HTTP http_encoding_dechunk function. Does not support UTF-8. Does not support
     * returning footer headers. Shouldn't be too difficult to support it though.
     *
     * @param string $body Body content
     *
     * @return string Chunked decoded body on success or raw body on failure.
     */
    public static function chunkTransferDecode($body)
    {
        $body = str_replace(array("\r\n", "\r"), "\n", $body);

        // The body is not chunked encoding or is malformed.
        if (! preg_match('/^[0-9a-f]+(\s|\n)+/mi', trim($body))) {
            return $body;
        }

        $parsedBody = '';

        while (true) {
            $hasChunk = (bool) preg_match('/^([0-9a-f]+)(\s|\n)+/mi', $body, $match);

            if (!$hasChunk || empty($match[1])) {
                return $body;
            }

            $length      = hexdec($match[1]);
            $chunkLength = strlen($match[0]);
            $strBody     = substr($body, $chunkLength, $length);
            $parsedBody .= $strBody;
            $body        = ltrim(str_replace(array($match[0], $strBody), '', $body), "\n");

            if ("0" == trim($body)) {
                return $parsedBody; // Ignore footer headers.
            }
        }
    }
}
