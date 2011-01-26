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
 * HTTP request method uses fsockopen function to retrieve the url.
 *
 * This would be the preferred method, but the fsockopen implementation has the most overhead of all
 * the HTTP transport implementations.
 *
 */
class org_wordpress_HttpClient_Fsockopen
{
    /**
     * Send a HTTP request to a URI using fsockopen().
     *
     * Does not support non-blocking mode.
     *
     * @param string $url  URI resource.
     * @param array  $args Optional. Override the defaults.
     *
     * @return array 'headers', 'body', 'cookies' and 'response' keys.
     */
    public function request($url, $args = array())
    {
        $defaults = array(
            'method' => 'GET', 'timeout' => 5,
            'redirection' => 5, 'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(), 'body' => null, 'cookies' => array()
        );

        $r = array_merge($defaults, $args);

        if (isset($r['headers']['User-Agent'])) {
            $r['user-agent'] = $r['headers']['User-Agent'];
            unset($r['headers']['User-Agent']);
        } else if (isset($r['headers']['user-agent'])) {
            $r['user-agent'] = $r['headers']['user-agent'];
            unset($r['headers']['user-agent']);
        }

        // Construct Cookie: header if any cookies are set
        org_wordpress_HttpClient::buildCookieHeader($r);

        $iError           = null; // Store error number
        $strError         = null; // Store error string
        $arrURL           = parse_url($url);
        $fsockopen_host   = $arrURL['host'];
        $secure_transport = false;

        if (! isset($arrURL['port'])) {
            if (($arrURL['scheme'] == 'ssl' || $arrURL['scheme'] == 'https') && extension_loaded('openssl')) {
                $fsockopen_host   = "ssl://$fsockopen_host";
                $arrURL['port']   = 443;
                $secure_transport = true;
            } else {
                $arrURL['port'] = 80;
            }
        }

        //fsockopen has issues with 'localhost' with IPv6 with certain versions of PHP, It attempts to connect to ::1,
        // which fails when the server is not set up for it. For compatibility, always connect to the IPv4 address.
        if ('localhost' == strtolower($fsockopen_host)) {
            $fsockopen_host = '127.0.0.1';
        }

        // There are issues with the HTTPS and SSL protocols that cause errors that can be safely
        // ignored and should be ignored.
        if (true === $secure_transport) {
            $error_reporting = error_reporting(0);
        }

        $handle = @fsockopen($fsockopen_host, $arrURL['port'], $iError, $strError, $r['timeout']);

        if (false === $handle) {
            throw new Exception($iError . ': ' . $strError);
        }

        $timeout  = (int) floor($r['timeout']);
        $utimeout = $timeout == $r['timeout'] ? 0 : 1000000 * $r['timeout'] % 1000000;
        stream_set_timeout($handle, $timeout, $utimeout);

        $requestPath = $arrURL['path'] . (isset($arrURL['query']) ? '?' . $arrURL['query'] : '');

        if (empty($requestPath)) {
            $requestPath .= '/';
        }

        $strHeaders  = strtoupper($r['method']) . ' ' . $requestPath . ' HTTP/' . $r['httpversion'] . "\r\n";
        $strHeaders .= 'Host: ' . $arrURL['host'] . "\r\n";

        if (isset($r['user-agent'])) {
            $strHeaders .= 'User-agent: ' . $r['user-agent'] . "\r\n";
        }

        if (is_array($r['headers'])) {
            foreach ((array) $r['headers'] as $header => $headerValue) {
                $strHeaders .= $header . ': ' . $headerValue . "\r\n";
            }
        } else {
            $strHeaders .= $r['headers'];
        }

        $strHeaders .= "\r\n";

        if (! is_null($r['body'])) {
            $strHeaders .= $r['body'];
        }

        fwrite($handle, $strHeaders);

        if (! $r['blocking']) {
            fclose($handle);
            return array('headers' => array(), 'body' => '', 'response' => array('code' => false, 'message' => false), 'cookies' => array());
        }

        $strResponse = '';
        while (! feof($handle)) {
            $strResponse .= fread($handle, 4096);
        }

        fclose($handle);

        if (true === $secure_transport) {
            error_reporting($error_reporting);
        }

        $process    = org_wordpress_HttpClient::processResponse($strResponse);
        $arrHeaders = org_wordpress_HttpClient::processHeaders($process['headers']);

        // Is the response code within the 400 range?
        if ((int) $arrHeaders['response']['code'] >= 400 && (int) $arrHeaders['response']['code'] < 500) {
            throw new Exception($arrHeaders['response']['code'] . ': ' . $arrHeaders['response']['message']);
        }

        // If location is found, then assume redirect and redirect to location.
        if ('HEAD' != $r['method'] && isset($arrHeaders['headers']['location'])) {
            if ($r['redirection']-- > 0) {
                return $this->request($arrHeaders['headers']['location'], $r);
            } else {
                throw new Exception('Too many redirects.');
            }
        }

        // If the body was chunk encoded, then decode it.
        if (! empty($process['body']) && isset($arrHeaders['headers']['transfer-encoding']) && 'chunked' == $arrHeaders['headers']['transfer-encoding']) {
            $process['body'] = org_wordpress_HttpClient::chunkTransferDecode($process['body']);
        }

        if (true === $r['decompress'] && true === org_wordpress_HttpClient_Encoding::should_decode($arrHeaders['headers'])) {
            $process['body'] = org_wordpress_HttpClient_Encoding::decompress($process['body']);
        }

        return array('headers' => $arrHeaders['headers'], 'body' => $process['body'], 'response' => $arrHeaders['response'], 'cookies' => $arrHeaders['cookies']);
    }

    /**
     * Whether this class can be used for retrieving an URL.
     *
     * @param array $args Optional arguments.
     *
     * @return boolean False means this class can not be used, true means it can.
     */
    public function test($args = array())
    {
        $is_ssl = isset($args['ssl']) && $args['ssl'];

        if (! $is_ssl && function_exists('fsockopen')) {
            $use = true;
        } elseif ($is_ssl && extension_loaded('openssl') && function_exists('fsockopen')) {
            $use = true;
        } else {
            $use = false;
        }

        return $use;
    }
}
