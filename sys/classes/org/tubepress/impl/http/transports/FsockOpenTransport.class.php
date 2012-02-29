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
 * HTTP request method uses fsockopen function to retrieve the url.
 *
 * This would be the preferred method, but the fsockopen implementation has the most overhead of all
 * the HTTP transport implementations.
 *
 */
class org_tubepress_impl_http_transports_FsockOpenTransport extends org_tubepress_impl_http_transports_AbstractHttpTransport
{
    private $_handle;

    private $_rawMessage;

    /**
     * Perform handling of the given request.
     *
     * @param org_tubepress_api_http_HttpRequest $request The HTTP request.
     *
     * @return void
     */
    protected function handleRequest(org_tubepress_api_http_HttpRequest $request)
    {
        $url  = $request->getUrl();
        $port = $url->getPort() === null ? 80 : $url->getPort();
        $host = $url->getHost();

        //fsockopen has issues with 'localhost' with IPv6 with certain versions of PHP, It attempts to connect to ::1,
        // which fails when the server is not set up for it. For compatibility, always connect to the IPv4 address.
        if ('localhost' == strtolower($host)) {

            $fsockopen_host = '127.0.0.1';
        }

        org_tubepress_impl_log_Log::log($this->logPrefix(), 'Now calling fsockopen()...');

        $this->_handle = @fsockopen("$host:$port", $port, $iError, $strError, 5);

        if (false === $this->_handle) {

            throw new Exception($iError . ': ' . $strError);
        }

        org_tubepress_impl_log_Log::log($this->logPrefix(), 'Successfully opened handle');

        fwrite($this->_handle, self::_buildHeaderString($request));

        stream_set_timeout($this->_handle, 5);

        org_tubepress_impl_log_Log::log($this->logPrefix(), 'Reading response...');

        $rawResponse = '';
        while (! feof($this->_handle)) {

            $rawResponse .= fread($this->_handle, 4096);
        }

        org_tubepress_impl_log_Log::log($this->logPrefix(), 'Done reading response');

        $this->_rawMessage = $rawResponse;

        return $rawResponse;
    }

    /**
     * Get the name of this transport.
     *
     * @return string The name of this transport.
     */
    protected function getTransportName()
    {
        return 'fsockopen()';
    }

    /**
     * Get the response code.
     *
     * @return int the HTTP response code.
     */
    protected function getResponseCode()
    {
        $lines     = explode("\n", $this->_rawMessage);
        $firstLine = $lines[0];

        $pieces = explode(" ", $firstLine);
        return $pieces[1];
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
        unset($this->_rawMessage);
    }

    /**
     * Determines whether or not this transport is available on the system.
     *
     * @return bool True if this transport is available on the system. False otherwise.
     */
    public function isAvailable()
    {
        if (! function_exists('fsockopen')) {

            org_tubepress_impl_log_Log::log($this->logPrefix(), 'fsockopen() does not exist');
            return false;
        }

        return true;
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

    private static function _buildHeaderString(org_tubepress_api_http_HttpRequest $request)
    {
        $url         = $request->getUrl();
        $path        = $url->getPath();
        $query       = $url->getQuery();
        $host        = $url->getHost();
        $entity      = $request->getEntity();
        $headerArray = $request->getAllHeaders();
        $toRequest   = '/';

        if ($path !== null) {

            $toRequest = $path;
        }

        if ($query !== null) {

            $toRequest .= '?' . $query;
        }

        /** Use HTTP 1.0 unless you want this to run SLOW. */
        $strHeaders  = $request->getMethod() . " $toRequest HTTP/1.0\r\n";
        $strHeaders .= "Host: $host\r\n";

        foreach ($headerArray as $name => $value) {

            $strHeaders .= "$name: $value\r\n";
        }

        $strHeaders .= "\r\n";

        if ($entity !== null && $entity->getContent() !== null) {

            $strHeaders .= $entity->getContent();
        }

        return $strHeaders;
    }
}
