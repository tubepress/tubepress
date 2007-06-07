<?php
/**
 * TubePressEnumOpt.php
 * 
 * An "enumeration" TubePressOption
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
    
class TubePressEnumOpt extends TubePressOption
{
	var $_validValues;
	
	/**
	 * Constructor
	 */
	function TubePressEnumOpt($theTitle, $theDesc, $defaultValue, $validValues)
	{
		parent::TubePressOption($theTitle, $theDesc, $defaultValue);
		$this->_validValues = $validValues;
	}
	
    /**
     * Tries to set the value after seeing if it's valid
     */
    function setValue($candidate)
    {
        /* see if it's a valid value */
        if (is_array($this->_validValues)
            && !in_array($candidate, $this->_validValues)) {
            return PEAR::raiseError(_tpMsg("BADVAL",
            array($candidate, $this->_title,
            implode(", ", $this->_validValues))));
        }
        /* looks good! */
        $this->_value = $candidate;
    }
}
?>
