<?php
class TubePressTextValue implements TubePressValue, TubePressHasName {
	
    private $string;
    private $name;
    
    public function __construct($theName, $defaultValue) {
        if (!is_a($defaultValue, "string")) {
            throw new Exception("Text values must have defaults that are strings!");
        }
        $this->string = $defaultValue;
        $this->name = $theName;
    }
    
    public function printValueForHTML() {
        
    }
    
    public function setValue(string $candidate) {
        
    }
    
    public function updateValueFromHTML(string $newValue) {
        
    }
    
    public function getName() { return $this->name; }
}
?>