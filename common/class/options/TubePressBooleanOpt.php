<?php
/**
 * TubePressBooleanOpt.php 
 * 
 * Copyright (C) 2007 Eric D. Hough (http://ehough.com)
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

require_once("PEAR.php");
function_exists("_tpMsg")
    || require(dirname(__FILE__) . "/../../messages.php");
class_exists("TubePressOption") || require("TubePressOption.php");
    
/**
 * A boolean TubePressOption   
 */
class TubePressBooleanOpt extends TubePressOption
{
    /**
     * Constructor
     */
    function TubePressBooleanOpt($theTitle, $theDesc, $defaultValue)
    {
        parent::TubePressOption($theTitle, $theDesc, $defaultValue);
    }
    
    /**
     * Tries to set the value after type checking
     */
    function setValue($candidate)
    {
        /* make sure it's the right type */
        $result = parent::checkType($candidate, "boolean");
        if (PEAR::isError($result)) {
            return $result;
        }
        
        /* looks good! */
        $this->_value = $candidate;
    }
}
?>
