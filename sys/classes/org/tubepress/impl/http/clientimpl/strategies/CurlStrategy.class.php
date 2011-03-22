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
 * HTTP request method uses Curl extension to retrieve the url.
 *
 * Requires the Curl extension to be installed.
 */
class org_tubepress_impl_http_clientimpl_strategies_CurlStrategy extends org_tubepress_impl_http_clientimpl_strategies_AbstractHttpStrategy
{
    /**
     * Send a HTTP request to a URI using cURL extension.
     *
     * @param string    $url The URL to handle.
     * @param str|array $r   Optional. Override the defaults.
     *
     * @return array 'headers', 'body', 'cookies' and 'response' keys.
     */
    protected function _doExecute($url, $r)
    {
        $handle    = curl_init();
        $sslVerify = isset($r[org_tubepress_impl_http_FastHttpClient::ARGS_SSL_VERIFY]) && $r[org_tubepress_impl_http_FastHttpClient::ARGS_SSL_VERIFY];

        // CURLOPT_TIMEOUT and CURLOPT_CONNECTTIMEOUT expect integers.  Have to use ceil since
        // a value of 0 will allow an ulimited timeout.
        $timeout = (int) ceil($r[org_tubepress_impl_http_FastHttpClient::ARGS_TIMEOUT]);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($handle, CURLOPT_TIMEOUT, $timeout);

        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, $sslVerify);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, $sslVerify);
        curl_setopt($handle, CURLOPT_USERAGENT, $r[org_tubepress_impl_http_FastHttpClient::ARGS_USER_AGENT]);
        curl_setopt($handle, CURLOPT_MAXREDIRS, 5);

        switch ($r[org_tubepress_impl_http_FastHttpClient::ARGS_METHOD]) {
        case 'POST':
            curl_setopt($handle, CURLOPT_POST, true);
            curl_setopt($handle, CURLOPT_POSTFIELDS, $r['body']);
            break;
        case 'PUT':
            curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($handle, CURLOPT_POSTFIELDS, $r['body']);
            break;
        }

        curl_setopt($handle, CURLOPT_HEADER, true);

        // The option doesn't work with safe mode or when open_basedir is set.
        // Disable HEAD when making HEAD requests.
        if (!ini_get('safe_mode') && !ini_get('open_basedir')) {
            curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
        }

        if (!empty($r[org_tubepress_impl_http_FastHttpClient::ARGS_HEADERS])) {
            // cURL expects full header strings in each element
            $headers = array();
            foreach ($r[org_tubepress_impl_http_FastHttpClient::ARGS_HEADERS] as $name => $value) {
                $headers[] = "{$name}: $value";
            }
            curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        }

        if ($r[org_tubepress_impl_http_FastHttpClient::ARGS_HTTP_VERSION] == '1.0') {
            curl_setopt($handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        } else {
            curl_setopt($handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        }

        $theResponse = curl_exec($handle);

        if (!empty($theResponse)) {

            $headerLength = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
            $theHeaders   = trim(substr($theResponse, 0, $headerLength));

            if (strlen($theResponse) > $headerLength) {
                $theBody = substr($theResponse, $headerLength);
            } else {
                $theBody = '';
            }

            if (false !== strrpos($theHeaders, "\r\n\r\n")) {
                $headerParts = explode("\r\n\r\n", $theHeaders);
                $theHeaders  = $headerParts[ count($headerParts) -1 ];
            }
            $theHeaders = self::_getProcessedHeaders($theHeaders);

        } else {
            if ($curlError = curl_error($handle)) {
                throw new Exception($curlError);
            }
            if (in_array(curl_getinfo($handle, CURLINFO_HTTP_CODE), array(301, 302))) {
                throw new Exception('Too many redirects.');
            }

            $theHeaders = array('headers' => array(), 'cookies' => array());
            $theBody    = '';
        }

        $response            = array();
        $response['code']    = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $response['message'] = $response['code'];

        curl_close($handle);

        if (true === $r[org_tubepress_impl_http_FastHttpClient::ARGS_DECOMPRESS] && true === org_tubepress_impl_http_clientimpl_Encoding::shouldDecode($theHeaders['headers'])) {
            $theBody = org_tubepress_impl_http_clientimpl_Encoding::decompress($theBody);
        }

        return array('headers' => $theHeaders['headers'], 'body' => $theBody, 'response' => $response, 'cookies' => $theHeaders['cookies']);
    }

    /**
     * Returns true if this strategy is able to handle
     *  the request.
     *
     * @return boolean True if the strategy can handle the request, false otherwise.
     */
    function canHandle()
    {
        return function_exists('curl_init') && function_exists('curl_exec');
    }
}
