<?php
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
class org_wordpress_HttpClient_Streams
{
    /**
     * Send a HTTP request to a URI using streams with fopen().
     *
     * @param string $url
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

        $arrURL = parse_url($url);

        if (false === $arrURL) {
            throw new Exception(sprintf('Malformed URL: %s'), $url);
        }

        if ('http' != $arrURL['scheme'] && 'https' != $arrURL['scheme']) {
            $url = preg_replace('|^' . preg_quote($arrURL['scheme'], '|') . '|', 'http', $url);
        }

        // Convert Header array to string.
        $strHeaders = '';
        if (is_array($r['headers'])) {
            foreach ($r['headers'] as $name => $value) {
                $strHeaders .= "{$name}: $value\r\n";
            }
        } else if (is_string($r['headers'])) {
            $strHeaders = $r['headers'];
        }

        $is_local   = isset($args['local']) && $args['local'];
        $ssl_verify = isset($args['sslverify']) && $args['sslverify'];

        if ($is_local) {
            $ssl_verify = $ssl_verify;
        } elseif (! $is_local) {
            $ssl_verify = $ssl_verify;
        }

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

        if ('HEAD' == $r['method']) {// Disable redirects for HEAD requests
            $arrContext['http']['max_redirects'] = 1;
        }

        if (! empty($r['body'])) {
            $arrContext['http']['content'] = $r['body'];
        }

        $context = stream_context_create($arrContext);

        $handle = @fopen($url, 'r', false, $context);

        if (! $handle) {
            throw new Exception(sprintf('Could not open handle for fopen() to %s'), $url);
        }

        $timeout  = (int) floor($r['timeout']);
        $utimeout = $timeout == $r['timeout'] ? 0 : 1000000 * $r['timeout'] % 1000000;
        stream_set_timeout($handle, $timeout, $utimeout);

        if (! $r['blocking']) {
            stream_set_blocking($handle, 0);
            fclose($handle);
            return array('headers' => array(), 'body' => '', 'response' => array('code' => false, 'message' => false), 'cookies' => array());
        }

        $strResponse = stream_get_contents($handle);
        $meta        = stream_get_meta_data($handle);

        fclose($handle);

        $processedHeaders = array();
        if (isset($meta['wrapper_data']['headers'])) {
            $processedHeaders = org_wordpress_HttpClient::processHeaders($meta['wrapper_data']['headers']);
        } else {
            $processedHeaders = org_wordpress_HttpClient::processHeaders($meta['wrapper_data']);
        }

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
     * @param array $args The optional args.
     *
     * @return boolean False means this class can not be used, true means it can.
     */
    function test($args = array())
    {
        if (! function_exists('fopen') || (function_exists('ini_get') && true != ini_get('allow_url_fopen'))) {
            return false;
        }

        if (version_compare(PHP_VERSION, '5.0', '<')) {
            return false;
        }

        return true;
    }
}
