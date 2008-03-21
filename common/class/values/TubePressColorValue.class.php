<?php
class TubePressColorValue extends TubePressEnumValue {
    
    const normal = "/";
    const darkgrey = "0x3a3a3a/0x999999";
    const darkblue = "0x2b405b/0x6b8ab6";
    const lightblue = "0x006699/0x54abd6";
    const green = "0x234900/0x4e9e00";
    const orange = "0xe1600f/0xfebd01";
    const pink = "0xcc2550/0xe87a9f";
    const purple = "0x402061/0x9461ca";
    const red = "0x5d1719/0xcd311b";
    
    public function __construct($theName) {

        parent::__construct($theName, array(
            TubePressColorValue::normal => "light grey (normal)",
            TubePressColorValue::darkgrey => "dark grey",
            TubePressColorValue::darkblue => "dark blue",
            TubePressColorValue::lightblue => "light blue",
            TubePressColorValue::green => "green",
            TubePressColorValue::orange => "orange",
            TubePressColorValue::pink => "pink",
            TubePressColorValue::purple => "purple",
            TubePressColorValue::red => "red"
        ), TubePressColorValue::normal);
    }
}

?>