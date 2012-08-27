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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_http_HttpResponse',
    'org_tubepress_impl_http_AbstractDecoderChain',
    'org_tubepress_spi_http_HttpContentDecoder',
));

/**
 * Decodes Content-Encoded HTTP messages using chain-of-responsibility.
 */
class org_tubepress_impl_http_HttpContentDecoderChain extends org_tubepress_impl_http_AbstractDecoderChain implements org_tubepress_spi_http_HttpContentDecoder
{
    protected function getArrayOfCommandNames()
    {
        return array(
            'org_tubepress_impl_http_contentencoding_NativeGzipDecompressor',
            'org_tubepress_impl_http_contentencoding_SimulatedGzipDecompressor',
            'org_tubepress_impl_http_contentencoding_NativeDeflateRfc1950Decompressor',
            'org_tubepress_impl_http_contentencoding_NativeRfc1951Decompressor',
        );
    }

    protected function getLogPrefix()
    {
        return 'HTTP Content Decoder Chain';
    }

    protected function getHeaderName()
    {
        return org_tubepress_api_http_HttpResponse::HTTP_HEADER_CONTENT_ENCODING;
    }

    /**
     * Get the Accept-Encoding header value to send with HTTP requests.
     *
     * @return string the Accept-Encoding header value to send with HTTP requests. May be null.
     */
    function getAcceptEncodingHeaderValue()
    {
        /* we can always handle gzip */
        $toReturn = 'gzip';

        /* we can sometimes do deflate... */
        if (function_exists('gzuncompress') || function_exists('gzinflate')) {

            $toReturn .= ';q=1.0, deflate;q=0.5';
        }

        return $toReturn;
    }
}

