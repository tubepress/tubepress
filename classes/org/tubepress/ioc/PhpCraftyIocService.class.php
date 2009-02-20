<?php
/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
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

    public function impl($className, $properties = array())
    {
        return $this->newComponentSpec($className, array(), $properties, true);
    }
}
?>
