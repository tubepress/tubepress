<?php
class TPAbstractHasValue extends TPAbstractHasDescription implements TubePressValue {
    
    private $defaultValue;
    
    private $currentValue;
    
    protected abstract function setDefault($candidate);
}
?>