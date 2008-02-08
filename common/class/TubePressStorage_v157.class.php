<?php
class TubePressStorage_v157 {
    
    private $optionPackages = array();

    public function __construct() {
        
        $this->optionPackages = array(
            "gallery" => new TubePressGalleryOptions(),
            "display" => new TubePressDisplayOptions(),
            "meta" => new TubePressMetaOptions(),
            "advanced" => new TubePressAdvancedOptions()
        );
    }
    
    public function &getOptionPackages() { return $this->optionPackages; }
    public function getGalleryOptions() { return $this->optionPackages["gallery"]; }
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