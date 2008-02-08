<?php
class TubePressBoolValue extends TubePressAbstractValue {
	
    public function __construct($theName, $theDefault) {
        
        if (!is_bool($theDefault)) {
            throw new Exception("TubePressBoolValues can only take on booleans as values");
        }
        
        $this->setCurrentValue($theDefault);
        $this->setName($theName);
    }
    
    public final function printForOptionsPage(HTML_Template_IT &$tpl) {
        
        if ($this->getCurrentValue()) {
            $tpl->setVariable("OPTION_SELECTED", "CHECKED");
        }
				
	    $tpl->parse("checkbox");
    }
    
    public final function updateManually($candidate) {
        if ($candidate instanceof boolean) {
            throw new Exception("Boolean values can only take on booleans");
        }
        $this->setCurrentValue($candidate);
    }
    
    public final function updateFromOptionsPage(array $postVars) {
        
    }
}
?>