<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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
 * Dependency injector for TubePress in a WordPress environment
 */
class org_tubepress_impl_ioc_TubePressIocService implements org_tubepress_api_ioc_IocService
{
    
    private $_interface;
    private $_label;

    /* map of labeled interfaces to implementations */
    private $_labeledMap;
    
    /* map of un-labeled interfaces to implementations */
    private $_map;

    public function __construct()
    {
        $this->_labeledMap = array();
        $this->_map = array();
    }

    public function bind($interface)
    {
        $this->_interface = $interface;

        /* chainable.. */
        return $this;
    }

    public function labeled($label)
    {
        $this->_label = $label;

        /* chainable.. */
        return $this;
    }

    public function to($className)
    {
        if (!isset($this->_interface)) {
            throw new Exception('Call to "to" without calling "bind" first');
        }

        /* labeled? */
        if (isset($this->_label)) {

            /* first time seeing this interface? */
            if (!isset($this->_labeledMap[$this->_interface])) {
                $this->_labeledMap[$this->_interface] = array();
            }

            /* register the interface/label combination */
            $this->_labeledMap[$this->_interface][$this->_label] = $className;

        } else {

            $this->_map[$this->_interface] = $className;

        }

        /* clear these out for the next round */
        unset($this->_interface);
        unset($this->_label);

        /* chainable.. */
        return $this;
    }

    public function get($interface, $label = "")
    {
        if ($label === "") {
            /* never heard of this interface before? */
            if (!isset($this->_map[$interface])) {
    
                /* maybe you forgot to supply a label? */
                if (isset($this->_labeledMap[$interface])) {
                    throw new Exception("$interface was never bound to an implementation without a label");
                }
    
                /* give up */
                throw new Exception("$interface was never bound to an implementation");
            }
    
            /* return the implementation */
            return $this->_getInstance($interface, $this->_map[$interface]);
        }
        
        /* don't have this interface/label combo? */
        if (!isset($this->_labeledMap[$interface][$label])) {
            throw new Exception("Can't find implementation of $interface with label $label");
        }

        return $this->_getInstance($interface, $this->_labeledMap[$interface][$label], $label);
    }

    private function _getInstance($interface, $implementation, $label = "")
    {
        /* see if we've already built it. this should be the normal case. */
        if (is_a($implementation, $interface)) {
            return $implementation;
        }

        class_exists($interface) || tubepress_classloader($interface);
        class_exists($implementation) || tubepress_classloader($implementation);

        /* built it with reflection */
        $ref = new ReflectionClass($implementation);
        $instance = $ref->newInstance();

        /* make sure the class looks OK */
        if (!is_a($instance, $interface)) {
            throw new Exception("$implementation does not implement $interface, but they were bound together");
        }

        /* save it for later */
        if ($label === "") {
            $this->_map[$interface] = $instance;
        } else {
            $this->_labeledMap[$interface][$label] = $instance;
        }

        return $instance;
    }
}
