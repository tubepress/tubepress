<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
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
    'org_tubepress_impl_http_transports_AbstractHttpTransport',
    'org_tubepress_impl_http_HttpClientChain',
));

/**
 * Lifted from http://core.trac.wordpress.org/browser/tags/3.0.4/wp-includes/class-http.php
 *
 * HTTP request method uses fopen function to retrieve the url.
 *
 * Does not allow for $context support,
 * but should still be okay, to write the headers, before getting the response. Also requires that
 * 'allow_url_fopen' to be enabled.
 *
 */
class org_tubepress_impl_http_transports_FopenTransport extends org_tubepress_impl_http_transports_AbstractHttpTransport
{
    private static $_fopen_readonly = 'r';

    private static $_meta_wrapper      = 'wrapper_data';
    private static $_meta_wrapper_info = 'headers';

    private $_handle;

    /**
    * Perform handling of the given request.
    *
    * @param org_tubepress_api_http_HttpRequest $request The HTTP request.
    *
    * @return void
    */
    protected function handleRequest(org_tubepress_api_http_HttpRequest $request)
    {
        org_tubepress_impl_log_Log::log($this->logPrefix(), 'Calling fopen()...');

        $this->_handle = @fopen($request->getUrl()->toString(), self::$_fopen_readonly);

        if ($this->_handle === false) {

            throw new Exception(sprintf('Could not open handle for fopen() to %s', $request->getUrl()));
        }

        org_tubepress_impl_log_Log::log($this->logPrefix(), 'Successfully opened stream');

        stream_set_timeout($this->_handle, 5);

        $rawContent = '';

        while (! feof($this->_handle)) {

            $rawContent .= fread($this->_handle, 4096);
        }

        org_tubepress_impl_log_Log::log($this->logPrefix(), 'Done reading stream');

        if (function_exists('stream_get_meta_data')) {

            org_tubepress_impl_log_Log::log($this->logPrefix(), 'Asking for stream metadata');

            $meta = stream_get_meta_data($this->_handle);

            $rawHeaders = $meta[self::$_meta_wrapper];

            if (isset($meta[self::$_meta_wrapper][self::$_meta_wrapper_info])) {

                $rawHeaders = $meta[self::$_meta_wrapper][self::$_meta_wrapper_info];
            }

        } else {

            org_tubepress_impl_log_Log::log($this->logPrefix(), 'stream_get_meta_data() does not exist');

            //$http_response_header is a PHP reserved variable which is set in the current-scope when using the HTTP Wrapper
            //see http://php.oregonstate.edu/manual/en/reserved.variables.httpresponseheader.php
            $rawHeaders = $http_response_header;
        }

        return implode("\r\n", $rawHeaders) . "\r\n\r\n" . $rawContent;
    }

    /**
     * Get the name of this transport.
     *
     * @return string The name of this transport.
     */
    protected function getTransportName()
    {
        return 'fopen()';
    }

    /**
     * Get the response code.
     *
     * @return int the HTTP response code.
     */
    protected function getResponseCode()
    {
        /* fopen will bail on any non-200 code */
        return 200;
    }

    /**
     * Perform optional tear down after handling a request.
     *
     * @return void
     */
    protected function tearDown()
    {
        @fclose($this->_handle);
        unset($this->_handle);
    }

    /**
     * Determines whether or not this transport is available on the system.
     *
     * @return bool True if this transport is available on the system. False otherwise.
     */
    public function isAvailable()
    {
        if (! function_exists('fopen')) {

            org_tubepress_impl_log_Log::log($this->logPrefix(), 'fopen() does not exist');
            return false;
        }

        if (function_exists('ini_get') && ini_get('allow_url_fopen') != true) {

            org_tubepress_impl_log_Log::log($this->logPrefix(), 'allow_url_fopen is set to false');
            return false;
        }
    }

    /**
     * Determines if this transport can handle the given request.
     *
     * @param org_tubepress_api_http_HttpRequest $request The request to handle.
     *
     * @return bool True if this transport can handle the given request. False otherwise.
     */
    public function canHandle(org_tubepress_api_http_HttpRequest $request)
    {
        return $request->getMethod() === org_tubepress_api_http_HttpRequest::HTTP_METHOD_GET;
    }
}

