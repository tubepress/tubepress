<?php
abstract class TubePressEnumValue implements TubePressValue {
    
    protected $validValues;
    protected $value;
    
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
}
?>