<?php
/**
 * TubePressIntegerOpt.php
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

class_exists("PEAR")
    || require(dirname(__FILE__) . "/../../../lib/PEAR/PEAR.php");
function_exists("_tpMsg")
    || require(dirname(__FILE__) . "/../../../messages.php");
class_exists("TubePressOption") || require(dirname(__FILE__) . "/../abstract/TubePressOption.php");

/**
 * An integer TubePressOption
 */
class TubePressIntegerOpt extends TubePressOption
{
    var $_max;
    
    /**
     * Constructor
     */
    function TubePressIntegerOpt($theTitle, $theDesc, $defaultValue,
        $theMax = 2147483647) 
    {
        parent::TubePressOption($theTitle, $theDesc, $defaultValue);
        $this->_max = $theMax;
    }
    
    /**
     * Tries to change the value after some integer error checking
     */
    function setValue($candidate)
    {
        $intval = intval($candidate);
        if ($candidate == "0"
            || $intval != 0) {
            $candidate = (integer)$candidate;
        }
        
        /* make sure it's the right type */
        $result = parent::checkType($candidate, "integer");
        if (PEAR::isError($result)) {
            return $result;
        }
        
        /* No TubePressIntegerOpts can be less than 1 */
        if (($candidate < 1)
            || ($candidate > $this->_max)) {
                return PEAR::raiseError(_tpMsg("MAXMIN",
                array($this->_title, $this->_max, $candidate)));
            }
        
        /* looks good! */
        $this->_value = $candidate;
    }
}
?>
