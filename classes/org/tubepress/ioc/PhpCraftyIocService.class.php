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

function_exists('tubepress_load_classes')
    || require(dirname(__FILE__) . '/../../../tubepress_classloader.php');
tubepress_load_classes(array('net_sourceforge_phpcrafty_ComponentFactory'));

/**
 * Dependency injector for TubePress that uses phpcrafty
 */
abstract class org_tubepress_ioc_PhpCraftyIocService extends net_sourceforge_phpcrafty_ComponentFactory
{
    public function get($className)
    {
        return $this->create($className);
    }

    public function ref($referenceName)
    {
        return $this->referenceFor($referenceName);
    }

    public function def($referenceName, $spec)
    {
        $this->setComponentSpec($referenceName, $spec);
    }

    /**
     * Define an implementation for a class. We'll never use constructor
     * injection, and we'll only use singletons, so this is just a convenience class.
     * 
     * @param $className
     * @param $properties
     * @return unknown_type
     */
    public function impl($className, $properties = array())
    {
        return $this->newComponentSpec($className, array(), $properties, true);
    }

    public function safeGet($firstChoice, $safeChoice)
    {
        if ($this->getComponentSpec($firstChoice) != null) {
            return $this->get($firstChoice);
        }
        return $this->get($safeChoice);
    }
}
