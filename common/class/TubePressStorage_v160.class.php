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
class TubePressStorage_v160 {
    
    private $optionPackages = array();

    public function __construct() {
        
        $this->optionPackages = array(
            "gallery" => new TubePressGalleryOptions(),
            "display" => new TubePressDisplayOptions(),
        	"embedded" => new TubePressEmbeddedOptions(),
            "meta" => new TubePressMetaOptions(),
            "advanced" => new TubePressAdvancedOptions()
        );
    }
    
    public function getEmbeddedOptions() { return $this->optionPackages["embedded"]; }
    public function &getOptionPackages() { return $this->optionPackages; }
    public function &getGalleryOptions() { return $this->optionPackages["gallery"]; }
    public function getDisplayOptions() { return $this->optionPackages["display"]; }
    public function getMetaOptions() { return $this->optionPackages["meta"]; }
    public function getAdvancedOptions() { return $this->optionPackages["advanced"]; }
    
    public function getCurrentValue($optionName) {
            
        foreach ($this->optionPackages as $optPackage) {
            if (array_key_exists($optionName, $optPackage->getOptions())) {
                $value = $optPackage->get($optionName)->getValue();
                if ($value instanceof TubePressValue) {
                    return $value->getCurrentValue();
                } else {
                    return $value;
                }
            }
        }
        throw new Exception("No such option: " . $optionName);
    }
    
    public function setCurrentValue($optionName, $optionValue) {
        
        foreach ($this->optionPackages as $optPackage) {
            if (array_key_exists($optionName, $optPackage->getOptions())) {
                
                $option =& $optPackage->get($optionName);
                $value =& $option->getValue();
                $value->updateManually($optionValue);
            }
        }
        
        throw new Exception("No such option: " . $optionName);
    }
}
?>