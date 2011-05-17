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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_impl_patterns_cor_AbstractChainContext',
));

/**
 * The context for HTTP clients.
 */
class org_tubepress_impl_http_HttpClientChainContext extends org_tubepress_impl_patterns_cor_AbstractChainContext
{
    private $_url;

    private $_args;

    public function __construct($url, $args)
    {
        $this->_url  = $url;
        $this->_args = $args;
    }

    public function getUrl()
    {
        return $this->_url;
    }

    public function getArgs()
    {
        return $this->_args;
    }

}
