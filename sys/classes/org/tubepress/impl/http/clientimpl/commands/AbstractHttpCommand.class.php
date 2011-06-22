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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_patterns_cor_Command',
    'org_tubepress_impl_http_clientimpl_Cookie',
));

/**
 * Lifted from http://core.trac.wordpress.org/browser/tags/3.0.4/wp-includes/class-http.php
 *
 * Base HTTP command.
 */
abstract class org_tubepress_impl_http_clientimpl_commands_AbstractHttpCommand implements org_tubepress_api_patterns_cor_Command
{
    private $_redirectCount;

    /**
     * Execute the command.
     *
     * @param array $context An array of context elements (may be empty).
     *
     * @return boolean True if this command was able to handle the execution. False otherwise.
     */
    public function execute($context)
    {
        if ($this->_canHandle($context->url, $context->args) === false) {
            return false;
        }

        $result = $this->_doExecute($context->url, $context->args);

        /* check response code */
        $code = $result['response']['code'];
        if ($code != 200) {
            throw new Exception("Request for " . $context->url . " returned a $code HTTP response: " . $result['response']['message']);
        }

        $context->returnValue = $result['body'];

        return true;
    }

    protected abstract function _doExecute($url, $args);

    protected abstract function _canHandle($url, $args);

    protected function _canRedirect()
    {
        return ++$this->_redirectCount < 5;
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
    protected static function _getProcessedHeaders($headers)
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
                    $cookies[] = new org_tubepress_impl_http_clientimpl_Cookie($value);
                }
            }
        }

        return array('response' => $response, 'headers' => $newheaders, 'cookies' => $cookies);
    }

    /**
     * Parses the responses and splits the parts into headers and body.
     *
     * @param string $strResponse The full response string
     *
     * @return array Array with 'headers' and 'body' keys.
     */
    protected static function _breakRawStringResponseIntoHeaderAndBody($strResponse)
    {
        $res = explode("\r\n\r\n", $strResponse, 2);

        return array('headers' => isset($res[0]) ? $res[0] : array(), 'body' => isset($res[1]) ? $res[1] : '');
    }

    /**
     * Takes the arguments for a ::request() and checks for the cookie array.
     *
     * If it's found, then it's assumed to contain org_tubepress_http_HttpClientChain_Cookie objects, which are each parsed
     * into strings and added to the Cookie: header (within the arguments array). Edits the array by
     * reference.
     *
     * @param array &$r Full array of args passed into ::request()
     *
     * @return void
     */
    protected static function _buildCookieHeader(&$r)
    {
        if (! empty($r[self::ARGS_COOKIES])) {
            $cookies_header = '';
            foreach ((array) $r[self::ARGS_COOKIES] as $cookie) {
                $cookies_header .= $cookie->getHeaderValue() . '; ';
            }
            $cookies_header         = substr($cookies_header, 0, -2);
            $r[self::ARGS_HEADERS]['cookie'] = $cookies_header;
        }
    }
}
