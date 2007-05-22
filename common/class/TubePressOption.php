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

class TubePressOption
{
    var $_name, $_title, $_description, $_value, $_type, $_valid_values;

    /**
     * Constructor
     */
    function TubePressOption($theName, $theTitle, $theDesc, $theValue, 
        $theType = "string")
    {
        $this->_name = $theName;
        $this->_description = $theDesc;
        $this->_value = $theValue;
        $this->_title = $theTitle;
        $this->_type = $theType;
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
     * This option's visible description (e.g. "YouTube video id")
     */
    function getDescription()
    {
        return $this->_description;
    }
    
    /**
     * This option's current value (e.g. "12345678abc")
     */
    function getValue()
    {
        return $this->_value;
    }
    
    /**
     * FIXME
     */
    function setValue($candidate)
    {
    	/* make sure it's the right type */
        if (gettype($candidate) != $this->_type) {
            return PEAR::raiseError(_tpMsg("BADTYPE", 
                array($this->_title, $this->_type,
                gettype($candidate), $candidate)));
        }
        
        /* see if it's a valid value */
        if (!in_array($candidate, $this->_valid_values)) {
        	return PEAR::raiseError(_tpMsg("BADVAL",
        	$candidate, $this->_title,
        	implode(", ", $this->_valid_values)));
        }
        
        /* looks good! */
        $this->_value = $candidate;
    }
    
    /**
     * FIXME
     */
    function getValidValues()
    {
        if (is_array($this->_valid_values)) {
            return $this->_valid_values;
        }
        return PEAR::raiseError(_tpMsg("NOVALS"));
    }
    
    /**
     * FIXME
     */
    function setValidValues($theVals)
    {
        if (!is_array($theVals)) {
            return PEAR::raiseError(_tpMsg("ARRSET"));
        }

        $this->_valid_values = $theVals;
    }
}
?>
