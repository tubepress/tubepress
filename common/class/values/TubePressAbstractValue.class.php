<?php
abstract class TubePressAbstractValue implements TubePressValue, TubePressHasName {
    
    /* this value's name */
    private $name;
    
    /* this value's current value */
    private $currentValue;
    
    public final function getName() { return $this->name; }
    
    protected final function setName($newName) {
        $this->name = $newName;
    }
    
    protected final function getCurrentValue() {
        return $this->currentValue;
    }
    
    protected final function setCurrentValue($newValue) {
        $this->currentValue = $newValue;
    }
}
?>