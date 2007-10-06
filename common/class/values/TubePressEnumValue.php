<?php
abstract class TubePressEnumValue implements TubePressValue, TubePressHasName {
    
    protected $validValues;
    protected $value;
    protected $name;
    
    public function __construct($theName, array $theValidValues, $defaultValue) {
        $this->name = $theName;
        $this->validValues = $theValidValues;
        $this->setValue($defaultValue);
    }
    
	/**
     * Tries to set the value after seeing if it's valid
     */
    function setValue($candidate)
    {
        /* see if it's a valid value */
        if (!in_array($candidate, $this->validValues)) {
           
            throw new Exception(
            	vsprintf("\"%s\" is invalid. Must be one of the following: '%s'",
            		array($candidate, implode("', '", $this->validValues))));
        }
        /* looks good! */
        $this->value = $candidate;
    }
    
    public function getName() { return $this->name; }
}
?>