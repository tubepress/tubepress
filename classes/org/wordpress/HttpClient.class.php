<?php
/**
 * This entire class is based on http://core.trac.wordpress.org/browser/trunk/wp-includes/class-http.php.
 *
 * HTTP Class for managing HTTP Transports and making HTTP requests.
 *
 * This class is called for the functionality of making HTTP requests. There is no available functionality
 * to add HTTP transport implementations, since most of the HTTP transports are added and available for use.
 *
 * There are no properties, because none are needed and for performance reasons. Some of the
 * functions are static and while they do have some overhead over functions in PHP4, the purpose is
 * maintainability.
 *
 */
class org_wordpress_HttpClient
{
    const LOG_PREFIX = 'HTTP Client';

    /**
     * PHP5 style Constructor - Set up available transport if not available.
     *
     * The transport are set up to save time and will only be created
     * once. This class can be created many times without having to go through the step of finding
     * which transports are available.
     *
     * @return org_wordpress_HttpClient
     */
    function __construct()
    {
        self::_getTransport();
        self::_postTransport();
    }

    /**
     * Tests all of the objects and returns the object that passes. Also caches that object to be
     * used later.
     *
     * The order for the GET/HEAD requests are HTTP Extension, cURL, Streams, Fopen, and finally
     * Fsockopen. fsockopen() is used last, because it has the most overhead in its implementation.
     * There isn't any real way around it, since redirects have to be supported, much the same way
     * the other transports also handle redirects.
     *
     * There are currently issues with "localhost" not resolving correctly with DNS. This may cause
     * an error "failed to open stream: A connection attempt failed because the connected party did
     * not properly respond after a period of time, or established connection failed because [the]
     * connected host has failed to respond."
     *
     * @param array $args Request args, default use an empty array
     *
     * @return object|null Null if no transports are available, HTTP transport object.
     */
    private static function &_getTransport($args = array())
    {
        static $working_transport, $blocking_transport, $nonblocking_transport;

        if ( is_null($working_transport) ) {

            org_tubepress_impl_log_Log(self::LOG_PREFIX, 'Determining which implementation to use for GET requests');

            if ( true === org_wordpress_HttpClient_ExtHttp::test($args) ) {
                org_tubepress_impl_log_Log(self::LOG_PREFIX, 'Will use ExtHttp for GET requests');
                $working_transport['exthttp'] = new org_wordpress_HttpClient_ExtHttp();
                $blocking_transport[]         = &$working_transport['exthttp'];
            } else if ( true === org_wordpress_HttpClient_Curl::test($args) ) {
                org_tubepress_impl_log_Log(self::LOG_PREFIX, 'Will use curl for GET requests');
                $working_transport['curl'] = new org_wordpress_HttpClient_Curl();
                $blocking_transport[]      = &$working_transport['curl'];
            } else if ( true === org_wordpress_HttpClient_Streams::test($args) ) {
                org_tubepress_impl_log_Log(self::LOG_PREFIX, 'Will use streams for GET requests');
                $working_transport['streams'] = new org_wordpress_HttpClient_Streams();
                $blocking_transport[]         = &$working_transport['streams'];
            } else if ( true === org_wordpress_HttpClient_Fopen::test($args) ) {
                org_tubepress_impl_log_Log(self::LOG_PREFIX, 'Will use fopen for GET requests');
                $working_transport['fopen'] = new org_wordpress_HttpClient_Fopen();
                $blocking_transport[]       = &$working_transport['fopen'];
            } else if ( true === org_wordpress_HttpClient_Fsockopen::test($args) ) {
                org_tubepress_impl_log_Log(self::LOG_PREFIX, 'Will use fsockopen for GET requests');
                $working_transport['fsockopen'] = new org_wordpress_HttpClient_Fsockopen();
                $blocking_transport[]           = &$working_transport['fsockopen'];
            }

            foreach ( array('curl', 'streams', 'fopen', 'fsockopen', 'exthttp') as $transport ) {
                if (isset($working_transport[$transport])) {
                    $nonblocking_transport[] = &$working_transport[$transport];
                }
            }
        }

        if ( isset($args['blocking']) && !$args['blocking']) {
            return $nonblocking_transport;
        } else {
            return $blocking_transport;
        }
    }

    /**
     * Tests all of the objects and returns the object that passes. Also caches
     * that object to be used later. This is for posting content to a URL and
     * is used when there is a body. The plain Fopen Transport can not be used
     * to send content, but the streams transport can. This is a limitation that
     * is addressed here, by just not including that transport.
     *
     * @param array $args Request args, default us an empty array
     *
     * @return object|null Null if no transports are available, HTTP transport object.
     */
    private static function &_postTransport($args = array())
    {
        static $working_transport, $blocking_transport, $nonblocking_transport;

        if ( is_null($working_transport) ) {

            org_tubepress_impl_log_Log(self::LOG_PREFIX, 'Determining which implementation to use for POST requests');

            if ( true === org_wordpress_HttpClient_ExtHttp::test($args) ) {
                org_tubepress_impl_log_Log(self::LOG_PREFIX, 'Will use ExtHttp for POST requests');
                $working_transport['exthttp'] = new org_wordpress_HttpClient_ExtHttp();
                $blocking_transport[]         = &$working_transport['exthttp'];
            } else if ( true === org_wordpress_HttpClient_Curl::test($args) ) {
                org_tubepress_impl_log_Log(self::LOG_PREFIX, 'Will use curl for POST requests');
                $working_transport['curl'] = new org_wordpress_HttpClient_Curl();
                $blocking_transport[]      = &$working_transport['curl'];
            } else if ( true === org_wordpress_HttpClient_Streams::test($args) ) {
                org_tubepress_impl_log_Log(self::LOG_PREFIX, 'Will use streams for POST requests');
                $working_transport['streams'] = new org_wordpress_HttpClient_Streams();
                $blocking_transport[]         = &$working_transport['streams'];
            } else if ( true === org_wordpress_HttpClient_Fsockopen::test($args) ) {
                org_tubepress_impl_log_Log(self::LOG_PREFIX, 'Will use fsockopen for POST requests');
                $working_transport['fsockopen'] = new org_wordpress_HttpClient_Fsockopen();
                $blocking_transport[]           = &$working_transport['fsockopen'];
            }

            foreach ( array('curl', 'streams', 'fsockopen', 'exthttp') as $transport ) {
                if ( isset($working_transport[$transport]) ) {
                    $nonblocking_transport[] = &$working_transport[$transport];
                }
            }
        }

        if ( isset($args['blocking']) && !$args['blocking'] ) {
            return $nonblocking_transport;
        } else {
            return $blocking_transport;
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
     * The defaults are 'method', 'timeout', 'redirection', 'httpversion', 'blocking' and
     * 'user-agent'.
     *
     * Accepted 'method' values are 'GET', 'POST', and 'HEAD', some transports technically allow
     * others, but should not be assumed. The 'timeout' is used to sent how long the connection
     * should stay open before failing when no response. 'redirection' is used to track how many
     * redirects were taken and used to sent the amount for other transports, but not all transports
     * accept setting that value.
     *
     * The 'httpversion' option is used to sent the HTTP version and accepted values are '1.0', and
     * '1.1' and should be a string. Version 1.1 is not supported, because of chunk response.
     *
     * 'blocking' is the default, which is used to tell the transport, whether it should halt PHP
     * while it performs the request or continue regardless. Actually, that isn't entirely correct.
     * Blocking mode really just means whether the fread should just pull what it can whenever it
     * gets bytes or if it should wait until it has enough in the buffer to read or finishes reading
     * the entire content. It doesn't actually always mean that PHP will continue going after making
     * the request.
     *
     * @param string $url  URI resource.
     * @param array  $args Optional. Override the defaults.
     *
     * @return array containing 'headers', 'body', 'response', 'cookies'
     */
    public function request( $url, $args = array() )
    {
        $defaults = array(
            'method'      => 'GET',
            'timeout'     => 5,
            'redirection' => 5,
            'httpversion' => '1.0',
            'user-agent'  => 'TubePress; http://tubepress.org',
            'blocking'    => true,
            'headers'     => array(),
            'cookies'     => array(),
            'body'        => null,
            'compress'    => false,
            'decompress'  => true,
            'sslverify'   => true
        );

        $r      = array_merge($defaults, $args);
        $arrURL = parse_url($url);

        if ( empty( $url ) || empty( $arrURL['scheme'] ) ) {
            throw new Exception('A valid URL was not provided.');
        }

        // Determine if this is a https call and pass that on to the transport functions
        // so that we can blacklist the transports that do not support ssl verification
        $r['ssl'] = $arrURL['scheme'] == 'https' || $arrURL['scheme'] == 'ssl';

        // Determine if this request is local
        $r['local'] = 'localhost' == $arrURL['host'];

        if ( is_null($r['headers']) ) {
            $r['headers'] = array();
        }

        if ( ! is_array($r['headers']) ) {
            $processedHeaders = org_wordpress_HttpClient::processHeaders($r['headers']);
            $r['headers']     = $processedHeaders['headers'];
        }

        if ( isset($r['headers']['User-Agent'] ) ) {
            $r['user-agent'] = $r['headers']['User-Agent'];
            unset( $r['headers']['User-Agent'] );
        }

        if ( isset($r['headers']['user-agent'] ) ) {
            $r['user-agent'] = $r['headers']['user-agent'];
            unset( $r['headers']['user-agent'] );
        }

        // Construct Cookie: header if any cookies are set
        org_wordpress_HttpClient::buildCookieHeader($r);

        if ( org_wordpress_HttpClient_Encoding::is_available() ) {
            $r['headers']['Accept-Encoding'] = org_wordpress_HttpClient_Encoding::accept_encoding();
        }

        if ( empty($r['body']) ) {
            $r['body'] = null;
            // Some servers fail when sending content without the content-length header being set.
            // Also, to fix another bug, we only send when doing POST and PUT and the content-length
            // header isn't already set.
            if ( ($r['method'] == 'POST' || $r['method'] == 'PUT') && ! isset($r['headers']['Content-Length'] ) ) {
                $r['headers']['Content-Length'] = 0;
            }

            // The method is ambiguous, because we aren't talking about HTTP methods, the "get" in
            // this case is simply that we aren't sending any bodies and to get the transports that
            // don't support sending bodies along with those which do.
            $transports = self::_getTransport($r);
        } else {
            if ( is_array($r['body']) || is_object($r['body']) ) {
                if ( ! version_compare(phpversion(), '5.1.2', '>=') ) {
                    $r['body'] = self::_http_build_query($r['body'], null, '&');
                } else {
                    $r['body'] = http_build_query($r['body'], null, '&');
                }
                $r['headers']['Content-Type']   = 'application/x-www-form-urlencoded; charset=utf-8';
                $r['headers']['Content-Length'] = strlen($r['body']);
            }

            if ( ! isset($r['headers']['Content-Length']) && ! isset($r['headers']['content-length']) ) {
                $r['headers']['Content-Length'] = strlen($r['body']);
            }

            // The method is ambiguous, because we aren't talking about HTTP methods, the "post" in
            // this case is simply that we are sending HTTP body and to get the transports that do
            // support sending the body. Not all do, depending on the limitations of the PHP core
            // limitations.
            $transports = self::_postTransport($r);
        }

        $response = array( 'headers' => array(), 'body' => '', 'response' => array('code' => false, 'message' => false), 'cookies' => array() );
        foreach ( (array) $transports as $transport ) {
            try {
                org_tubepress_impl_log_Log(self::LOG_PREFIX, 'Fetching <tt>%s</tt> with %s', $url, get_class($transport));
                $response = $transport->request($url, $r);
            } catch (Exception $e) {
                org_tubepress_impl_log_Log(self::LOG_PREFIX, 'Caught error when requesting %s with %s: %s', $url, get_class($transport), $e->getMessage());
            }
        }

        org_tubepress_impl_log_Log(self::LOG_PREFIX, 'Successfully retrieved <tt>%s</tt> with %s', $url, get_class($transport));
        return $response;
    }

    /**
     * Uses the POST HTTP method.
     *
     * @param string $url  URI resource.
     * @param array  $args Optional. Override the defaults.
     *
     * @return boolean
     */
    public function post($url, $args = array())
    {
        $defaults = array('method' => 'POST');
        $r        = array_merge($defaults, $args);
        return $this->request($url, $r);
    }

    /**
     * Uses the GET HTTP method.
     *
     * @param string $url  URI resource.
     * @param array  $args Optional. Override the defaults.
     *
     * @return boolean
     */
    public function get($url, $args = array())
    {
        $defaults = array('method' => 'GET');
        $r        = array_merge($defaults, $args);
        return $this->request($url, $r);
    }

    /**
     * Uses the HEAD HTTP method.
     *
     * @param string $url  URI resource.
     * @param array  $args Optional. Override the defaults.
     *
     * @return boolean
     */
    public function head($url, $args = array())
    {
        $defaults = array('method' => 'HEAD');
        $r        = array_merge($defaults, $args);
        return $this->request($url, $r);
    }

    /**
     * Parses the responses and splits the parts into headers and body.
     *
     * @param string $strResponse The full response string
     *
     * @return array Array with 'headers' and 'body' keys.
     */
    public static function processResponse($strResponse)
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
    public static function processHeaders($headers)
    {
        // split headers, one per array element
        if ( is_string($headers) ) {
            // tolerate line terminator: CRLF = LF (RFC 2616 19.3)
            $headers = str_replace("\r\n", "\n", $headers);
            // unfold folded header fields. LWS = [CRLF] 1*( SP | HT ) <US-ASCII SP, space (32)>, <US-ASCII HT, horizontal-tab (9)> (RFC 2616 2.2)
            $headers = preg_replace('/\n[ \t]/', ' ', $headers);
            // create the headers array
            $headers = explode("\n", $headers);
        }

        $response = array('code' => 0, 'message' => '');

        // If a redirection has taken place, The headers for each page request may have been passed.
        // In this case, determine the final HTTP header and parse from there.
        for ( $i = count($headers)-1; $i >= 0; $i-- ) {
            if ( !empty($headers[$i]) && false === strpos($headers[$i], ':') ) {
                $headers = array_splice($headers, $i);
                break;
            }
        }

        $cookies    = array();
        $newheaders = array();
        foreach ( $headers as $tempheader ) {
            if ( empty($tempheader) ) {
                continue;
            }

            if ( false === strpos($tempheader, ':') ) {
                list( , $response['code'], $response['message']) = explode(' ', $tempheader, 3);
                continue;
            }

            list($key, $value) = explode(':', $tempheader, 2);

            if ( !empty( $value ) ) {
                $key = strtolower($key);
                if ( isset($newheaders[$key]) ) {
                    if ( !is_array($newheaders[$key]) ) {
                        $newheaders[$key] = array($newheaders[$key]);
                    }
                    $newheaders[$key][] = trim($value);
                } else {
                    $newheaders[$key] = trim($value);
                }
                if ( 'set-cookie' == $key ) {
                    $cookies[] = new org_wordpress_HttpClient_Cookie($value);
                }
            }
        }

        return array('response' => $response, 'headers' => $newheaders, 'cookies' => $cookies);
    }

    /**
     * Takes the arguments for a ::request() and checks for the cookie array.
     *
     * If it's found, then it's assumed to contain org_wordpress_HttpClient_Cookie objects, which are each parsed
     * into strings and added to the Cookie: header (within the arguments array). Edits the array by
     * reference.
     *
     * @param array &$r Full array of args passed into ::request()
     *
     * @return void
     */
    public static function buildCookieHeader( &$r )
    {
        if ( ! empty($r['cookies']) ) {
            $cookies_header = '';
            foreach ( (array) $r['cookies'] as $cookie ) {
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
        if ( ! preg_match('/^[0-9a-f]+(\s|\n)+/mi', trim($body))) {
            return $body;
        }

        $parsedBody = '';
        //$parsedHeaders = array(); Unsupported

        while ( true ) {
            $hasChunk = (bool) preg_match('/^([0-9a-f]+)(\s|\n)+/mi', $body, $match);

            if ( $hasChunk ) {
                if ( empty($match[1]) ) {
                    return $body;
                }

                $length      = hexdec($match[1]);
                $chunkLength = strlen($match[0]);

                $strBody     = substr($body, $chunkLength, $length);
                $parsedBody .= $strBody;

                $body = ltrim(str_replace(array($match[0], $strBody), '', $body), "\n");

                if ( "0" == trim($body) ) {
                    return $parsedBody; // Ignore footer headers.
                }
            } else {
                return $body;
            }
        }
    }

    private static function _http_build_query($data, $prefix=null, $sep=null, $key='', $urlencode=true)
    {
        $ret = array();

        foreach ( (array) $data as $k => $v ) {
            if ( $urlencode) {
                $k = urlencode($k);
            }
            if ( is_int($k) && $prefix != null ) {
                $k = $prefix.$k;
            }
            if ( !empty($key) ) {
                $k = $key . '%5B' . $k . '%5D';
            }
            if ( $v === null ) {
                continue;
            } elseif ( $v === false ) {
                $v = '0';
            }

            if ( is_array($v) || is_object($v) ) {
                array_push($ret, self::_http_build_query($v, '', $sep, $k, $urlencode));
            } elseif ( $urlencode ) {
                array_push($ret, $k.'='.urlencode($v));
            } else {
                array_push($ret, $k.'='.$v);
            }
        }

        if ( null === $sep ) {
            $sep = ini_get('arg_separator.output');
        }

        return implode($sep, $ret);
    }
}

/**
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

        if ( isset($r['headers']['User-Agent']) ) {
            $r['user-agent'] = $r['headers']['User-Agent'];
            unset($r['headers']['User-Agent']);
        } else if ( isset($r['headers']['user-agent']) ) {
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

        if ( ! isset($arrURL['port'] ) ) {
            if ( ( $arrURL['scheme'] == 'ssl' || $arrURL['scheme'] == 'https' ) && extension_loaded('openssl') ) {
                $fsockopen_host   = "ssl://$fsockopen_host";
                $arrURL['port']   = 443;
                $secure_transport = true;
            } else {
                $arrURL['port'] = 80;
            }
        }

        //fsockopen has issues with 'localhost' with IPv6 with certain versions of PHP, It attempts to connect to ::1,
        // which fails when the server is not set up for it. For compatibility, always connect to the IPv4 address.
        if ( 'localhost' == strtolower($fsockopen_host) ) {
            $fsockopen_host = '127.0.0.1';
        }

        // There are issues with the HTTPS and SSL protocols that cause errors that can be safely
        // ignored and should be ignored.
        if ( true === $secure_transport ) {
            $error_reporting = error_reporting(0);
        }

        $handle = @fsockopen($fsockopen_host, $arrURL['port'], $iError, $strError, $r['timeout']);

        if ( false === $handle ) {
            throw new Exception($iError . ': ' . $strError);
        }

        $timeout  = (int) floor($r['timeout']);
        $utimeout = $timeout == $r['timeout'] ? 0 : 1000000 * $r['timeout'] % 1000000;
        stream_set_timeout($handle, $timeout, $utimeout);

        $requestPath = $arrURL['path'] . ( isset($arrURL['query']) ? '?' . $arrURL['query'] : '' );

        if ( empty($requestPath) ) {
            $requestPath .= '/';
        }

        $strHeaders  = strtoupper($r['method']) . ' ' . $requestPath . ' HTTP/' . $r['httpversion'] . "\r\n";
        $strHeaders .= 'Host: ' . $arrURL['host'] . "\r\n";

        if ( isset($r['user-agent']) ) {
            $strHeaders .= 'User-agent: ' . $r['user-agent'] . "\r\n";
        }

        if ( is_array($r['headers']) ) {
            foreach ( (array) $r['headers'] as $header => $headerValue ) {
                $strHeaders .= $header . ': ' . $headerValue . "\r\n";
            }
        } else {
            $strHeaders .= $r['headers'];
        }

        $strHeaders .= "\r\n";

        if ( ! is_null($r['body']) ) {
            $strHeaders .= $r['body'];
        }

        fwrite($handle, $strHeaders);

        if ( ! $r['blocking'] ) {
            fclose($handle);
            return array( 'headers' => array(), 'body' => '', 'response' => array('code' => false, 'message' => false), 'cookies' => array() );
        }

        $strResponse = '';
        while ( ! feof($handle) ) {
            $strResponse .= fread($handle, 4096);
        }

        fclose($handle);

        if ( true === $secure_transport ) {
            error_reporting($error_reporting);
        }

        $process    = org_wordpress_HttpClient::processResponse($strResponse);
        $arrHeaders = org_wordpress_HttpClient::processHeaders($process['headers']);

        // Is the response code within the 400 range?
        if ( (int) $arrHeaders['response']['code'] >= 400 && (int) $arrHeaders['response']['code'] < 500 ) {
            throw new Exception($arrHeaders['response']['code'] . ': ' . $arrHeaders['response']['message']);
        }

        // If location is found, then assume redirect and redirect to location.
        if ( 'HEAD' != $r['method'] && isset($arrHeaders['headers']['location']) ) {
            if ( $r['redirection']-- > 0 ) {
                return $this->request($arrHeaders['headers']['location'], $r);
            } else {
                throw new Exception('Too many redirects.');
            }
        }

        // If the body was chunk encoded, then decode it.
        if ( ! empty( $process['body'] ) && isset($arrHeaders['headers']['transfer-encoding'] ) && 'chunked' == $arrHeaders['headers']['transfer-encoding'] ) {
            $process['body'] = org_wordpress_HttpClient::chunkTransferDecode($process['body']);
        }

        if ( true === $r['decompress'] && true === org_wordpress_HttpClient_Encoding::should_decode($arrHeaders['headers']) ) {
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
    public function test( $args = array() )
    {
        $is_ssl = isset($args['ssl']) && $args['ssl'];

        if ( ! $is_ssl && function_exists('fsockopen') ) {
            $use = true;
        } elseif ( $is_ssl && extension_loaded('openssl') && function_exists('fsockopen') ) {
            $use = true;
        } else {
            $use = false;
        }

        return $use;
    }
}

/**
 * HTTP request method uses fopen function to retrieve the url.
 *
 * Does not allow for $context support,
 * but should still be okay, to write the headers, before getting the response. Also requires that
 * 'allow_url_fopen' to be enabled.
 *
 */
class org_wordpress_HttpClient_Fopen
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
    function request($url, $args = array())
    {
        $defaults = array(
            'method' => 'GET', 'timeout' => 5,
            'redirection' => 5, 'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(), 'body' => null, 'cookies' => array()
        );

        $r = array_merge($defaults, $args);

        $arrURL = parse_url($url);

        if ( false === $arrURL ) {
            throw new Exception(sprintf('Malformed URL: %s'), $url);
        }

        if ( 'http' != $arrURL['scheme'] && 'https' != $arrURL['scheme'] ) {
            $url = str_replace($arrURL['scheme'], 'http', $url);
        }

        if ( is_null($r['headers']) ) {
            $r['headers'] = array();
        }

        if ( is_string($r['headers']) ) {
            $processedHeaders = org_wordpress_HttpClient::processHeaders($r['headers']);
            $r['headers']     = $processedHeaders['headers'];
        }

        $initial_user_agent = ini_get('user_agent');

        if ( !empty($r['headers']) && is_array($r['headers']) ) {
            $user_agent_extra_headers = '';
            foreach ( $r['headers'] as $header => $value ) {
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

        if ( ! $r['blocking'] ) {
            fclose($handle);
            @ini_set('user_agent', $initial_user_agent); //Clean up any extra headers added
            return array( 'headers' => array(), 'body' => '', 'response' => array('code' => false, 'message' => false), 'cookies' => array() );
        }

        $strResponse = '';
        while ( ! feof($handle) ) {
            $strResponse .= fread($handle, 4096);
        }

        if ( function_exists('stream_get_meta_data') ) {
            $meta = stream_get_meta_data($handle);

            $theHeaders = $meta['wrapper_data'];
            if ( isset($meta['wrapper_data']['headers']) ) {
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

        if ( ! empty( $strResponse ) && isset($processedHeaders['headers']['transfer-encoding'] ) && 'chunked' == $processedHeaders['headers']['transfer-encoding'] ) {
            $strResponse = org_wordpress_HttpClient::chunkTransferDecode($strResponse);
        }

        if ( true === $r['decompress'] && true === org_wordpress_HttpClient_Encoding::should_decode($processedHeaders['headers'])) {
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
        if ( ! function_exists('fopen') || (function_exists('ini_get') && true != ini_get('allow_url_fopen')) ) {
            return false;
        }

        if ( isset($args['method']) && 'HEAD' == $args['method'] )  {//This transport cannot make a HEAD request
            return false;
        }

        $use = true;
        //PHP does not verify SSL certs, We can only make a request via this transports if SSL Verification is turned off.
        $is_ssl = isset($args['ssl']) && $args['ssl'];
        if ( $is_ssl ) {
            $is_local   = isset($args['local']) && $args['local'];
            $ssl_verify = isset($args['sslverify']) && $args['sslverify'];
            if ( $is_local && true != true ) {
                $use = true;
            } elseif ( !$is_local && true != true ) {
                $use = true;
            } elseif ( !$ssl_verify ) {
                $use = true;
            } else {
                $use = false;
            }
        }

        return $use;
    }
}

/**
 * HTTP request method uses Streams to retrieve the url.
 *
 * Requires PHP 5.0+ and uses fopen with stream context. Requires that 'allow_url_fopen' PHP setting
 * to be enabled.
 *
 * Second preferred method for getting the URL, for PHP 5.
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 2.7.0
 */
class org_wordpress_HttpClient_Streams {
    /**
     * Send a HTTP request to a URI using streams with fopen().
     *
     * @access public
     * @since 2.7.0
     *
     * @param string $url
     * @param str|array $args Optional. Override the defaults.
     * @return array 'headers', 'body', 'cookies' and 'response' keys.
     */
    function request($url, $args = array()) {
        $defaults = array(
            'method' => 'GET', 'timeout' => 5,
            'redirection' => 5, 'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(), 'body' => null, 'cookies' => array()
        );

        $r = array_merge($defaults, $args );

        if ( isset($r['headers']['User-Agent']) ) {
            $r['user-agent'] = $r['headers']['User-Agent'];
            unset($r['headers']['User-Agent']);
        } else if ( isset($r['headers']['user-agent']) ) {
            $r['user-agent'] = $r['headers']['user-agent'];
            unset($r['headers']['user-agent']);
        }

        // Construct Cookie: header if any cookies are set
        org_wordpress_HttpClient::buildCookieHeader( $r );

        $arrURL = parse_url($url);

        if ( false === $arrURL )
            throw new Exception(sprintf('Malformed URL: %s'), $url);

        if ( 'http' != $arrURL['scheme'] && 'https' != $arrURL['scheme'] )
            $url = preg_replace('|^' . preg_quote($arrURL['scheme'], '|') . '|', 'http', $url);

        // Convert Header array to string.
        $strHeaders = '';
        if ( is_array( $r['headers'] ) )
            foreach ( $r['headers'] as $name => $value )
                $strHeaders .= "{$name}: $value\r\n";
        else if ( is_string( $r['headers'] ) )
            $strHeaders = $r['headers'];

        $is_local = isset($args['local']) && $args['local'];
        $ssl_verify = isset($args['sslverify']) && $args['sslverify'];
        if ( $is_local )
            $ssl_verify = $ssl_verify;
        elseif ( ! $is_local )
            $ssl_verify = $ssl_verify;

        $arrContext = array('http' =>
            array(
                'method' => strtoupper($r['method']),
                'user_agent' => $r['user-agent'],
                'max_redirects' => $r['redirection'] + 1, // See #11557
                'protocol_version' => (float) $r['httpversion'],
                'header' => $strHeaders,
                'ignore_errors' => true, // Return non-200 requests.
                'timeout' => $r['timeout'],
                'ssl' => array(
                        'verify_peer' => $ssl_verify,
                        'verify_host' => $ssl_verify
                )
            )
        );

        $proxy = new org_wordpress_HttpClient_Proxy();

        if ( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) ) {
            $arrContext['http']['proxy'] = 'tcp://' . $proxy->host() . ':' . $proxy->port();
            $arrContext['http']['request_fulluri'] = true;

            // We only support Basic authentication so this will only work if that is what your proxy supports.
            if ( $proxy->use_authentication() )
                $arrContext['http']['header'] .= $proxy->authentication_header() . "\r\n";
        }

        if ( 'HEAD' == $r['method'] ) // Disable redirects for HEAD requests
            $arrContext['http']['max_redirects'] = 1;

        if ( ! empty($r['body'] ) )
            $arrContext['http']['content'] = $r['body'];

        $context = stream_context_create($arrContext);

            $handle = @fopen($url, 'r', false, $context);

        if ( ! $handle )
            throw new Exception(sprintf('Could not open handle for fopen() to %s'), $url);

        $timeout = (int) floor( $r['timeout'] );
        $utimeout = $timeout == $r['timeout'] ? 0 : 1000000 * $r['timeout'] % 1000000;
        stream_set_timeout( $handle, $timeout, $utimeout );

        if ( ! $r['blocking'] ) {
            stream_set_blocking($handle, 0);
            fclose($handle);
            return array( 'headers' => array(), 'body' => '', 'response' => array('code' => false, 'message' => false), 'cookies' => array() );
        }

        $strResponse = stream_get_contents($handle);
        $meta = stream_get_meta_data($handle);

        fclose($handle);

        $processedHeaders = array();
        if ( isset($meta['wrapper_data']['headers'] ) )
            $processedHeaders = org_wordpress_HttpClient::processHeaders($meta['wrapper_data']['headers']);
        else
            $processedHeaders = org_wordpress_HttpClient::processHeaders($meta['wrapper_data']);

        if ( ! empty( $strResponse ) && isset($processedHeaders['headers']['transfer-encoding'] ) && 'chunked' == $processedHeaders['headers']['transfer-encoding'] )
            $strResponse = org_wordpress_HttpClient::chunkTransferDecode($strResponse);

        if ( true === $r['decompress'] && true === org_wordpress_HttpClient_Encoding::should_decode($processedHeaders['headers']) )
            $strResponse = org_wordpress_HttpClient_Encoding::decompress($strResponse );

        return array('headers' => $processedHeaders['headers'], 'body' => $strResponse, 'response' => $processedHeaders['response'], 'cookies' => $processedHeaders['cookies']);
    }

    /**
     * Whether this class can be used for retrieving an URL.
     *
     * @static
     * @access public
     * @since 2.7.0
     *
     * @return boolean False means this class can not be used, true means it can.
     */
    function test($args = array()) {
        if ( ! function_exists('fopen') || (function_exists('ini_get') && true != ini_get('allow_url_fopen')) )
            return false;

        if ( version_compare(PHP_VERSION, '5.0', '<') )
            return false;

        //HTTPS via Proxy was added in 5.1.0
        $is_ssl = isset($args['ssl']) && $args['ssl'];
        if ( $is_ssl && version_compare(PHP_VERSION, '5.1.0', '<') ) {
            $proxy = new org_wordpress_HttpClient_Proxy();
            /**
             * No URL check, as its not currently passed to the ::test() function
             * In the case where a Proxy is in use, Just bypass this transport for HTTPS.
             */
            if ( $proxy->is_enabled() )
                return false;
        }

        return true;
    }
}

/**
 * HTTP request method uses HTTP extension to retrieve the url.
 *
 * Requires the HTTP extension to be installed. This would be the preferred transport since it can
 * handle a lot of the problems that forces the others to use the HTTP version 1.0. Even if PHP 5.2+
 * is being used, it doesn't mean that the HTTP extension will be enabled.
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 2.7.0
 */
class org_wordpress_HttpClient_ExtHTTP {
    /**
     * Send a HTTP request to a URI using HTTP extension.
     *
     * Does not support non-blocking.
     *
     * @access public
     * @since 2.7
     *
     * @param string $url
     * @param str|array $args Optional. Override the defaults.
     * @return array 'headers', 'body', 'cookies' and 'response' keys.
     */
    function request($url, $args = array()) {
        $defaults = array(
            'method' => 'GET', 'timeout' => 5,
            'redirection' => 5, 'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(), 'body' => null, 'cookies' => array()
        );

        $r = array_merge($defaults, $args );

        if ( isset($r['headers']['User-Agent']) ) {
            $r['user-agent'] = $r['headers']['User-Agent'];
            unset($r['headers']['User-Agent']);
        } else if ( isset($r['headers']['user-agent']) ) {
            $r['user-agent'] = $r['headers']['user-agent'];
            unset($r['headers']['user-agent']);
        }

        // Construct Cookie: header if any cookies are set
        org_wordpress_HttpClient::buildCookieHeader( $r );

        switch ( $r['method'] ) {
            case 'POST':
                $r['method'] = HTTP_METH_POST;
                break;
            case 'HEAD':
                $r['method'] = HTTP_METH_HEAD;
                break;
            case 'PUT':
                $r['method'] =  HTTP_METH_PUT;
                break;
            case 'GET':
            default:
                $r['method'] = HTTP_METH_GET;
        }

        $arrURL = parse_url($url);

        if ( 'http' != $arrURL['scheme'] && 'https' != $arrURL['scheme'] )
            $url = preg_replace('|^' . preg_quote($arrURL['scheme'], '|') . '|', 'http', $url);

        $is_local = isset($args['local']) && $args['local'];
        $ssl_verify = isset($args['sslverify']) && $args['sslverify'];
        if ( $is_local )
            $ssl_verify = $ssl_verify;
        elseif ( ! $is_local )
            $ssl_verify = $ssl_verify;

        $r['timeout'] = (int) ceil( $r['timeout'] );

        $options = array(
            'timeout' => $r['timeout'],
            'connecttimeout' => $r['timeout'],
            'redirect' => $r['redirection'],
            'useragent' => $r['user-agent'],
            'headers' => $r['headers'],
            'ssl' => array(
                'verifypeer' => $ssl_verify,
                'verifyhost' => $ssl_verify
            )
        );

        if ( HTTP_METH_HEAD == $r['method'] )
            $options['redirect'] = 0; // Assumption: Docs seem to suggest that this means do not follow. Untested.

        // The HTTP extensions offers really easy proxy support.
        $proxy = new org_wordpress_HttpClient_Proxy();

        if ( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) ) {
            $options['proxyhost'] = $proxy->host();
            $options['proxyport'] = $proxy->port();
            $options['proxytype'] = HTTP_PROXY_HTTP;

            if ( $proxy->use_authentication() ) {
                $options['proxyauth'] = $proxy->authentication();
                $options['proxyauthtype'] = HTTP_AUTH_ANY;
            }
        }

            $strResponse = @http_request($r['method'], $url, $r['body'], $options, $info);

        // Error may still be set, Response may return headers or partial document, and error
        // contains a reason the request was aborted, eg, timeout expired or max-redirects reached.
        if ( false === $strResponse || ! empty($info['error']) )
            throw new Exception($info['response_code'] . ': ' . $info['error']);

        if ( ! $r['blocking'] )
            return array( 'headers' => array(), 'body' => '', 'response' => array('code' => false, 'message' => false), 'cookies' => array() );

        $headers_body = org_wordpress_HttpClient::processResponse($strResponse);
        $theHeaders = $headers_body['headers'];
        $theBody = $headers_body['body'];
        unset($headers_body);

        $theHeaders = org_wordpress_HttpClient::processHeaders($theHeaders);

        if ( ! empty( $theBody ) && isset($theHeaders['headers']['transfer-encoding'] ) && 'chunked' == $theHeaders['headers']['transfer-encoding'] ) {
                $theBody = @http_chunked_decode($theBody);
        }

        if ( true === $r['decompress'] && true === org_wordpress_HttpClient_Encoding::should_decode($theHeaders['headers']) )
            $theBody = http_inflate( $theBody );

        $theResponse = array();
        $theResponse['code'] = $info['response_code'];
        $theResponse['message'] = get_status_header_desc($info['response_code']);

        return array('headers' => $theHeaders['headers'], 'body' => $theBody, 'response' => $theResponse, 'cookies' => $theHeaders['cookies']);
    }

    /**
     * Whether this class can be used for retrieving an URL.
     *
     * @static
     * @since 2.7.0
     *
     * @return boolean False means this class can not be used, true means it can.
     */
    function test($args = array()) {
        return function_exists('http_request');
    }
}

/**
 * HTTP request method uses Curl extension to retrieve the url.
 *
 * Requires the Curl extension to be installed.
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 2.7
 */
class org_wordpress_HttpClient_Curl {

    /**
     * Send a HTTP request to a URI using cURL extension.
     *
     * @access public
     * @since 2.7.0
     *
     * @param string $url
     * @param str|array $args Optional. Override the defaults.
     * @return array 'headers', 'body', 'cookies' and 'response' keys.
     */
    function request($url, $args = array()) {
        $defaults = array(
            'method' => 'GET', 'timeout' => 5,
            'redirection' => 5, 'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(), 'body' => null, 'cookies' => array()
        );

        $r = array_merge($defaults, $args );

        if ( isset($r['headers']['User-Agent']) ) {
            $r['user-agent'] = $r['headers']['User-Agent'];
            unset($r['headers']['User-Agent']);
        } else if ( isset($r['headers']['user-agent']) ) {
            $r['user-agent'] = $r['headers']['user-agent'];
            unset($r['headers']['user-agent']);
        }

        // Construct Cookie: header if any cookies are set.
        org_wordpress_HttpClient::buildCookieHeader( $r );

        $handle = curl_init();

        // cURL offers really easy proxy support.
        $proxy = new org_wordpress_HttpClient_Proxy();

        if ( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) ) {

            $isPHP5 = version_compare(PHP_VERSION, '5.0.0', '>=');

            if ( $isPHP5 ) {
                curl_setopt( $handle, CURLOPT_PROXYTYPE, CURLPROXY_HTTP );
                curl_setopt( $handle, CURLOPT_PROXY, $proxy->host() );
                curl_setopt( $handle, CURLOPT_PROXYPORT, $proxy->port() );
            } else {
                curl_setopt( $handle, CURLOPT_PROXY, $proxy->host() .':'. $proxy->port() );
            }

            if ( $proxy->use_authentication() ) {
                if ( $isPHP5 )
                    curl_setopt( $handle, CURLOPT_PROXYAUTH, CURLAUTH_ANY );

                curl_setopt( $handle, CURLOPT_PROXYUSERPWD, $proxy->authentication() );
            }
        }

        $is_local = isset($args['local']) && $args['local'];
        $ssl_verify = isset($args['sslverify']) && $args['sslverify'];
        if ( $is_local )
            $ssl_verify = $ssl_verify;
        elseif ( ! $is_local )
            $ssl_verify = $ssl_verify;


        // CURLOPT_TIMEOUT and CURLOPT_CONNECTTIMEOUT expect integers.  Have to use ceil since
        // a value of 0 will allow an ulimited timeout.
        $timeout = (int) ceil( $r['timeout'] );
        curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, $timeout );
        curl_setopt( $handle, CURLOPT_TIMEOUT, $timeout );

        curl_setopt( $handle, CURLOPT_URL, $url);
        curl_setopt( $handle, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $handle, CURLOPT_SSL_VERIFYHOST, $ssl_verify );
        curl_setopt( $handle, CURLOPT_SSL_VERIFYPEER, $ssl_verify );
        curl_setopt( $handle, CURLOPT_USERAGENT, $r['user-agent'] );
        curl_setopt( $handle, CURLOPT_MAXREDIRS, $r['redirection'] );

        switch ( $r['method'] ) {
            case 'HEAD':
                curl_setopt( $handle, CURLOPT_NOBODY, true );
                break;
            case 'POST':
                curl_setopt( $handle, CURLOPT_POST, true );
                curl_setopt( $handle, CURLOPT_POSTFIELDS, $r['body'] );
                break;
            case 'PUT':
                curl_setopt( $handle, CURLOPT_CUSTOMREQUEST, 'PUT' );
                curl_setopt( $handle, CURLOPT_POSTFIELDS, $r['body'] );
                break;
        }

        if ( true === $r['blocking'] )
            curl_setopt( $handle, CURLOPT_HEADER, true );
        else
            curl_setopt( $handle, CURLOPT_HEADER, false );

        // The option doesn't work with safe mode or when open_basedir is set.
        // Disable HEAD when making HEAD requests.
        if ( !ini_get('safe_mode') && !ini_get('open_basedir') && 'HEAD' != $r['method'] )
            curl_setopt( $handle, CURLOPT_FOLLOWLOCATION, true );

        if ( !empty( $r['headers'] ) ) {
            // cURL expects full header strings in each element
            $headers = array();
            foreach ( $r['headers'] as $name => $value ) {
                $headers[] = "{$name}: $value";
            }
            curl_setopt( $handle, CURLOPT_HTTPHEADER, $headers );
        }

        if ( $r['httpversion'] == '1.0' )
            curl_setopt( $handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
        else
            curl_setopt( $handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );

        // We don't need to return the body, so don't. Just execute request and return.
        if ( ! $r['blocking'] ) {
            curl_exec( $handle );
            curl_close( $handle );
            return array( 'headers' => array(), 'body' => '', 'response' => array('code' => false, 'message' => false), 'cookies' => array() );
        }

        $theResponse = curl_exec( $handle );

        if ( !empty($theResponse) ) {
            $headerLength = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
            $theHeaders = trim( substr($theResponse, 0, $headerLength) );
            if ( strlen($theResponse) > $headerLength )
                $theBody = substr( $theResponse, $headerLength );
            else
                $theBody = '';
            if ( false !== strrpos($theHeaders, "\r\n\r\n") ) {
                $headerParts = explode("\r\n\r\n", $theHeaders);
                $theHeaders = $headerParts[ count($headerParts) -1 ];
            }
            $theHeaders = org_wordpress_HttpClient::processHeaders($theHeaders);
        } else {
            if ( $curl_error = curl_error($handle) )
                throw new Exception($curl_error);
            if ( in_array( curl_getinfo( $handle, CURLINFO_HTTP_CODE ), array(301, 302) ) )
                throw new Exception('Too many redirects.');

            $theHeaders = array( 'headers' => array(), 'cookies' => array() );
            $theBody = '';
        }

        $response = array();
        $response['code'] = curl_getinfo( $handle, CURLINFO_HTTP_CODE );
        $response['message'] = get_status_header_desc($response['code']);

        curl_close( $handle );

        // See #11305 - When running under safe mode, redirection is disabled above. Handle it manually.
        if ( !empty($theHeaders['headers']['location']) && (ini_get('safe_mode') || ini_get('open_basedir')) ) {
            if ( $r['redirection']-- > 0 ) {
                return $this->request($theHeaders['headers']['location'], $r);
            } else {
                throw new Exception('Too many redirects.');
            }
        }

        if ( true === $r['decompress'] && true === org_wordpress_HttpClient_Encoding::should_decode($theHeaders['headers']) )
            $theBody = org_wordpress_HttpClient_Encoding::decompress($theBody );

        return array('headers' => $theHeaders['headers'], 'body' => $theBody, 'response' => $response, 'cookies' => $theHeaders['cookies']);
    }

    /**
     * Whether this class can be used for retrieving an URL.
     *
     * @static
     * @since 2.7.0
     *
     * @return boolean False means this class can not be used, true means it can.
     */
    function test($args = array()) {
        if ( function_exists('curl_init') && function_exists('curl_exec') )
            return true;

        return false;
    }
}

/**
 * Internal representation of a single cookie.
 *
 * Returned cookies are represented using this class, and when cookies are set, if they are not
 * already a org_wordpress_HttpClient_Cookie() object, then they are turned into one.
 *
 * @todo The WordPress convention is to use underscores instead of camelCase for function and method
 * names. Need to switch to use underscores instead for the methods.
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 2.8.0
 */
class org_wordpress_HttpClient_Cookie {

    /**
     * Cookie name.
     *
     * @since 2.8.0
     * @var string
     */
    var $name;

    /**
     * Cookie value.
     *
     * @since 2.8.0
     * @var string
     */
    var $value;

    /**
     * When the cookie expires.
     *
     * @since 2.8.0
     * @var string
     */
    var $expires;

    /**
     * Cookie URL path.
     *
     * @since 2.8.0
     * @var string
     */
    var $path;

    /**
     * Cookie Domain.
     *
     * @since 2.8.0
     * @var string
     */
    var $domain;

    /**
     * PHP4 style Constructor - Calls PHP5 Style Constructor.
     *
     * @access public
     * @since 2.8.0
     * @param string|array $data Raw cookie data.
     */
    function org_wordpress_HttpClient_Cookie( $data ) {
        $this->__construct( $data );
    }

    /**
     * Sets up this cookie object.
     *
     * The parameter $data should be either an associative array containing the indices names below
     * or a header string detailing it.
     *
     * If it's an array, it should include the following elements:
     * <ol>
     * <li>Name</li>
     * <li>Value - should NOT be urlencoded already.</li>
     * <li>Expires - (optional) String or int (UNIX timestamp).</li>
     * <li>Path (optional)</li>
     * <li>Domain (optional)</li>
     * </ol>
     *
     * @access public
     * @since 2.8.0
     *
     * @param string|array $data Raw cookie data.
     */
    function __construct( $data ) {
        if ( is_string( $data ) ) {
            // Assume it's a header string direct from a previous request
            $pairs = explode( ';', $data );

            // Special handling for first pair; name=value. Also be careful of "=" in value
            $name  = trim( substr( $pairs[0], 0, strpos( $pairs[0], '=' ) ) );
            $value = substr( $pairs[0], strpos( $pairs[0], '=' ) + 1 );
            $this->name  = $name;
            $this->value = urldecode( $value );
            array_shift( $pairs ); //Removes name=value from items.

            // Set everything else as a property
            foreach ( $pairs as $pair ) {
                $pair = rtrim($pair);
                if ( empty($pair) ) //Handles the cookie ending in ; which results in a empty final pair
                    continue;

                list( $key, $val ) = strpos( $pair, '=' ) ? explode( '=', $pair ) : array( $pair, '' );
                $key = strtolower( trim( $key ) );
                if ( 'expires' == $key )
                    $val = strtotime( $val );
                $this->$key = $val;
            }
        } else {
            if ( !isset($data['name'] ) )
                return false;

            // Set properties based directly on parameters
            $this->name   = $data['name'];
            $this->value  = isset($data['value'] ) ? $data['value'] : '';
            $this->path   = isset($data['path'] ) ? $data['path'] : '';
            $this->domain = isset($data['domain'] ) ? $data['domain'] : '';

            if ( isset($data['expires'] ) )
                $this->expires = is_int( $data['expires'] ) ? $data['expires'] : strtotime( $data['expires'] );
            else
                $this->expires = null;
        }
    }

    /**
     * Confirms that it's OK to send this cookie to the URL checked against.
     *
     * Decision is based on RFC 2109/2965, so look there for details on validity.
     *
     * @access public
     * @since 2.8.0
     *
     * @param string $url URL you intend to send this cookie to
     * @return boolean TRUE if allowed, FALSE otherwise.
     */
    function test( $url ) {
        // Expires - if expired then nothing else matters
        if ( time() > $this->expires )
            return false;

        // Get details on the URL we're thinking about sending to
        $url = parse_url( $url );
        $url['port'] = isset($url['port'] ) ? $url['port'] : 80;
        $url['path'] = isset($url['path'] ) ? $url['path'] : '/';

        // Values to use for comparison against the URL
        $path   = isset($this->path )   ? $this->path   : '/';
        $port   = isset($this->port )   ? $this->port   : 80;
        $domain = isset($this->domain ) ? strtolower( $this->domain ) : strtolower( $url['host'] );
        if ( false === stripos( $domain, '.' ) )
            $domain .= '.local';

        // Host - very basic check that the request URL ends with the domain restriction (minus leading dot)
        $domain = substr( $domain, 0, 1 ) == '.' ? substr( $domain, 1 ) : $domain;
        if ( substr( $url['host'], -strlen( $domain ) ) != $domain )
            return false;

        // Port - supports "port-lists" in the format: "80,8000,8080"
        if ( !in_array( $url['port'], explode( ',', $port) ) )
            return false;

        // Path - request path must start with path restriction
        if ( substr( $url['path'], 0, strlen( $path ) ) != $path )
            return false;

        return true;
    }

    /**
     * Convert cookie name and value back to header string.
     *
     * @access public
     * @since 2.8.0
     *
     * @return string Header encoded cookie name and value.
     */
    function getHeaderValue() {
        if ( empty( $this->name ) || empty( $this->value ) )
            return '';

        return $this->name . '=' . urlencode( $this->value );
    }

    /**
     * Retrieve cookie header for usage in the rest of the WordPress HTTP API.
     *
     * @access public
     * @since 2.8.0
     *
     * @return string
     */
    function getFullHeader() {
        return 'Cookie: ' . $this->getHeaderValue();
    }
}

/**
 * Implementation for deflate and gzip transfer encodings.
 *
 * Includes RFC 1950, RFC 1951, and RFC 1952.
 *
 * @since 2.8
 * @package WordPress
 * @subpackage HTTP
 */
class org_wordpress_HttpClient_Encoding {

    /**
     * Compress raw string using the deflate format.
     *
     * Supports the RFC 1951 standard.
     *
     * @since 2.8
     *
     * @param string $raw String to compress.
     * @param int $level Optional, default is 9. Compression level, 9 is highest.
     * @param string $supports Optional, not used. When implemented it will choose the right compression based on what the server supports.
     * @return string|bool False on failure.
     */
    function compress( $raw, $level = 9, $supports = null ) {
        return gzdeflate( $raw, $level );
    }

    /**
     * Decompression of deflated string.
     *
     * Will attempt to decompress using the RFC 1950 standard, and if that fails
     * then the RFC 1951 standard deflate will be attempted. Finally, the RFC
     * 1952 standard gzip decode will be attempted. If all fail, then the
     * original compressed string will be returned.
     *
     * @since 2.8
     *
     * @param string $compressed String to decompress.
     * @param int $length The optional length of the compressed data.
     * @return string|bool False on failure.
     */
    function decompress($compressed, $length = null ) {

        if ( empty($compressed) )
            return $compressed;

        if ( false !== ( $decompressed = @gzinflate( $compressed ) ) )
            return $decompressed;

        if ( false !== ( $decompressed = org_wordpress_HttpClient_Encoding::compatible_gzinflate( $compressed ) ) )
            return $decompressed;

        if ( false !== ( $decompressed = @gzuncompress( $compressed ) ) )
            return $decompressed;

        if ( function_exists('gzdecode') ) {
            $decompressed = @gzdecode( $compressed );

            if ( false !== $decompressed )
                return $decompressed;
        }

        return $compressed;
    }

    /**
     * Decompression of deflated string while staying compatible with the majority of servers.
     *
     * Certain Servers will return deflated data with headers which PHP's gziniflate()
     * function cannot handle out of the box. The following function lifted from
     * http://au2.php.net/manual/en/function.gzinflate.php#77336 will attempt to deflate
     * the various return forms used.
     *
     * @since 2.8.1
     * @link http://au2.php.net/manual/en/function.gzinflate.php#77336
     *
     * @param string $gzData String to decompress.
     * @return string|bool False on failure.
     */
    function compatible_gzinflate($gzData) {
        if ( substr($gzData, 0, 3) == "\x1f\x8b\x08" ) {
            $i = 10;
            $flg = ord( substr($gzData, 3, 1) );
            if ( $flg > 0 ) {
                if ( $flg & 4 ) {
                    list($xlen) = unpack('v', substr($gzData, $i, 2) );
                    $i = $i + 2 + $xlen;
                }
                if ( $flg & 8 )
                    $i = strpos($gzData, "\0", $i) + 1;
                if ( $flg & 16 )
                    $i = strpos($gzData, "\0", $i) + 1;
                if ( $flg & 2 )
                    $i = $i + 2;
            }
            return gzinflate( substr($gzData, $i, -8) );
        } else {
            return false;
        }
    }

    /**
     * What encoding types to accept and their priority values.
     *
     * @since 2.8
     *
     * @return string Types of encoding to accept.
     */
    function accept_encoding() {
        $type = array();
        if ( function_exists( 'gzinflate' ) )
            $type[] = 'deflate;q=1.0';

        if ( function_exists( 'gzuncompress' ) )
            $type[] = 'compress;q=0.5';

        if ( function_exists( 'gzdecode' ) )
            $type[] = 'gzip;q=0.5';

        return implode(', ', $type);
    }

    /**
     * What enconding the content used when it was compressed to send in the headers.
     *
     * @since 2.8
     *
     * @return string Content-Encoding string to send in the header.
     */
    function content_encoding() {
        return 'deflate';
    }

    /**
     * Whether the content be decoded based on the headers.
     *
     * @since 2.8
     *
     * @param array|string $headers All of the available headers.
     * @return bool
     */
    function should_decode($headers) {
        if ( is_array( $headers ) ) {
            if ( array_key_exists('content-encoding', $headers) && ! empty( $headers['content-encoding'] ) )
                return true;
        } else if ( is_string( $headers ) ) {
            return ( stripos($headers, 'content-encoding:') !== false );
        }

        return false;
    }

    /**
     * Whether decompression and compression are supported by the PHP version.
     *
     * Each function is tested instead of checking for the zlib extension, to
     * ensure that the functions all exist in the PHP version and aren't
     * disabled.
     *
     * @since 2.8
     *
     * @return bool
     */
    function is_available() {
        return ( function_exists('gzuncompress') || function_exists('gzdeflate') || function_exists('gzinflate') );
    }
}
