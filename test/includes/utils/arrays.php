<?php

class TubePressArrayTestUtils
{
    public static function checkArrayEquality($expected, $actual)
    {
        foreach ($expected as $expectedName) {
            if (!in_array($expectedName, $actual)) {
                throw new Exception("Missing expected array value: $expectedName");
            }
        }
    
        foreach ($actual as $actualName) {
            if (!in_array($actualName, $expected)) {
                throw new Exception("Extra array value: $actualName");
            }
        }
    }
}