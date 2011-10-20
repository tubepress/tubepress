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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_http_HttpResponse',
    'org_tubepress_spi_http_HttpChunkedTransferMessageDecoder',
));

/**
 * Largely based on http://core.trac.wordpress.org/browser/tags/3.0.4/wp-includes/class-http.php
 *
 * Decodes messages that are Transfer-Encoding: chunked
 *
 */
class org_tubepress_impl_http_DefaultHttpChunkedTransferMessageDecoder implements org_tubepress_spi_http_HttpChunkedTransferMessageDecoder
{
	private static $_logPrefix = 'HTTP Chunked-Transfer Message Decoder';

    private static $_chunkedName = 'chunked';

	/**
	 * Determines whether or not response contains a body that is chunk-transfer encoded.
	 *
	 * @param org_tubepress_api_http_HttpResponse $response The response to examine.
	 *
	 * @return bool True if this response contains a body that is chunk-transfer encoded. False otherwise.
	 */
	function containsChunkedData(org_tubepress_api_http_HttpResponse $response)
	{
        $entity = $response->getEntity();

        if ($entity === null) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Response contains no entity');
            return false;
        }

        $content = $entity->getContent();

        if ($content == '' || $content == null) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Response entity contains no content');
            return false;
        }

        $encoding = $response->getHeaderValue(org_tubepress_api_http_HttpResponse::HTTP_HEADER_TRANSFER_ENCODING);

        if ($encoding === null) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Response is missing Content-Encoding header');
            return false;
        }

        org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Response contains content encoded as "%s"', $encoding);

        $isChunkedEncoded = strcasecmp($encoding, self::$_chunkedName) === 0;

        org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Response %s contain chunked-transfer encoded data', $isChunkedEncoded ? 'does' : 'does not');

        return $isChunkedEncoded;
	}

    /**
     * Decodes chunked-transfer encoded data.
     *
     * @param string $body The chunked-transfer body.
     *
     * @return string The decoded data.
     */
	//http://tools.ietf.org/html/rfc2616#section-19.4.6
    function dechunk($body)
    {
        $body = str_replace(array("\r\n", "\r"), "\n", $body);

        /* first grab the initial chunk length */
        $result = preg_match('/^\s*([0-9a-fA-F]+)[^\n]*\n/m', $body, $matches);

        if ($result === false || count($matches) !== 2) {

            throw new Exception('Data does not appear to be chunked (missing initial chunk length)');
        }

        /* set initial values */
        $offset      = strlen($matches[0]);
        $chunkLength = hexdec($matches[1]);
        $decoded     = '';
        $bodyLength  = strlen($body);

        while ($chunkLength > 0) {

            /* read in the first chunk data */
            $decoded .= substr($body, $offset, $chunkLength);

            /* increment the offset to what we just read */
            $offset  += $chunkLength;

            /* whoa nelly, we've hit the end of the road. */
            if ($offset >= $bodyLength) {

                return $decoded;
            }

            /* grab the next chunk length */
            $result = preg_match('/\n\s*([0-9a-fA-F]+)[^\n]*\n/m', $body, $matches, null, $offset);

            if ($result === false || count($matches) !== 2) {

                return $decoded;
                //throw new Exception('Data does not appear to be chunked (missing chunk length)');
            }

            /* increment the offset to start of next data */
            $offset     += strlen($matches[0]);

            /* set up how much data we want to read */
            $chunkLength = hexdec($matches[1]);
        }

        return $decoded;
    }
}

