<?php
/**
 * TubePressOption.php
 * 
 * An "abstract" TubePressOption
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

class TubePressOption
{
    var $_title, $_description, $_value;

    /**
     * Constructor
     */
    function TubePressOption($theTitle, $theDesc, $defaultValue)
    {
        $this->_description = $theDesc;
        $this->_value = $defaultValue;
        $this->_title = $theTitle;
    }
       
    /**
     * This option's visible description (e.g. "YouTube video id")
     */
    function getDescription()
    {
        return $this->_description;
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
     * Meant to be overridden
     */ 
    function setValue($candidate)
    {
        die("TubePressOption is an abstract class");
    }
    
    function checkType($candidate, $type) {
        if (gettype($candidate) != $type) {
            return PEAR::raiseError(_tpMsg("BADTYPE", 
                array($this->_title, $type,
                $candidate, gettype($candidate))));
        }
    }
}
?>
