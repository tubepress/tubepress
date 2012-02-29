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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_spi_patterns_cor_Command',
));

/**
 * Decodes messages that are Transfer-Encoding: chunked
 */
class org_tubepress_impl_http_transferencoding_ChunkedTransferDecoder implements org_tubepress_spi_patterns_cor_Command
{
    private static $_logPrefix = 'HTTP Chunked-Transfer Message Decoder';

    /**
    * Execute the command.
    *
    * @param array $context An array of context elements (may be empty).
    *
    * @return boolean True if this command was able to handle the execution. False otherwise.
    */
    function execute($context)
    {
        $response = $context->response;
        $encoding = $response->getHeaderValue(org_tubepress_api_http_HttpResponse::HTTP_HEADER_TRANSFER_ENCODING);

        if (strcasecmp($encoding, 'chunked') !== 0) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Response is not encoded with Chunked-Transfer');
            return false;
        }

        $context->decoded = self::_decode($response->getEntity()->getContent());

        return true;
    }

    private static function _decode($body)
    {
        /* http://tools.ietf.org/html/rfc2616#section-19.4.6 */

        /* first grab the initial chunk length */
        $chunkLengthPregMatchResult = preg_match('/^\s*([0-9a-fA-F]+)(?:(?!\r\n).)*\r\n/sm', $body, $chunkLengthMatches);

        if ($chunkLengthPregMatchResult === false || count($chunkLengthMatches) !== 2) {

            throw new Exception('Data does not appear to be chunked (missing initial chunk length)');
        }

        /* set initial values */
        $currentOffsetIntoBody = strlen($chunkLengthMatches[0]);
        $currentChunkLength    = hexdec($chunkLengthMatches[1]);
        $decoded               = '';
        $bodyLength            = strlen($body);

        while ($currentChunkLength > 0) {

            /* read in the first chunk data */
            $decoded .= substr($body, $currentOffsetIntoBody, $currentChunkLength);

            /* increment the offset to what we just read */
            $currentOffsetIntoBody += $currentChunkLength;

            /* whoa nelly, we've hit the end of the road. */
            if ($currentOffsetIntoBody >= $bodyLength) {

                return $decoded;
            }

            /* grab the next chunk length */
            $chunkLengthPregMatchResult = preg_match('/\r\n\s*([0-9a-fA-F]+)(?:(?!\r\n).)*\r\n/sm', $body, $chunkLengthMatches, null, $currentOffsetIntoBody);

            if ($chunkLengthPregMatchResult === false || count($chunkLengthMatches) !== 2) {

                return $decoded;
            }

            /* increment the offset to start of next data */
            $currentOffsetIntoBody += strlen($chunkLengthMatches[0]);

            /* set up how much data we want to read */
            $currentChunkLength = hexdec($chunkLengthMatches[1]);
        }

        return $decoded;
    }
}

