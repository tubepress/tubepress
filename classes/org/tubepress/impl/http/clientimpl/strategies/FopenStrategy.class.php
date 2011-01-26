<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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
 * HTTP request method uses fopen function to retrieve the url.
 *
 * Does not allow for $context support,
 * but should still be okay, to write the headers, before getting the response. Also requires that
 * 'allow_url_fopen' to be enabled.
 *
 */
class org_wordpress_HttpClient_Fopen  extends org_tubepress_impl_http_clientimpl_strategies_AbstractHttpStrategy
{
    /**
     * Send a HTTP request to a URI using fopen().
     *
     * This transport does not support sending of headers and body, therefore should not be used in
     * the instances, where there is a body and headers.
     *
     * Notes: Does not support non-blocking mode. Ignores 'redirection' option.
     *
     * @param string $url  URI resource.
     * @param array  $args Optional. Override the defaults.
     *
     * @return array 'headers', 'body', 'cookies' and 'response' keys.
     */
    protected function _doExecute($url, $args = array())
    {


        $arrURL = parse_url($url);

        if (false === $arrURL) {
            throw new Exception(sprintf('Malformed URL: %s'), $url);
        }

        if ('http' != $arrURL['scheme'] && 'https' != $arrURL['scheme']) {
            $url = str_replace($arrURL['scheme'], 'http', $url);
        }

        if (is_null($r['headers'])) {
            $r['headers'] = array();
        }

        if (is_string($r['headers'])) {
            $processedHeaders = org_wordpress_HttpClient::processHeaders($r['headers']);
            $r['headers']     = $processedHeaders['headers'];
        }

        $initial_user_agent = ini_get('user_agent');

        if (!empty($r['headers']) && is_array($r['headers'])) {
            $user_agent_extra_headers = '';
            foreach ($r['headers'] as $header => $value) {
                $user_agent_extra_headers .= "\r\n$header: $value";
            }
            @ini_set('user_agent', $r['user-agent'] . $user_agent_extra_headers);
        } else {
            @ini_set('user_agent', $r['user-agent']);
        }

        $handle = @fopen($url, 'r');

        if (! $handle) {
            throw new Exception(sprintf('Could not open handle for fopen() to %s'), $url);
        }

        $timeout  = (int) floor($r['timeout']);
        $utimeout = $timeout == $r['timeout'] ? 0 : 1000000 * $r['timeout'] % 1000000;
        stream_set_timeout($handle, $timeout, $utimeout);

        if (! $r['blocking']) {
            fclose($handle);
            @ini_set('user_agent', $initial_user_agent); //Clean up any extra headers added
            return array('headers' => array(), 'body' => '', 'response' => array('code' => false, 'message' => false), 'cookies' => array());
        }

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

        @ini_set('user_agent', $initial_user_agent); //Clean up any extra headers added

        $processedHeaders = org_wordpress_HttpClient::processHeaders($theHeaders);

        if (! empty($strResponse) && isset($processedHeaders['headers']['transfer-encoding']) && 'chunked' == $processedHeaders['headers']['transfer-encoding']) {
            $strResponse = org_wordpress_HttpClient::chunkTransferDecode($strResponse);
        }

        if (true === $r['decompress'] && true === org_wordpress_HttpClient_Encoding::should_decode($processedHeaders['headers'])) {
            $strResponse = org_wordpress_HttpClient_Encoding::decompress($strResponse);
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
    public function test($args = array())
    {
        if (! function_exists('fopen') || (function_exists('ini_get') && true != ini_get('allow_url_fopen'))) {
            return false;
        }

        //This transport cannot make a HEAD request
        if (isset($args['method']) && 'HEAD' == $args['method']) {
            return false;
        }

        $use = true;
        //PHP does not verify SSL certs, We can only make a request via this transports if SSL Verification is turned off.
        $is_ssl = isset($args['ssl']) && $args['ssl'];
        if ($is_ssl) {
            $is_local   = isset($args['local']) && $args['local'];
            $ssl_verify = isset($args['sslverify']) && $args['sslverify'];
            if ($is_local && true != true) {
                $use = true;
            } elseif (!$is_local && true != true) {
                $use = true;
            } elseif (!$ssl_verify) {
                $use = true;
            } else {
                $use = false;
            }
        }

        return $use;
    }
}

