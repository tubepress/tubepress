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
    'org_tubepress_api_const_options_names_Advanced',
    'org_tubepress_api_http_HttpClient',
    'org_tubepress_api_exec_ExecutionContext',
    'org_tubepress_api_patterns_cor_Chain',
    'org_tubepress_impl_http_clientimpl_Encoding',
));

/**
 * Lifted from http://core.trac.wordpress.org/browser/tags/3.0.4/wp-includes/class-http.php
 *
 * HTTP Class for managing HTTP Transports and making HTTP requests.
 *
 */
class org_tubepress_impl_http_HttpClientChain implements org_tubepress_api_http_HttpClient
{
    const LOG_PREFIX = 'HTTP Client';

    const ARGS_BODY         = 'body';
    const ARGS_COOKIES      = 'cookies';
    const ARGS_COMPRESS     = 'compress';
    const ARGS_DECOMPRESS   = 'decompress';
    const ARGS_HEADERS      = 'headers';
    const ARGS_HTTP_VERSION = 'httpversion';
    const ARGS_IS_SSL       = 'ssl';
    const ARGS_METHOD       = 'method';
    const ARGS_SSL_VERIFY   = 'sslverify';
    const ARGS_TIMEOUT      = 'timeout';
    const ARGS_USER_AGENT   = 'user-agent';

    /**
     * Post.
     *
     * @param string  $url  URI resource.
     * @param unknown $body The body of the POST
     *
     * @return string Resulting body as a string (could be null)
     */
    public function post($url, $body)
    {
        return $this->_request($url, array(
           self::ARGS_METHOD => org_tubepress_api_http_HttpClient::HTTP_METHOD_POST,
           self::ARGS_BODY => $body
        ));
    }

    /**
     * Get.
     *
     * @param string $url URI resource.
     *
     * @return string Resulting body as a string (could be null)
     */
    public function get($url)
    {
        return $this->_request($url, array(self::ARGS_METHOD => org_tubepress_api_http_HttpClient::HTTP_METHOD_GET));
    }

    /**
     * Send a HTTP request to a URI.
     *
     * The body and headers are part of the arguments. The 'body' argument is for the body and will
     * accept either a string or an array. The 'headers' argument should be an array, but a string
     * is acceptable.
     *
     * The only URI that are supported in the HTTP Transport implementation are the HTTP and HTTPS
     * protocols. HTTP and HTTPS are assumed so the server might not know how to handle the send
     * headers. Other protocols are unsupported and most likely will fail.
     *
     * @param string $url  URI resource.
     * @param array  $args Generic HTTP options.
     *
     * @return array containing 'headers', 'body', 'response', 'cookies'
     */
    private function _request($url, $args)
    {
        $defaults = array(
            self::ARGS_METHOD       => org_tubepress_api_http_HttpClient::HTTP_METHOD_GET,
            self::ARGS_TIMEOUT      => 5,
            self::ARGS_HTTP_VERSION => '1.0',
            self::ARGS_USER_AGENT   => 'TubePress; http://tubepress.org',
            self::ARGS_HEADERS      => array(),
            self::ARGS_COOKIES      => array(),
            self::ARGS_BODY         => null,
            self::ARGS_COMPRESS     => false,
            self::ARGS_DECOMPRESS   => true,
            self::ARGS_SSL_VERIFY   => true
        );

        $r      = array_merge($defaults, $args);
        $arrURL = parse_url($url);

        if (empty($url) || empty($arrURL['scheme'])) {
            throw new Exception('A valid URL was not provided.');
        }

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Will perform %s to <a href="%s">%s</a>', $r[self::ARGS_METHOD], $url, $url);

        // Determine if this is a https call and pass that on to the transport functions
        // so that we can blacklist the transports that do not support ssl verification
        $r[self::ARGS_IS_SSL] = $arrURL['scheme'] == 'https' || $arrURL['scheme'] == 'ssl';

        // Determine if this request is local
        $r['local'] = 'localhost' == $arrURL['host'];

        if (org_tubepress_impl_http_clientimpl_Encoding::isCompressionAvailable()) {

            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'HTTP compression is available. Yay!');
            $r[self::ARGS_HEADERS][org_tubepress_api_http_HttpClient::HTTP_HEADER_ACCEPT_ENCODING] = org_tubepress_impl_http_clientimpl_Encoding::getAcceptEncodingString();

        } else {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'HTTP compression is NOT available. Boo.');
        }

        if (empty($r[self::ARGS_BODY])) {

            $r[self::ARGS_BODY] = null;

            // Some servers fail when sending content without the content-length header being set.
            // Also, to fix another bug, we only send when doing POST and PUT and the content-length
            // header isn't already set.
            if (($r[self::ARGS_METHOD] == org_tubepress_api_http_HttpClient::HTTP_METHOD_POST || $r[self::ARGS_METHOD] == org_tubepress_api_http_HttpClient::HTTP_METHOD_PUT)) {
                $r[self::ARGS_HEADERS][org_tubepress_api_http_HttpClient::HTTP_HEADER_CONTENT_LENGTH] = 0;
            }

        } else {

            $r[self::ARGS_HEADERS][org_tubepress_api_http_HttpClient::HTTP_HEADER_CONTENT_LENGTH] = strlen($r[self::ARGS_BODY]);
        }

        $ioc      = org_tubepress_impl_ioc_IocContainer::getInstance();
        $commands = self::_getTransportCommands($ioc);
        $sm       = $ioc->get('org_tubepress_api_patterns_cor_Chain');
        $context  = $sm->createContextInstance();

        $context->url  = $url;
        $context->args = $r;

        $status = $sm->execute($context, $commands);

        if ($status === false) {
            throw new Exception("Could not retrieve $url");
        }

        return $context->returnValue;
    }

    private static function _getTransportCommands(org_tubepress_api_ioc_IocService $ioc)
    {
        $result  = array();
        $context = $ioc->get('org_tubepress_api_exec_ExecutionContext');

        if (!$context->get(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_EXTHTTP)) {
            $result[] = 'org_tubepress_impl_http_clientimpl_commands_ExtHttpCommand';
        } else {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'ExtHttp transport disabled by request');
        }

        if (!$context->get(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_CURL)) {
            $result[] = 'org_tubepress_impl_http_clientimpl_commands_CurlCommand';
        } else {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Curl transport disabled by request');
        }

        if (!$context->get(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_STREAMS)) {
            $result[] = 'org_tubepress_impl_http_clientimpl_commands_StreamsCommand';
        } else {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Streams transport disabled by request');
        }

        if (!$context->get(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FOPEN)) {
            $result[] = 'org_tubepress_impl_http_clientimpl_commands_FopenCommand';
        } else {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'fopen transport disabled by request');
        }

        if (!$context->get(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FSOCKOPEN)) {
            $result[] = 'org_tubepress_impl_http_clientimpl_commands_FsockOpenCommand';
        } else {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'fsockopen transport disabled by request');
        }

        if (sizeof($result) === 0) {
            throw new Exception("Must enable at least one HTTP transport");
        }

        return $result;
    }
}

