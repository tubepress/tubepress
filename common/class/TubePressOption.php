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
    function TubePressOption($theName, $theTitle, $theDesc, $theValue, $theType = "string")
    {
        $this->_name = $theName;
        $this->_description = $theDesc;
        $this->_value = $theValue;
        $this->_title = $theTitle;
        $this->_type = $theType;
    }
    
    function getName() {
    	return $this->_name;
    }
    
    function getTitle() {
    	return $this->_title;
    }
    
    function getDescription() {
    	return $this->_description;
    }
    
    function getValue() {
    	return $this->_value;
    }
    
    function setValue($candidate) {
    	if (gettype($candidate) != $this->_type) {
    		return PEAR::raiseError(_tpMsg("BADTYPE", 
    		    array($this->_title, $this->_type,
    		    gettype($candidate), $candidate)));
    	}
    	
    	if (is_array($this->_valid_values)) {
    		$validOpt = false;
    		foreach ($this->_valid_values as $val) {
    			if ($val->_name == $candidate) {
    				$validOpt = true;
    			}
    		}
    		if ($validOpt == false) {
    			return PEAR::raiseError(_tpMsg("BADVAL"));
    		}
    	}
    	$this->_value = $candidate;
    }
    
    function getValidValues() {
    	if (is_array($this->_valid_values)) {
    		return $this->_valid_values;
    	}
    	return PEAR::raiseError(_tpMsg("NOVALS"));
    }
    
    function setValidValues($theVals) {
    	if (!is_array($theVals)) {
    		return PEAR::raiseError(_tpMsg("ARRSET"));
    	}
    	foreach ($theVals as $val) {
    		if (!is_a($val, TubePressEnum)) {
    			return PEAR::raiseError(_tpMsg("VALTYPE"));
    		}
    	}
    
    	$this->_valid_values = $theVals;
    }
}
?>
