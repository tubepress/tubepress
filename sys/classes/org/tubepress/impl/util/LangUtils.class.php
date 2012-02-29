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
 * PHP language utilities.
 */
class org_tubepress_impl_util_LangUtils
{
    public static function isAssociativeArray($candidate)
    {
        return is_array($candidate)
            && ! empty($candidate)
            && count(array_filter(array_keys($candidate),'is_string')) == count($candidate);
    }
    
    public static function getDefinedConstants($classOrInterface)
    {
        if (! class_exists($classOrInterface) && ! interface_exists($classOrInterface)) {
            
            return array();
        }
        
        $ref       = new ReflectionClass($classOrInterface);
        $constants = $ref->getConstants();
        $toReturn  = array();
        
        foreach ($constants as $name => $value) {
            
            if (substr($name, 0, 1) !== '_') {
                
                $toReturn[] = $value;
            }
        }
        
        return $toReturn;
    }
}

