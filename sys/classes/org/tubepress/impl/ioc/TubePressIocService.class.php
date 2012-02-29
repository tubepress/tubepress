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
org_tubepress_impl_classloader_ClassLoader::loadClass('org_tubepress_api_ioc_IocService');

/**
 * Simple dependency injector for TubePress.
 */
class org_tubepress_impl_ioc_TubePressIocService implements org_tubepress_api_ioc_IocService
{
    private $_interface;

    /* map of interface/class names to implementations */
    private $_map;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_map = array();
    }

    /**
     * Define the interface to which we are about to bind.
     *
     * @param string $interface The name of the interface.
     *
     * @return org_tubepress_impl_ioc_TubePressIocService For chaining.
     */
    public function bind($interface)
    {
        $this->_interface = $interface;

        /* chainable.. */
        return $this;
    }

    /**
    * Define the implementation of the interface used in bind().
    *
    * @param string $className The name of the implementation.
    *
    * @return void
    */
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

    /**
     * Gets a reference to the object implemented by the given class or interface name.
     *
     * @param string $classOrInterfaceName The name of the class or interface to retrieve.
     *
     * @return object The object instance.
     */
    public function get($classOrInterfaceName)
    {
        /* haven't built this class/interface before? */
        if (!isset($this->_map[$classOrInterfaceName])) {

            /* maybe we can instantiate a singleton? */
            org_tubepress_impl_classloader_ClassLoader::loadClass($classOrInterfaceName);
            if (class_exists($classOrInterfaceName)) {
                return $this->_buildAndRemember($classOrInterfaceName, $classOrInterfaceName);
            }

            /* give up */
            throw new Exception("$classOrInterfaceName was never bound to an implementation");
        }

        /* we've already built it. this should be the normal case. */
        if ($this->_map[$classOrInterfaceName] instanceof $classOrInterfaceName) {
            return $this->_map[$classOrInterfaceName];
        }

        /* build and return the mapped implementation */
        return $this->_buildInterface($classOrInterfaceName, $this->_map[$classOrInterfaceName]);
    }

    private function _buildInterface($interfaceName, $implementationName)
    {
        class_exists($interfaceName) || org_tubepress_impl_classloader_ClassLoader::loadClass($interfaceName);
        class_exists($implementationName) || org_tubepress_impl_classloader_ClassLoader::loadClass($implementationName);

        /* build the implementation */
        $instance = $this->_buildAndRemember($interfaceName, $implementationName);

        /* make sure the class looks OK */
        if (!($instance instanceof $interfaceName)) {
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
