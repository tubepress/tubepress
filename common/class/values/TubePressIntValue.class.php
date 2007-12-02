<?php
class TubePressIntValue extends TubePressAbstractValue {
    
    private $min = 1;
    private $max = 2147483647;
    
    public function __construct($theName, $defaultValue) {
        
        if (!is_int($defaultValue)) {
            throw new Exception("TubePressIntValue can only take on integers");
        }
        
        $this->setCurrentValue($defaultValue);
        $this->setName($theName);
    }
    
    public final function updateManually($candidate) {
        if (($candidate < $this->min) || ($candidate > $this->max)) {
            throw new Exception(
                vsprintf("Out of range. Must be between %s and %s. You supplied %s.",
                array($this->min, $this->max, $candidate)));
        }
        $this->setCurrentValue($candidate);
    }
    
    public final function setMax($newMax) {
        
        if (!is_int($newMax)) {
            throw new Exception("Max value must be an integer");
        }
        
        if ($newMax < $this->min) {
            throw new Exception("Max must be greater than or equal to 1");
        }
        
        $this->max = $newMax;
    }
    
    public function printForOptionsPage() {
        
    }
    
    public function updateFromOptionsPage(array $postVars) {
        
    }
    
}
?>