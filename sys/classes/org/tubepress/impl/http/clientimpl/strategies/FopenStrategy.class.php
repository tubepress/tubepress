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

function_exists('tubepress_load_classes')
    || require dirname(__FILE__) . '/../../../../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_impl_http_clientimpl_strategies_AbstractHttpStrategy',
    'org_tubepress_impl_http_FastHttpClient'));

/**
 * Lifted from http://core.trac.wordpress.org/browser/tags/3.0.4/wp-includes/class-http.php
 *
 * HTTP request method uses fopen function to retrieve the url.
 *
 * Does not allow for $context support,
 * but should still be okay, to write the headers, before getting the response. Also requires that
 * 'allow_url_fopen' to be enabled.
 *
 */
class org_tubepress_impl_http_clientimpl_strategies_FopenStrategy  extends org_tubepress_impl_http_clientimpl_strategies_AbstractHttpStrategy
{
    const INI_USER_AGENT = 'user_agent';

    /**
     * Send a HTTP request to a URI using fopen().
     *
     * This transport does not support sending of headers and body, therefore should not be used in
     * the instances, where there is a body and headers.
     *
     * @param string $url  URI resource.
     * @param array  $args Optional. Override the defaults.
     *
     * @return array 'headers', 'body', 'cookies' and 'response' keys.
     */
    protected function _doExecute($url, $r)
    {
        $arrURL = parse_url($url);

        if ('http' != $arrURL['scheme'] && 'https' != $arrURL['scheme']) {
            $url = str_replace($arrURL['scheme'], 'http', $url);
        }

        $initialUserAgent = ini_get(self::INI_USER_AGENT);

        if (!empty($r[org_tubepress_impl_http_FastHttpClient::ARGS_HEADERS]) && is_array($r[org_tubepress_impl_http_FastHttpClient::ARGS_HEADERS])) {
            $user_agent_extra_headers = '';
            foreach ($r[org_tubepress_impl_http_FastHttpClient::ARGS_HEADERS] as $header => $value) {
                $user_agent_extra_headers .= "\r\n$header: $value";
            }
            @ini_set(self::INI_USER_AGENT, $r[org_tubepress_impl_http_FastHttpClient::ARGS_HEADERS] . $user_agent_extra_headers);
        } else {
            @ini_set(self::INI_USER_AGENT, $r[org_tubepress_impl_http_FastHttpClient::ARGS_HEADERS]);
        }

        $handle = @fopen($url, 'r');

        if (! $handle) {
            throw new Exception(sprintf('Could not open handle for fopen() to %s'), $url);
        }

        $timeout  = (int) floor($r[org_tubepress_impl_http_FastHttpClient::ARGS_TIMEOUT]);
        $utimeout = $timeout == $r[org_tubepress_impl_http_FastHttpClient::ARGS_TIMEOUT] ? 0 : 1000000 * $r['timeout'] % 1000000;
        stream_set_timeout($handle, $timeout, $utimeout);

        $strResponse = '';
        while (! feof($handle)) {
            $strResponse .= fread($handle, 4096);
        }

        if (function_exists('stream_get_meta_data')) {
            $meta = stream_get_meta_data($handle);

            $theHeaders = $meta['wrapper_data'];
            if (isset($meta['wrapper_data']['headers'])) {
                $theHeaders = $meta['wrapper_data']['headers'];
            }
        } else {
            //$http_response_header is a PHP reserved variable which is set in the current-scope when using the HTTP Wrapper
            //see http://php.oregonstate.edu/manual/en/reserved.variables.httpresponseheader.php
            $theHeaders = $http_response_header;
        }

        fclose($handle);

        @ini_set(self::INI_USER_AGENT, $initialUserAgent); //Clean up any extra headers added

        $processedHeaders = self::_getProcessedHeaders($theHeaders);

        if (! empty($strResponse) && isset($processedHeaders['headers']['transfer-encoding']) && 'chunked' == $processedHeaders['headers']['transfer-encoding']) {
            $strResponse = org_tubepress_impl_http_clientimpl_Encoding::chunkTransferDecode($strResponse);
        }

        if (true === $r['decompress'] && true === org_tubepress_impl_http_clientimpl_Encoding::shouldDecode($processedHeaders['headers'])) {
            $strResponse = org_tubepress_impl_http_clientimpl_Encoding::decompress($strResponse);
        }

        return array('headers' => $processedHeaders['headers'], 'body' => $strResponse, 'response' => $processedHeaders['response'], 'cookies' => $processedHeaders['cookies']);
    }

    /**
     * Whether this class can be used for retrieving an URL.
     *
     * @param array $args The optional args
     *
     * @return boolean False means this class can not be used, true means it can.
     */
    public function canHandle()
    {
        $args = func_get_args();
        $url  = $args[0];
        $r    = $args[1];

        if (! function_exists('fopen') || (function_exists('ini_get') && true != ini_get('allow_url_fopen'))) {
            return false;
        }

        $use = true;

        //PHP does not verify SSL certs, We can only make a request via this transports if SSL Verification is turned off.
        $isSsl = isset($args[org_tubepress_impl_http_FastHttpClient::ARGS_IS_SSL]) && $args[org_tubepress_impl_http_FastHttpClient::ARGS_IS_SSL];

        if ($isSsl) {
            $isLocal   = isset($args['local']) && $args['local'];
            $sslVerify = isset($args[org_tubepress_impl_http_FastHttpClient::ARGS_SSL_VERIFY]) && $args[org_tubepress_impl_http_FastHttpClient::ARGS_SSL_VERIFY];
            if ($isLocal && true != true) {
                $use = true;
            } elseif (!$isLocal && true != true) {
                $use = true;
            } elseif (!$sslVerify) {
                $use = true;
            } else {
                $use = false;
            }
        }

        return $use;
    }
}

