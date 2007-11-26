<?php
class TubePressTextValue extends TubePressAbstractValue {
	
    public function __construct($theName, $defaultValue) {
        if (!is_a($defaultValue, "string")) {
            throw new Exception("Text values must have defaults that are strings!");
        }
        $this->setCurrentValue($defaultValue);
        $this->setName($theName);
    }
}
?>