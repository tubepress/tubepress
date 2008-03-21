<?php
class TubePressTextValue extends TubePressAbstractValue {
	
    public function __construct($theName, $defaultValue) {
        if (!is_string($defaultValue)) {
            throw new Exception("Text values must have defaults that are strings!");
        }
        $this->setCurrentValue($defaultValue);
        $this->setName($theName);
    }
    
    public final function printForOptionsPage(HTML_Template_IT &$tpl) {
    	$tpl->setVariable("OPTION_NAME", $this->getName());
        $tpl->setVariable("OPTION_VALUE", $this->getCurrentValue());
	    $tpl->parse("text");
    }
    
    public function updateManually($candidate) {
        if (!is_string($candidate)) {
        	throw new Exception($this->getName() . " can only take string values. You supplied " . $candidate);
        }
        $this->setCurrentValue($candidate);
    }
    
    public function updateFromOptionsPage(array $postVars) {
        if (array_key_exists($this->getName(), $postVars)) {
        	$this->updateManually($postVars[$this->getName()]);
        }
    }
}
?>