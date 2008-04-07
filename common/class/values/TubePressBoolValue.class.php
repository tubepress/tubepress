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
class TubePressBoolValue extends TubePressAbstractValue {
	
    public function __construct($theName, $theDefault) {
        
        if (!is_bool($theDefault)) {
            throw new Exception("TubePressBoolValues can only take on booleans as values");
        }
        
        $this->setCurrentValue($theDefault);
        $this->setName($theName);
    }
    
    public final function printForOptionsPage(HTML_Template_IT &$tpl) {
        
        if ($this->getCurrentValue()) {
            $tpl->setVariable("OPTION_SELECTED", "CHECKED");
        }
				
	    $tpl->parse("checkbox");
    }
    
    public final function updateManually($candidate) {
        if ($candidate instanceof boolean) {
            throw new Exception("Boolean values can only take on booleans");
        }
        $this->setCurrentValue($candidate);
    }
    
    public final function updateFromOptionsPage(array $postVars) {
        if (in_array($this->getName(), $postVars)) {
        	$this->updateManually(true);
        } else {
        	$this->updateManually(false);
        }
    }
}
?>