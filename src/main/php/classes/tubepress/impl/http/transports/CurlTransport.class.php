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
    'org_tubepress_impl_http_clientimpl_HeaderUtils',
    'org_tubepress_impl_http_transports_AbstractHttpTransport',
    'org_tubepress_impl_http_HttpClientChain',
));

/**
 * Lifted from http://core.trac.wordpress.org/browser/tags/3.0.4/wp-includes/class-http.php
 *
 * HTTP request method uses Curl extension to retrieve the url.
 *
 * Requires the Curl extension to be installed.
 */
class org_tubepress_impl_http_transports_CurlTransport extends org_tubepress_impl_http_transports_AbstractHttpTransport
{
    private $_handle;

    /**
     * Determines whether or not this transport is available on the system.
     *
     * @return bool True if this transport is available on the system. False otherwise.
     */
    public function isAvailable()
    {
        if (! function_exists('curl_init')) {

            org_tubepress_impl_log_Log::log($this->logPrefix(), 'curl_init() does not exist');
            return false;
        }

        if (! function_exists('curl_exec')) {

            org_tubepress_impl_log_Log::log($this->logPrefix(), 'curl_exec() does not exist');
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
        return true;
    }

    /**
     * Perform optional setup to handle a new HTTP request.
     *
     * @param org_tubepress_api_http_HttpRequest $request The HTTP request to handle.
     *
     * @return void
     */
    protected function prepareToHandleNewRequest(org_tubepress_api_http_HttpRequest $request)
    {
        org_tubepress_impl_log_Log::log($this->logPrefix(), 'Initializing cURL');

        $this->_handle = curl_init();
        $this->_setCurlOptions($request);

        org_tubepress_impl_log_Log::log($this->logPrefix(), 'cURL initialized');
    }

    /**
     * Perform handling of the given request.
     *
     * @param org_tubepress_api_http_HttpRequest $request The HTTP request.
     *
     * @return void
     */
    protected function handleRequest(org_tubepress_api_http_HttpRequest $request)
    {
        org_tubepress_impl_log_Log::log($this->logPrefix(), 'Calling curl_exec()');

        $response = curl_exec($this->_handle);

        if ($response === false) {

            if ($curlError = curl_error($this->_handle)) {

                throw new Exception($curlError);
            }

            throw new Exception('cURL failed');
        }

        org_tubepress_impl_log_Log::log($this->logPrefix(), 'cURL returned a valid response');

        return $response;
    }

    /**
     * Get the name of this transport.
     *
     * @return string The name of this transport.
     */
    protected function getTransportName()
    {
        return 'cURL';
    }

    /**
     * Get the response code.
     *
     * @return int the HTTP response code.
     */
    protected function getResponseCode()
    {
        return curl_getinfo($this->_handle, CURLINFO_HTTP_CODE);
    }

    /**
     * Perform optional tear down after handling a request.
     *
     * @return void
     */
    protected function tearDown()
    {
        org_tubepress_impl_log_Log::log($this->logPrefix(), 'Closing cURL');

        if (isset($this->_handle)) {

            curl_close($this->_handle);
            unset($this->_handle);
        }
    }

    private function _setCurlOptions(org_tubepress_api_http_HttpRequest $request)
    {
        curl_setopt_array($this->_handle, array(

            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HEADER         => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_0,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_URL            => $request->getUrl()->toString(),
            CURLOPT_USERAGENT      => $request->getHeaderValue(org_tubepress_api_http_HttpRequest::HTTP_HEADER_USER_AGENT),

        ));

        $this->_setCurlOptionsFollowLocation();
        $this->_setCurlOptionsBody($request);
        $this->_setCurlOptionsHeaders($request);
    }

    private function _setCurlOptionsFollowLocation()
    {
        // The option doesn't work with safe mode or when open_basedir is set.
        // Disable HEAD when making HEAD requests.
        if (!ini_get('safe_mode') && !ini_get('open_basedir')) {

            curl_setopt($this->_handle, CURLOPT_FOLLOWLOCATION, true);
        }
    }

    private function _setCurlOptionsBody(org_tubepress_api_http_HttpRequest $request)
    {
        $body = $request->getEntity() === null ? null : $request->getEntity()->getContent();

        switch ($request->getMethod()) {

            case org_tubepress_api_http_HttpRequest::HTTP_METHOD_POST:

                curl_setopt($this->_handle, CURLOPT_POST, true);
                curl_setopt($this->_handle, CURLOPT_POSTFIELDS, $body);

                break;

            case org_tubepress_api_http_HttpRequest::HTTP_METHOD_PUT:

                curl_setopt($this->_handle, CURLOPT_CUSTOMREQUEST, org_tubepress_api_http_HttpRequest::HTTP_METHOD_PUT);
                curl_setopt($this->_handle, CURLOPT_POSTFIELDS, $body);

                break;
        }
    }

    private function _setCurlOptionsHeaders(org_tubepress_api_http_HttpRequest $request)
    {
        // cURL expects full header strings in each element
        $newHeaders = array();
        $headers    = $request->getAllHeaders();

        foreach ($headers as $name => $value) {

            $newHeaders[] = "{$name}: $value";
        }

        curl_setopt($this->_handle, CURLOPT_HTTPHEADER, $newHeaders);
    }
}
