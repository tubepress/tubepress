<?php
/**
 * TubePressOption.php
 * 
 * A single TubePressOption
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

class TubePressOption
{
    var $_name, $_title, $_description, $_value, $_type, $_valid_values, $_max;

    /**
     * Constructor
     */
    function TubePressOption($theName, $theTitle, $theDesc, $defaultValue, 
        $theType = "string", $max = 2147483647, $validValues = "")
    {
        $this->_name = $theName;
        $this->_description = $theDesc;
        $this->_value = $defaultValue;
        $this->_title = $theTitle;
        $this->_type = $theType;
        $this->_max = $max;
        $this->_valid_values = $validValues;
    }
       
    /**
     * This option's visible description (e.g. "YouTube video id")
     */
    function getDescription()
    {
        return $this->_description;
    }
    
    /**
     * This option's internal name (e.g. "id")
     */
    function getName()
    {
        return $this->_name;
    }
    
    /**
     * This option's visible title (e.g. "Video ID"")
     */
    function getTitle()
    {
        return $this->_title;
    }
    
    /**
     * This option's current value (e.g. "12345678abc")
     */
    function getValue()
    {
        return $this->_value;
    }
    
    
    /**
     * Does error checking for each value. If checking passes,
     * will actually change the value.
     */
    function setValue($candidate)
    {
        /* integers come in here as strings */
        if ($this->_type == "integer") {
            $intval = intval($candidate);
            if ($candidate == "0" || $intval != 0) {
                $candidate = (integer)$candidate;
            }
        }
        
        /* make sure it's the right type */
        if (gettype($candidate) != $this->_type) {
            return PEAR::raiseError(_tpMsg("BADTYPE", 
                array($this->_title, $this->_type,
                $candidate, gettype($candidate))));
        }
        
        /* see if it's a valid value */
        if (is_array($this->_valid_values) && !in_array($candidate, $this->_valid_values)) {
            return PEAR::raiseError(_tpMsg("BADVAL",
            array($candidate, $this->_title,
            implode(", ", $this->_valid_values))));
        }
        
        /* check max and min */
        if (is_int($candidate)) {
            if (($candidate < 1)
                || ($candidate > $this->_max)) {
                    return PEAR::raiseError(_tpMsg("MAXMIN",
                    array($this->_title, $this->_max, $candidate)));
                }
        }
        
        /* looks good! */
        $this->_value = $candidate;
    }
}
?>
