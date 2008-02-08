<?php
class TubePressColorValue extends TubePressEnumValue {
    
    const black = "black";
    const silver = "silver";
    
    public function __construct($theName) {

        parent::__construct($theName, array(
            TubePressColorValue::black => "black",
            TubePressColorValue::silver => "silver"
        ), TubePressColorValue::black);
    }
}

?>