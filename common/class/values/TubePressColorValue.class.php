<?php
/**
 * Copyright 2006, 2007, 2008 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org)
 * 
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
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