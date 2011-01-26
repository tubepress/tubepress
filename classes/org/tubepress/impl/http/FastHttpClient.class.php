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

function_exists('tubepress_load_classes')
    || require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_api_http_HttpClient',
    'org_tubepress_impl_http_clientimpl_Encoding',
    'org_tubepress_api_patterns_StrategyManager'));

/**
 * Lifted from http://core.trac.wordpress.org/browser/tags/3.0.4/wp-includes/class-http.php
 *
 * HTTP Class for managing HTTP Transports and making HTTP requests.
 *
 */
class org_tubepress_impl_http_FastHttpClient implements org_tubepress_api_http_HttpClient
{
    const LOG_PREFIX = 'HTTP Client';

    const ARGS_BODY         = 'body';
    const ARGS_COOKIES      = 'cookies';
    const ARGS_COMPRESS     = 'compress';
    const ARGS_DECOMPRESS   = 'decompress';
    const ARGS_HEADERS      = 'headers';
    const ARGS_HTTP_VERSION = 'httpversion';
    const ARGS_METHOD       = 'method';
    const ARGS_SSL_VERIFY   = 'sslverify';
    const ARGS_TIMEOUT      = 'timeout';
    const ARGS_USER_AGENT   = 'user-agent';

    /**
     * Post.
     *
     * @param string $url URI resource.
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
     * Parses the responses and splits the parts into headers and body.
     *
     * @param string $strResponse The full response string
     *
     * @return array Array with 'headers' and 'body' keys.
     */
    public static function breakRawStringResponseIntoHeaderAndBody($strResponse)
    {
        $res = explode("\r\n\r\n", $strResponse, 2);

        return array('headers' => isset($res[0]) ? $res[0] : array(), 'body' => isset($res[1]) ? $res[1] : '');
    }

    /**
     * Transform header string into an array.
     *
     * If an array is given then it is assumed to be raw header data with numeric keys with the
     * headers as the values. No headers must be passed that were already processed.
     *
     * @param string|array $headers The headers.
     *
     * @return array Processed string headers. If duplicate headers are encountered,
     *                  Then a numbered array is returned as the value of that header-key.
     */
    public static function getProcessedHeaders($headers)
    {
        // split headers, one per array element
        if (is_string($headers)) {

            // tolerate line terminator: CRLF = LF (RFC 2616 19.3)
            $headers = str_replace("\r\n", "\n", $headers);

            // unfold folded header fields. LWS = [CRLF] 1*(SP | HT) <US-ASCII SP, space (32)>, <US-ASCII HT, horizontal-tab (9)> (RFC 2616 2.2)
            $headers = preg_replace('/\n[ \t]/', ' ', $headers);

            // create the headers array
            $headers = explode("\n", $headers);
        }

        $response = array('code' => 0, 'message' => '');

        // If a redirection has taken place, The headers for each page request may have been passed.
        // In this case, determine the final HTTP header and parse from there.
        for ($i = count($headers)-1; $i >= 0; $i--) {
            if (!empty($headers[$i]) && false === strpos($headers[$i], ':')) {
                $headers = array_splice($headers, $i);
                break;
            }
        }

        $cookies    = array();
        $newheaders = array();

        foreach ($headers as $tempheader) {
            if (empty($tempheader)) {
                continue;
            }

            if (false === strpos($tempheader, ':')) {
                list(, $response['code'], $response['message']) = explode(' ', $tempheader, 3);
                continue;
            }

            list($key, $value) = explode(':', $tempheader, 2);

            if (!empty($value)) {

                $key = strtolower($key);

                if (isset($newheaders[$key])) {

                    if (!is_array($newheaders[$key])) {
                        $newheaders[$key] = array($newheaders[$key]);
                    }

                    $newheaders[$key][] = trim($value);
                } else {
                    $newheaders[$key] = trim($value);
                }
                if ('set-cookie' == $key) {
                    $cookies[] = new org_tubepress_http_FastHttpClient_Cookie($value);
                }
            }
        }

        return array('response' => $response, 'headers' => $newheaders, 'cookies' => $cookies);
    }

    /**
     * Takes the arguments for a ::request() and checks for the cookie array.
     *
     * If it's found, then it's assumed to contain org_tubepress_http_FastHttpClient_Cookie objects, which are each parsed
     * into strings and added to the Cookie: header (within the arguments array). Edits the array by
     * reference.
     *
     * @param array &$r Full array of args passed into ::request()
     *
     * @return void
     */
    public static function buildCookieHeader(&$r)
    {
        if (! empty($r[self::ARGS_COOKIES])) {
            $cookies_header = '';
            foreach ((array) $r[self::ARGS_COOKIES] as $cookie) {
                $cookies_header .= $cookie->getHeaderValue() . '; ';
            }
            $cookies_header         = substr($cookies_header, 0, -2);
            $r['headers']['cookie'] = $cookies_header;
        }
    }

    /**
     * Decodes chunk transfer-encoding, based off the HTTP 1.1 specification.
     *
     * Based off the HTTP http_encoding_dechunk function. Does not support UTF-8. Does not support
     * returning footer headers. Shouldn't be too difficult to support it though.
     *
     * @param string $body Body content
     *
     * @return string Chunked decoded body on success or raw body on failure.
     */
    public static function chunkTransferDecode($body)
    {
        $body = str_replace(array("\r\n", "\r"), "\n", $body);

        // The body is not chunked encoding or is malformed.
        if (! preg_match('/^[0-9a-f]+(\s|\n)+/mi', trim($body))) {
            return $body;
        }

        $parsedBody = '';

        while (true) {
            $hasChunk = (bool) preg_match('/^([0-9a-f]+)(\s|\n)+/mi', $body, $match);

            if (!$hasChunk || empty($match[1])) {
                return $body;
            }

            $length      = hexdec($match[1]);
            $chunkLength = strlen($match[0]);
            $strBody     = substr($body, $chunkLength, $length);
            $parsedBody .= $strBody;
            $body        = ltrim(str_replace(array($match[0], $strBody), '', $body), "\n");

            if ("0" == trim($body)) {
                return $parsedBody; // Ignore footer headers.
            }
        }
    }

/**
     * Send a HTTP request to a URI.
     *
     * The body and headers are part of the arguments. The 'body' argument is for the body and will
     * accept either a string or an array. The 'headers' argument should be an array, but a string
     * is acceptable. If the 'body' argument is an array, then it will automatically be escaped
     * using http_build_query().
     *
     * The only URI that are supported in the HTTP Transport implementation are the HTTP and HTTPS
     * protocols. HTTP and HTTPS are assumed so the server might not know how to handle the send
     * headers. Other protocols are unsupported and most likely will fail.
     *
     * Accepted self::ARGS_METHOD values are 'GET', 'POST', and 'HEAD', some transports technically allow
     * others, but should not be assumed. The 'timeout' is used to sent how long the connection
     * should stay open before failing when no response. 'redirection' is used to track how many
     * redirects were taken and used to sent the amount for other transports, but not all transports
     * accept setting that value.
     *
     * The 'httpversion' option is used to sent the HTTP version and accepted values are '1.0', and
     * '1.1' and should be a string. Version 1.1 is not supported, because of chunk response.
     *
     * @param string $url        URI resource.
     * @param array  $methodName GET|POST
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

        $r = array_merge($defaults, $args);
        $arrURL = parse_url($url);

        if (empty($url) || empty($arrURL['scheme'])) {
            throw new Exception('A valid URL was not provided.');
        }

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Will perform %s to %s', $r[self::ARGS_METHOD], $url);

        // Determine if this is a https call and pass that on to the transport functions
        // so that we can blacklist the transports that do not support ssl verification
        $r['ssl'] = $arrURL['scheme'] == 'https' || $arrURL['scheme'] == 'ssl';

        // Determine if this request is local
        $r['local'] = 'localhost' == $arrURL['host'];

        if (org_tubepress_impl_http_clientimpl_Encoding::isAvailable()) {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'HTTP compression is available. Yay!');
            $r[self::ARGS_HEADERS]['Accept-Encoding'] = org_tubepress_impl_http_clientimpl_Encoding::getAcceptEncodingString();
        } else {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'HTTP compression is NOT available. Boo.');
        }

        if (empty($r[self::ARGS_BODY])) {

            $r[self::ARGS_BODY] = null;

            // Some servers fail when sending content without the content-length header being set.
            // Also, to fix another bug, we only send when doing POST and PUT and the content-length
            // header isn't already set.
            if (($r[self::ARGS_METHOD] == org_tubepress_api_http_HttpClient::HTTP_METHOD_POST || $r[self::ARGS_METHOD] == org_tubepress_api_http_HttpClient::HTTP_METHOD_PUT) 
                && ! isset($r[self::ARGS_HEADERS][org_tubepress_api_http_HttpClient::HTTP_HEADER_CONTENT_LENGTH])) {
                $r[self::ARGS_HEADERS][org_tubepress_api_http_HttpClient::HTTP_HEADER_CONTENT_LENGTH] = 0;
            }

        } else {

            if (is_array($r[self::ARGS_BODY]) || is_object($r[self::ARGS_BODY])) {

                if (! version_compare(phpversion(), '5.1.2', '>=')) {
                    $r[self::ARGS_BODY] = self::_http_build_query($r[self::ARGS_BODY], null, '&');
                } else {
                    $r[self::ARGS_BODY] = http_build_query($r[self::ARGS_BODY], null, '&');
                }

                $r[self::ARGS_HEADERS]['Content-Type']   = 'application/x-www-form-urlencoded; charset=utf-8';
                $r[self::ARGS_HEADERS][org_tubepress_api_http_HttpClient::HTTP_HEADER_CONTENT_LENGTH] = strlen($r[self::ARGS_BODY]);
            }

            if (! isset($r[self::ARGS_HEADERS][org_tubepress_api_http_HttpClient::HTTP_HEADER_CONTENT_LENGTH])) {
                $r[self::ARGS_HEADERS][org_tubepress_api_http_HttpClient::HTTP_HEADER_CONTENT_LENGTH] = strlen($r[self::ARGS_BODY]);
            }
        }

        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $sm  = $ioc->get('org_tubepress_api_patterns_StrategyManager');

        return $sm->executeStrategy(array(
            'org_tubepress_impl_http_clientimpl_strategies_ExtHttpStrategy',
            'org_tubepress_impl_http_clientimpl_strategies_CurlStrategy',
            'org_tubepress_impl_http_clientimpl_strategies_StreamsStrategy',
            'org_tubepress_impl_http_clientimpl_strategies_FopenStrategy',
            'org_tubepress_impl_http_clientimpl_strategies_FsockOpenStrategy'), $url, $r);
    }

    private static function _http_build_query($data, $prefix=null, $sep=null, $key='', $urlencode=true)
    {
        $ret = array();

        foreach ((array) $data as $k => $v) {
            if ($urlencode) {
                $k = urlencode($k);
            }
            if (is_int($k) && $prefix != null) {
                $k = $prefix.$k;
            }
            if (!empty($key)) {
                $k = $key . '%5B' . $k . '%5D';
            }
            if ($v === null) {
                continue;
            } elseif ($v === false) {
                $v = '0';
            }

            if (is_array($v) || is_object($v)) {
                array_push($ret, self::_http_build_query($v, '', $sep, $k, $urlencode));
            } elseif ($urlencode) {
                array_push($ret, $k.'='.urlencode($v));
            } else {
                array_push($ret, $k.'='.$v);
            }
        }

        if (null === $sep) {
            $sep = ini_get('arg_separator.output');
        }

        return implode($sep, $ret);
    }
}

