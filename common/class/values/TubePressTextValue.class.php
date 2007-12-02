<?php
class TubePressTextValue extends TubePressAbstractValue {
	
    public function __construct($theName, $defaultValue) {
        if (!is_string($defaultValue)) {
            throw new Exception("Text values must have defaults that are strings!");
        }
        $this->setCurrentValue($defaultValue);
        $this->setName($theName);
    }
    
    public final function printForOptionsPage() {
        
    }
    
    public final function updateManually($candidate) {
        
    }
    
    public final function updateFromOptionsPage(array $postVars) {
        
    }
}
?>