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
 * HTTP request method uses Streams to retrieve the url.
 *
 * Requires PHP 5.0+ and uses fopen with stream context. Requires that 'allow_url_fopen' PHP setting
 * to be enabled.
 *
 * Second preferred method for getting the URL, for PHP 5.
 */
class org_tubepress_impl_http_clientimpl_commands_StreamsCommand extends org_tubepress_impl_http_clientimpl_commands_AbstractHttpCommand
{
    /**
     * Send a HTTP request to a URI using streams with fopen().
     *
     * @param string $url
     * @param array  $args Optional. Override the defaults.
     *
     * @return array 'headers', 'body', 'cookies' and 'response' keys.
     */
    public function _doExecute($url, $r)
    {
        $arrURL           = parse_url($url);
        if ('http' != $arrURL['scheme'] && 'https' != $arrURL['scheme']) {
            $url = preg_replace('|^' . preg_quote($arrURL['scheme'], '|') . '|', 'http', $url);
        }

        // Convert Header array to string.
        $strHeaders = '';
        if (is_array($r[org_tubepress_impl_http_HttpClientChain::ARGS_HEADERS])) {
            foreach ($r[org_tubepress_impl_http_HttpClientChain::ARGS_HEADERS] as $name => $value) {
                $strHeaders .= "{$name}: $value\r\n";
            }
        } else if (is_string($r[org_tubepress_impl_http_HttpClientChain::ARGS_HEADERS])) {
            $strHeaders = $r[org_tubepress_impl_http_HttpClientChain::ARGS_HEADERS];
        }

        $is_local   = isset($args['local']) && $args['local'];
        $ssl_verify = isset($args[org_tubepress_impl_http_HttpClientChain::ARGS_SSL_VERIFY]) && $args[org_tubepress_impl_http_HttpClientChain::ARGS_SSL_VERIFY];

        if ($is_local) {
            $ssl_verify = $ssl_verify;
        } elseif (! $is_local) {
            $ssl_verify = $ssl_verify;
        }

        $arrContext = array('http' =>
            array(
                'method' => strtoupper($r[org_tubepress_impl_http_HttpClientChain::ARGS_METHOD]),
                'user_agent' => $r[org_tubepress_impl_http_HttpClientChain::ARGS_USER_AGENT],
                'max_redirects' => 6, // See #11557
                'protocol_version' => (float) $r[org_tubepress_impl_http_HttpClientChain::ARGS_HTTP_VERSION],
                'header' => $strHeaders,
                'ignore_errors' => true, // Return non-200 requests.
                'timeout' => $r[org_tubepress_impl_http_HttpClientChain::ARGS_TIMEOUT],
                'ssl' => array(
                        'verify_peer' => $ssl_verify,
                        'verify_host' => $ssl_verify
               )
           )
        );

        if (! empty($r[org_tubepress_impl_http_HttpClientChain::ARGS_BODY])) {
            $arrContext['http']['content'] = $r[org_tubepress_impl_http_HttpClientChain::ARGS_BODY];
        }

        $context = stream_context_create($arrContext);

        $handle = @fopen($url, 'r', false, $context);

        if (! $handle) {
            throw new Exception(sprintf('Could not open handle for fopen() to %s'), $url);
        }

        $timeout  = (int) floor($r[org_tubepress_impl_http_HttpClientChain::ARGS_TIMEOUT]);
        $utimeout = $timeout == $r[org_tubepress_impl_http_HttpClientChain::ARGS_TIMEOUT] ? 0 : 1000000 * $r['timeout'] % 1000000;
        stream_set_timeout($handle, $timeout, $utimeout);

        $strResponse = stream_get_contents($handle);
        $meta        = stream_get_meta_data($handle);

        fclose($handle);

        $processedHeaders = array();
        if (isset($meta['wrapper_data']['headers'])) {
            $processedHeaders = self::_getProcessedHeaders($meta['wrapper_data']['headers']);
        } else {
            $processedHeaders = self::_getProcessedHeaders($meta['wrapper_data']);
        }

        if (! empty($strResponse) && isset($processedHeaders['headers']['transfer-encoding']) && 'chunked' == $processedHeaders['headers']['transfer-encoding']) {
            $strResponse = org_tubepress_impl_http_clientimpl_Encoding::chunkTransferDecode($strResponse);
        }

        if (true === $r[org_tubepress_impl_http_HttpClientChain::ARGS_DECOMPRESS] && true === org_tubepress_impl_http_clientimpl_Encoding::shoulddecode($processedHeaders['headers'])) {
            $strResponse = org_tubepress_impl_http_clientimpl_Encoding::decompress($strResponse);
        }

        return array('headers' => $processedHeaders['headers'], 'body' => $strResponse, 'response' => $processedHeaders['response'], 'cookies' => $processedHeaders['cookies']);
    }

    protected function _canHandle($url, $args)
    {
        if (! function_exists('fopen') || (function_exists('ini_get') && true != ini_get('allow_url_fopen'))) {
            return false;
        }

        return true;
    }
}
