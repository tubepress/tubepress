<?php
/**
 * TubePressEnumOpt.php
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


/**
 * An "enumeration" TubePressOption. This option can only take on
 * certain values.
 */
class TubePressEnumOpt extends TubePressDataItem
{
    private $validValues;
    
    /**
     * Constructor
     */
    function TubePressEnumOpt($theTitle, $theDesc, $defaultValue, $vals)
    {
        parent::TubePressDataItem($theTitle, $theDesc, $defaultValue);
        if (!is_array($vals)) {
            throw new Exception("Enum options must have an array of valid values");
        }
        $this->validValues = $vals;
    }
    
    /**
     * Tries to set the value after seeing if it's valid
     */
    function setValue($candidate)
    {
        /* see if it's a valid value */
        if (!in_array($candidate, $this->validValues)) {
           
            throw new Exception(
            	vsprintf("\"%s\" not a valid value for \"%s\". Must be one of the following: '%s'",
            		array($candidate, $this->_title, implode("', '", $this->validValues))));
        }
        /* looks good! */
        $this->value = $candidate;
    }
}
?>
