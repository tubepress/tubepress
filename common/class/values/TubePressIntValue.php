<?php
class TubePressIntValue extends TPAbstractHasName implements TubePressValue {
    
    private $int;
    private $min = 1;
    private $max = 2147483647;
    
    public function printValueForHTML() {
        
    }
    
    public function setValue(int $candidate) {
        if (($candidate < $this->min) || ($candidate > $this->max)) {
            throw new Exception(
                vsprintf("Out of range. Must be between %s and %s. You supplied %s.",
                array($this->getTitle(), $this->max, $candidate)));
        }
    }
    
    public function updateValueFromHTML(int $newValue) {
        
    }
    
    public function setMax(int $newMax) {
        $this->max = $newMax;
    }
}
?>