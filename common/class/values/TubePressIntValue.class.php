<?php
/**
 * Copyright 2006, 2007, 2008 Eric D. Hough (http://ehough.com)
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
class TubePressIntValue extends TubePressTextValue {
    
    private $min = 1;
    private $max = 2147483647;
    
    public function __construct($theName, $defaultValue) {
        
        if (!is_int($defaultValue)) {
            throw new Exception("TubePressIntValue can only take on integers");
        }
        
        $this->setCurrentValue($defaultValue);
        $this->setName($theName);
    }
    
    public final function updateManually($candidate) {
        if (($candidate < $this->min) || ($candidate > $this->max)) {
            throw new Exception(
                vsprintf("Out of range. Must be between %s and %s. You supplied %s.",
                array($this->min, $this->max, $candidate)));
        }
        $this->setCurrentValue($candidate);
    }
    
    public final function setMax($newMax) {
        
        if (!is_int($newMax)) {
            throw new Exception("Max value must be an integer");
        }
        
        if ($newMax < $this->min) {
            throw new Exception("Max must be greater than or equal to 1");
        }
        
        $this->max = $newMax;
    }
    
}
?>