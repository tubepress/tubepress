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
    		dirname(__FILE__).'/common/TubePressOptionsPackageTest.php',
    		dirname(__FILE__).'/common/TubePressCSSTest.php',
    		dirname(__FILE__).'/common/TubePressXMLTest.php',
    		dirname(__FILE__).'/common/TubePressOptionTest.php',
    		dirname(__FILE__).'/common/TubePressVideoTest.php');
        $suite->addTestFiles($filenames);
        return $suite;
    }
}
 
if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}

?>

