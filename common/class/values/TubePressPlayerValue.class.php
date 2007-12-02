<?php
final class TubePressPlayerValue extends TubePressAbstractValue {
   
    public function __construct($theName, $theDefault) {
        
        if (!is_a($theDefault, "TubePressPlayer")) {
            throw new Exception("Player value can only take on a TubePressPlayer");
        }
        
        $this->setName($theName);
        $this->setCurrentValue($theDefault);
        
    }
    
    public function printForOptionsPage() {
        
    }
    
    public function updateFromOptionsPage(array $postVars) {
        
    }
    
    public function updateManually($newValue) {
        
    }
}
?>