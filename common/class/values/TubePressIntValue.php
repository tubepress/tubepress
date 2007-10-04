<?php
class TubePressIntValue implements TubePressValue, TubePressHasName {
    
    private $int;
    private $min = 1;
    private $max = 2147483647;
    private $name;
    
    public function __construct($theName, $defaultValue) {
        $this->setValue($defaultValue);
        $this->name = $theName;
    }
    
    public function printValueForHTML() {
        
    }
    
    public function setValue($candidate) {
        if (($candidate < $this->min) || ($candidate > $this->max)) {
            throw new Exception(
                vsprintf("Out of range. Must be between %s and %s. You supplied %s.",
                array($this->getTitle(), $this->max, $candidate)));
        }
        $this->int = $candidate;
    }
    
    public function updateValueFromHTML(int $newValue) {
        
    }
    
    public function setMax(int $newMax) {
        $this->max = $newMax;
    }
}
?>