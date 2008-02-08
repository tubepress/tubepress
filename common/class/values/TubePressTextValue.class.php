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
        $tpl->setVariable("OPTION_VALUE", $this->getCurrentValue());
	    $tpl->parse("text");
    }
    
    public function updateManually($candidate) {
        
    }
    
    public function updateFromOptionsPage(array $postVars) {
        
    }
}
?>