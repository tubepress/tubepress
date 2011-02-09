<?php

require_once dirname(__FILE__) . '/../../../../../../test/unit/TubePressUnitTest.php';

class org_tubepress_api_const_ClassConstantTestUtility
{
    public static function performTest($className, $expected)
    {
        $class = new ReflectionClass($className);
        $actualNames = $class->getConstants();

        foreach ($expected as $expectedName) {
            if (!in_array($expectedName, $actualNames)) {
                throw new Exception("$className is missing constant: $expectedName");
            }
        }
    
        foreach ($actualNames as $actualName) {
            if (!in_array($actualName, $expected)) {
                throw new Exception("$className has an extra constant: $actualName");
            }
        }
    }    
}
?>
