<?php
class TubePressBoolValue implements TubePressValue, TubePressHasName {
	
    private $bool;
    private $name;
    
    public function __construct($theName, $theDefault) {
        $this->bool = $theDefault;
        $this->name = $theName;   
    }
    
    public function printValueForHTML() {
        
    }
    
    public function setValue(boolean $candidate) {
        
    }
    
    public function updateValueFromHTML(boolean $newValue) {
        
    }
    
}
?>