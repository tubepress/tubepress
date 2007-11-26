<?php
class TubePressBoolValue extends TubePressAbstractValue {
	
    public function __construct($theName, $theDefault) {
        
        if (!is_bool($theDefault)) {
            throw new Exception("TubePressBoolValues can only take on booleans as values");
        }
        
        $this->setCurrentValue($theDefault);
        $this->setName($theName);
    }
    
    public final function printForOptionsPage() {
        
    }
    
    public final function updateManually(boolean $candidate) {
        
    }
    
    public final function updateFromOptionsPage(boolean $newValue) {
        
    }
}
?>