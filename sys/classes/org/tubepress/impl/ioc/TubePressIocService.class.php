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

function_exists('tubepress_load_classes') || require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_classloader('org_tubepress_api_ioc_IocService');

/**
 * Simple dependency injector for TubePress.
 */
class org_tubepress_impl_ioc_TubePressIocService implements org_tubepress_api_ioc_IocService
{
    private $_interface;

    /* map of interface/class names to implementations */
    private $_map;

    public function __construct()
    {
        $this->_map = array();
    }

    public function bind($interface)
    {
        $this->_interface = $interface;

        /* chainable.. */
        return $this;
    }

    public function to($className)
    {
        /* prevent people from doing something like $ioc->to('foo') */
        if (!isset($this->_interface)) {
            throw new Exception('Call to "to" without calling "bind" first');
        }

        $this->_map[$this->_interface] = $className;

        /* clear this out for the next round */
        unset($this->_interface);
    }

    public function get($classOrInterfaceName)
    {
        /* haven't built this class/interface before? */
        if (!isset($this->_map[$classOrInterfaceName])) {
    
            /* maybe we can instantiate a singleton? */
            tubepress_classloader($classOrInterfaceName);
            if (class_exists($classOrInterfaceName)) {
                return $this->_buildAndRemember($classOrInterfaceName, $classOrInterfaceName);
            }

            /* give up */
            throw new Exception("$classOrInterfaceName was never bound to an implementation");
        }
    
        /* we've already built it. this should be the normal case. */
        if (is_a($this->_map[$classOrInterfaceName], $classOrInterfaceName)) {
            return $this->_map[$classOrInterfaceName];
        }

        /* build and return the mapped implementation */
        return $this->_buildInterface($classOrInterfaceName, $this->_map[$classOrInterfaceName]);
    }

    private function _buildInterface($interfaceName, $implementationName)
    {
        class_exists($interfaceName) || tubepress_classloader($interfaceName);
        class_exists($implementationName) || tubepress_classloader($implementationName);

        /* build the implementation */
        $instance = $this->_buildAndRemember($interfaceName, $implementationName);

        /* make sure the class looks OK */
        if (!is_a($instance, $interfaceName)) {
            throw new Exception("$implementationName does not implement $interfaceName, but they were bound together");
        }

        return $instance;
    }

    private function _buildAndRemember($interfaceName, $className)
    {
        /* build it */
        $ref      = new ReflectionClass($className);
        $instance = $ref->newInstance();
                
        /* save it for later */
        $this->_map[$interfaceName] = $instance;

        return $instance;
    }
}
