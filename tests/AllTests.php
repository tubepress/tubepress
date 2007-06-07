<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}
 
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
 

class AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
 
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit');
        $filenames = array(
    		dirname(__FILE__).'/common/class/TubePressOptionsPackageTest.php',
    		dirname(__FILE__).'/common/class/TubePressXMLTest.php',
    		dirname(__FILE__).'/common/class/options/TubePressIntegerOptTest.php',
    		dirname(__FILE__).'/common/class/options/TubePressBooleanOptTest.php',
    		dirname(__FILE__).'/common/class/options/TubePressStringOptTest.php',
    		dirname(__FILE__).'/common/class/options/TubePressEnumOptTest.php',
    		dirname(__FILE__).'/common/class/TubePressVideoTest.php');
        $suite->addTestFiles($filenames);
        return $suite;
    }
}
 
if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}

?>

