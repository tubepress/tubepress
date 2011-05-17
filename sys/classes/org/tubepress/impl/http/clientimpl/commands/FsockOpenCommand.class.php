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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../../impl/classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_impl_http_clientimpl_commands_AbstractHttpCommand',
    'org_tubepress_impl_http_HttpClientChain',
));

/**
 * Lifted from http://core.trac.wordpress.org/browser/tags/3.0.4/wp-includes/class-http.php
 *
 * HTTP request method uses fsockopen function to retrieve the url.
 *
 * This would be the preferred method, but the fsockopen implementation has the most overhead of all
 * the HTTP transport implementations.
 *
 */
class org_tubepress_impl_http_clientimpl_commands_FsockOpenCommand extends org_tubepress_impl_http_clientimpl_commands_AbstractHttpCommand
{
    /**
     * Send a HTTP request to a URI using fsockopen().
     *
     * Does not support non-blocking mode.
     *
     * @param string $url  URI resource.
     * @param array  $r    Optional. Override the defaults.
     *
     * @return array 'headers', 'body', 'cookies' and 'response' keys.
     */
    protected function _doExecute($url, $r)
    {
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

        $handle = @fsockopen($fsockopen_host, $arrURL['port'], $iError, $strError, $r[org_tubepress_impl_http_HttpClientChain::ARGS_TIMEOUT]);

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

        $strHeaders  = strtoupper($r[org_tubepress_impl_http_HttpClientChain::ARGS_METHOD]) . ' ' . $requestPath . ' HTTP/' . $r[org_tubepress_impl_http_HttpClientChain::ARGS_HTTP_VERSION] . "\r\n";
        $strHeaders .= 'Host: ' . $arrURL['host'] . "\r\n";

        if (isset($r[org_tubepress_impl_http_HttpClientChain::ARGS_USER_AGENT])) {
            $strHeaders .= org_tubepress_api_http_HttpClient::HTTP_HEADER_USER_AGENT . ': ' . $r[org_tubepress_impl_http_HttpClientChain::ARGS_USER_AGENT] . "\r\n";
        }

        if (is_array($r[org_tubepress_impl_http_HttpClientChain::ARGS_HEADERS])) {
            foreach ((array) $r[org_tubepress_impl_http_HttpClientChain::ARGS_HEADERS] as $header => $headerValue) {
                $strHeaders .= $header . ': ' . $headerValue . "\r\n";
            }
        } else {
            $strHeaders .= $r[org_tubepress_impl_http_HttpClientChain::ARGS_HEADERS];
        }

        $strHeaders .= "\r\n";

        if (! is_null($r[org_tubepress_impl_http_HttpClientChain::ARGS_BODY])) {
            $strHeaders .= $r[org_tubepress_impl_http_HttpClientChain::ARGS_BODY];
        }

        fwrite($handle, $strHeaders);

        $strResponse = '';
        while (! feof($handle)) {
            $strResponse .= fread($handle, 4096);
        }

        fclose($handle);

        if (true === $secure_transport) {
            error_reporting($error_reporting);
        }

        $process    = self::_breakRawStringResponseIntoHeaderAndBody($strResponse);
        $arrHeaders = self::_getProcessedHeaders($process['headers']);

        // If location is found, then assume redirect and redirect to location.
        if (isset($arrHeaders['headers']['location'])) {
            if ($this->_canRedirect()) {
                return $this->_doExecute($arrHeaders['headers']['location'], $r);
            } else {
                throw new Exception('Too many redirects.');
            }
        }

        // If the body was chunk encoded, then decode it.
        if (! empty($process['body']) && isset($arrHeaders['headers']['transfer-encoding']) && 'chunked' == $arrHeaders['headers']['transfer-encoding']) {
            $process['body'] = org_tubepress_impl_http_clientimpl_Encoding::chunkTransferDecode($process['body']);
        }

        if (true === $r[org_tubepress_impl_http_HttpClientChain::ARGS_DECOMPRESS] && true === org_tubepress_impl_http_clientimpl_Encoding::shouldDecode($arrHeaders['headers'])) {
            $process['body'] = org_tubepress_impl_http_clientimpl_Encoding::decompress($process['body']);
        }

        return array('headers' => $arrHeaders['headers'], 'body' => $process['body'], 'response' => $arrHeaders['response'], 'cookies' => $arrHeaders['cookies']);
    }

    protected function _canHandle($url, $args)
    {
        $isSsl = isset($args[org_tubepress_impl_http_HttpClientChain::ARGS_IS_SSL]) && $args[org_tubepress_impl_http_HttpClientChain::ARGS_IS_SSL];

        if (! $isSsl && function_exists('fsockopen')) {
            $use = true;
        } elseif ($isSsl && extension_loaded('openssl') && function_exists('fsockopen')) {
            $use = true;
        } else {
            $use = false;
        }

        return $use;
    }
}
