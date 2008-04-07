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
class TubePressTextValue extends TubePressAbstractValue {
	
    public function __construct($theName, $defaultValue) {
        if (!is_string($defaultValue)) {
            throw new Exception("Text values must have defaults that are strings!");
        }
        $this->setCurrentValue($defaultValue);
        $this->setName($theName);
    }
    
    public final function printForOptionsPage(HTML_Template_IT &$tpl) {
    	$tpl->setVariable("OPTION_NAME", $this->getName());
        $tpl->setVariable("OPTION_VALUE", $this->getCurrentValue());
	    $tpl->parse("text");
    }
    
    public function updateManually($candidate) {
        if (!is_string($candidate)) {
        	throw new Exception($this->getName() . " can only take string values. You supplied " . $candidate);
        }
        $this->setCurrentValue($candidate);
    }
    
    public function updateFromOptionsPage(array $postVars) {
        if (array_key_exists($this->getName(), $postVars)) {
        	$this->updateManually($postVars[$this->getName()]);
        }
    }
}
?>