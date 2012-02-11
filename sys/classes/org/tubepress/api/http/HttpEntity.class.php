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

/**
 * An HTTP entity.
 */
class org_tubepress_api_http_HttpEntity
{
    const _ = 'org_tubepress_api_http_HttpEntity';

    private $_content;

    private $_contentLength = 0;

    private $_contentType;


    /**
     * Get the content of this entity.
     *
     * @return unknown_type The content of this entity. May be null.
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Sets the content of this entity.
     *
     * @param unknown_type $content The entity content.
     *
     * @return void
     */
    public function setContent($content)
    {
        $this->_content = $content;
    }

    /**
     * Get the Content-Length of this entity.
     *
     * @return int The content length of this entity.
     */
    public function getContentLength()
    {
        return $this->_contentLength;
    }

    /**
     * Sets the content length of the entity.
     *
     * @param int $length The Content-Length.
     *
     * @throws Exception If the supplied length is not a non-negative integer.
     *
     * @return void
     */
    public function setContentLength($length)
    {
        if (! is_numeric($length)) {

            throw new Exception("Content-Length must be an integer ($length)");
        }

        $length = intval($length);

        if ($length < 0) {

            throw new Exception("Content-Length cannot be negative ($length)");
        }

        return $this->_contentLength = $length;
    }

    /**
     * Get the Contenty-Type of this entity.
     *
     * @return string The Content-Type of this entity. May be null.
     */
    public function getContentType()
    {
        return $this->_contentType;
    }

    /**
     * Sets the Contenty-Type of this entity
     *
     * @param string $type The Contenty-Type of this entity.
     *
     * @throws Exception If the given type is not a string.
     *
     * @return void
     */
    public function setContentType($type)
    {
        if (! is_string($type)) {

            throw new Exception("Content-Type must be a string ($type)");
        }

        $this->_contentType = $type;
    }
}
