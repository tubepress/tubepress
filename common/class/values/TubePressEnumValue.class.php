<?php
abstract class TubePressEnumValue extends TubePressAbstractValue {
    
    /* an array of the valid values that this value can take on */
    private $validValues;
    
    /**
     * Default constructor
     *
     * @param string $theName
     * @param array $theValidValues
     * @param string $defaultValue
     */
    public function __construct($theName, array $theValidValues, $defaultValue) {
        $this->setName($theName);
        $this->validValues = $theValidValues;
        $this->setCurrentValue($defaultValue);
    }
    
	/**
     * Tries to set the value after seeing if it's valid
     */
    public final function updateManually($candidate)
    {
        /* see if it's a valid value */
        if (!in_array($candidate, array_keys($this->validValues))) {
           
            throw new Exception(
            	vsprintf("\"%s\" is invalid. Must be one of the following: '%s'",
            		array($candidate, implode("', '", array_keys($this->validValues)))));
        }
        
        /* looks good! */
        $this->setCurrentValue($candidate);
    }
    
    /**
     * Prints out a drop down menu of the values
     *
     * @param HTML_Template_IT $tpl
     */
    public function printForOptionsPage(HTML_Template_IT &$tpl) {
        						
    	$tpl->setVariable("OPTION_NAME", $this->getName());
		foreach($this->validValues as $validValue => $validValueTitle) {

		    if ($this->getCurrentValue() === $validValue) {
		        $tpl->setVariable("OPTION_SELECTED", "SELECTED");
		    }
		    $tpl->setVariable("MENU_ITEM_TITLE", $validValueTitle);
		    $tpl->setVariable("MENU_ITEM_VALUE", $validValue);
		    $tpl->parse("menuItem");
		}			
        $tpl->parse("menu");
    }
    
    public function updateFromOptionsPage(array $postVars) {
        
    	/* see if it's there */
    	if (array_key_exists($this->getName() . "Value", $postVars)) {
        	$this->updateManually($postVars[$this->getName() . "Value"]);
        }
    }
    
}
?>