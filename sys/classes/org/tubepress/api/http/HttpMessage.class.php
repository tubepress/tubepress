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
    'org_tubepress_api_http_HttpEntity',
));

/**
 * An HTTP message.
 */
abstract class org_tubepress_api_http_HttpMessage
{
    const HTTP_HEADER_HTTP_VERSION     = 'HTTP-Version';
    const HTTP_HEADER_CONTENT_LENGTH   = 'Content-Length';
    const HTTP_HEADER_CONTENT_ENCODING = 'Content-Encoding';
    const HTTP_HEADER_CONTENT_TYPE     = 'Content-Type';

    const _ = 'org_tubepress_api_http_HttpMessage';

    private $_headers = array();

    private $_entity;

    /**
     * Set the message entity.
     *
     * @param org_tubepress_api_http_HttpEntity $entity The entity.
     *
     * @throws Exception If entity is not of type org_tubepress_api_http_HttpEntity.
     *
     * @return void
     */
    public function setEntity($entity)
    {
        if (! $entity instanceof org_tubepress_api_http_HttpEntity) {

            throw new Exception('Entity must be of type org_tubepress_api_http_HttpEntity');
        }

        $this->_entity = $entity;
    }

    /**
     * Get the HTTP message entity.
     *
     * @return org_tubepress_api_http_HttpEntity The HTTP entity. May be null.
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * Get an associative array of all headers.
     *
     * @return array An associative array of HTTP headers with this message. May be empty.
     */
    public function getAllHeaders()
    {
        return $this->_headers;
    }

    /**
     * Find a header value by header name.
     *
     * @param string $name The header name to lookup.
     *
     * @return string The header value. May be null.
     */
    public function getHeaderValue($name)
    {
        self::checkString($name);

        foreach ($this->_headers as $headerName => $headerValue) {

            if (strcasecmp($name, $headerName) === 0) {

                return $headerValue;
            }
        }

        return null;
    }

    /**
     * Set a single header.
     *
     * @param string $name  The header name.
     * @param string $value The header value.
     *
     * @return void
     */
    public function setHeader($name, $value)
    {
        self::checkString($name);
        self::checkString($value);

        $this->_headers[$name] = $value;
    }

    /**
     * Find whether or not this message carries any headers
     * with the given name.
     *
     * @param string $name The header name to lookup.
     *
     * @return bool True if a header with this name exists. False otherwise.
     */
    public function containsHeader($name)
    {
        return $this->getHeaderValue($name) !== null;
    }

    /**
     * Removes any headers with the given name.
     *
     * @param string $name The header name.
     *
     * @return void
     */
    public function removeHeaders($name)
    {
        self::checkString($name);

        foreach ($this->_headers as $headerName => $headerValue) {

            if (strcasecmp($name, $headerName) === 0) {

                unset($this->_headers[$headerName]);
            }
        }
    }

    /**
     * Determines if the given argument is a string.
     *
     * @param unknown_type $candidate The argument to check.
     *
     * @throws Exception If the argument is not a string.
     *
     * @return void
     */
    protected static function checkString($candidate)
    {
        if ($candidate != '' && ! is_string($candidate)) {

            throw new Exception("All HTTP headers must be strings ($candidate)");
        }
    }
}
