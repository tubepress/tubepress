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
 * HTTP request method uses HTTP extension to retrieve the url.
 *
 * Requires the HTTP extension to be installed. This would be the preferred transport since it can
 * handle a lot of the problems that forces the others to use the HTTP version 1.0. Even if PHP 5.2+
 * is being used, it doesn't mean that the HTTP extension will be enabled.
 *
 */
class org_tubepress_impl_http_clientimpl_commands_ExtHttpCommand extends org_tubepress_impl_http_clientimpl_commands_AbstractHttpCommand
{
    /**
     * Send a HTTP request to a URI using HTTP extension.
     *
     * Does not support non-blocking.
     *
     * @param string    $url  The URL to handle.
     * @param str|array $args Optional. Override the defaults.
     *
     * @return array 'headers', 'body', 'cookies' and 'response' keys.
     */
    protected function _doExecute($url, $args)
    {
        switch ($args[org_tubepress_impl_http_HttpClientChain::ARGS_METHOD]) {
        case org_tubepress_api_http_HttpClient::HTTP_METHOD_POST:
            $args[org_tubepress_impl_http_HttpClientChain::ARGS_METHOD] = HTTP_METH_POST;
            break;
        case org_tubepress_api_http_HttpClient::HTTP_METHOD_PUT:
            $args[org_tubepress_impl_http_HttpClientChain::ARGS_METHOD] =  HTTP_METH_PUT;
            break;
        case org_tubepress_api_http_HttpClient::HTTP_METHOD_GET:
        default:
            $args[org_tubepress_impl_http_HttpClientChain::ARGS_METHOD] = HTTP_METH_GET;
        }

        $urlAsArray = parse_url($url);

        if ('http' != $urlAsArray['scheme'] && 'https' != $urlAsArray['scheme']) {
            $url = preg_replace('|^' . preg_quote($urlAsArray['scheme'], '|') . '|', 'http', $url);
        }

        $sslVerify = isset($args[org_tubepress_impl_http_HttpClientChain::ARGS_SSL_VERIFY]) && $args[org_tubepress_impl_http_HttpClientChain::ARGS_SSL_VERIFY];

        $args[org_tubepress_impl_http_HttpClientChain::ARGS_TIMEOUT] = (int) ceil($args[org_tubepress_impl_http_HttpClientChain::ARGS_TIMEOUT]);

        $options = array(
            'timeout'        => $args[org_tubepress_impl_http_HttpClientChain::ARGS_TIMEOUT],
            'connecttimeout' => $args[org_tubepress_impl_http_HttpClientChain::ARGS_TIMEOUT],
            'redirect'       => 5,
            'useragent'      => $args[org_tubepress_impl_http_HttpClientChain::ARGS_USER_AGENT],
            'headers'        => $args[org_tubepress_impl_http_HttpClientChain::ARGS_HEADERS],
            'ssl'            => array(
                'verifypeer' => $sslVerify,
                'verifyhost' => $sslVerify
            )
        );

        $strResponse = @http_request($args[org_tubepress_impl_http_HttpClientChain::ARGS_METHOD], $url, $args[org_tubepress_impl_http_HttpClientChain::ARGS_BODY], $options, $info);

        // Error may still be set, Response may return headers or partial document, and error
        // contains a reason the request was aborted, eg, timeout expired or max-redirects reached.
        if (false === $strResponse || ! empty($info['error'])) {
            throw new Exception($info['response_code'] . ': ' . $info['error']);
        }

        $headersBody = self::_breakRawStringResponseIntoHeaderAndBody($strResponse);
        $theHeaders   = $headersBody[org_tubepress_impl_http_HttpClientChain::ARGS_HEADERS];
        $theBody      = $headersBody[org_tubepress_impl_http_HttpClientChain::ARGS_BODY];

        unset($headersBody);

        $theHeaders = self::_getProcessedHeaders($theHeaders);

        if (! empty($theBody) && isset($theHeaders['headers']['transfer-encoding']) && 'chunked' == $theHeaders['headers']['transfer-encoding']) {
            $theBody = @http_chunked_decode($theBody);
        }

        if (true === $args['decompress'] && true === org_tubepress_impl_http_clientimpl_Encoding::shouldDecode($theHeaders['headers'])) {
            $theBody = http_inflate($theBody);
        }

        return array('headers' => $theHeaders['headers'], 'body' => $theBody, 'response' => $theHeaders['response'], 'cookies' => $theHeaders['cookies']);
    }

    protected function _canHandle($url, $args)
    {
        return function_exists('http_request');
    }
}
