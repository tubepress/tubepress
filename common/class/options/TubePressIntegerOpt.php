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


/**
 * An integer TubePressOption
 */
class TubePressIntegerOpt extends TubePressDataItem
{
    private $max;
    
    /**
     * Constructor
     */
    function TubePressIntegerOpt($theTitle, $theDesc, $defaultValue,
        $theMax = 2147483647) 
    {
        parent::TubePressOption($theTitle, $theDesc, $defaultValue);
        $this->max = $theMax;
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
        
        /* No TubePressIntegerOpt can be less than 1 */
        if (($candidate < 1)
            || ($candidate > $this->max)) {
                throw new Exception(
                	vsprintf("%s must be between 1 and %s. You supplied %s.",
                		array($this->getTitle(), $this->max, $candidate)));
            }
        
        /* looks good! */
        $this->value = $candidate;
    }
}
?>
