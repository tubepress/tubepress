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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_http_HttpMessage',
    'org_tubepress_api_url_Url',
));

/**
 * An HTTP request.
 */
class org_tubepress_api_http_HttpRequest extends org_tubepress_api_http_HttpMessage
{
    const _ = 'org_tubepress_api_http_HttpRequest';

    const HTTP_HEADER_USER_AGENT      = 'User-Agent';
    const HTTP_HEADER_ACCEPT_ENCODING = 'Accept-Encoding';

    const HTTP_METHOD_GET  = 'GET';
    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_PUT  = 'PUT';

    private $_method;

    private $_url;

    /**
     * Constructor.
     *
     * @param string       $method The HTTP method.
     * @param unknown_type $url    The URL.
     */
    public function __construct($method, $url)
    {
        $this->setMethod($method);
        $this->setUrl($url);
    }

    /**
     * Get the HTTP method.
     *
     * @return string The HTTP method. One of GET, PUT, DELETE, or POST.
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * Sets the HTTP method.
     *
     * @param string $method The HTTP method.
     *
     * @throws Exception If the method is not a string matching GET, PUT, POST, or DELETE.
     *
     * @return void
     */
    public function setMethod($method)
    {
        if (preg_match('/get|post|put|delete/i', $method, $matches) !== 1) {

            throw new Exception('Method must be PUT, GET, POST, or DELETE');
        }

        $this->_method = strtoupper($method);
    }

    /**
     * Get the URL of this request.
     *
     * @return org_tubepress_api_url_Url The URL of this request.
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Sets the URL of this request.
     *
     * @param unknown_type $url The URL of this request.
     *
     * @throws Exception If the given URL is not a valid string URL or instance of org_tubepress_api_url_Url
     *
     * @return void
     */
    public function setUrl($url)
    {
        if (is_string($url)) {

            $this->_url = new org_tubepress_api_url_Url($url);
            return;
        }

        if (! $url instanceof org_tubepress_api_url_Url) {

            throw new Exception('setUrl() only takes a string or a URL');
        }

        $this->_url = $url;
    }

    /**
     * Generate string representation of this request.
     *
     * @return string A string representation of this request.
     */
    public function toString()
    {
        return sprintf('%s to <a href="%s">URL</a>', $this->getMethod(), $this->getUrl());
    }

    /**
     * Delegates to toString();
     *
     * @return string A string representation of this request.
     */
    public function __toString()
    {
        return $this->toString();
    }
}
