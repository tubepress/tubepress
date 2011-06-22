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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require(dirname(__FILE__) . '/../classloader/ClassLoader.class.php');
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_template_Template',
    'org_tubepress_impl_util_StringUtils',
));

class org_tubepress_impl_template_SimpleTemplate implements org_tubepress_api_template_Template
{
    private $_source;
    private $_path;

    public function __construct($path)
    {
        if (!is_readable($path)) {
            throw new Exception("Cannot read template at $path");
        }

            $this->_path = $path;
            $this->_source = array();
    }

    public function setVariable($name, $value)
    {
        $this->_source[$name] = $value;
    }

    public function toString()
    {
        if (!isset($this->_path)) {
            throw new Exception('Can\'t build template when no file is set');
        }

        ob_start();
        extract($this->_source);
        include realpath($this->_path);
        $result = ob_get_contents();
        ob_end_clean();

        return org_tubepress_impl_util_StringUtils::removeEmptyLines($result);
    }

    public function reset()
    {
        $this->_source = array();
    }

    public function getInstance($path)
    {
        return new org_tubepress_impl_template_SimpleTemplate($path);
    }
}
