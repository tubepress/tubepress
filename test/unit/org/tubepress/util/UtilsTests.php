<?php
require dirname(__FILE__) . '/../../../PhpUnitLoader.php';
require_once 'StringUtilsTest.php';
require_once 'TimeUtilsTest.php';
require_once 'OptionsReferenceTest.php';

class UtilsTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress Utility Tests');
		$suite->addTestSuite('org_tubepress_util_StringUtilsTest');
		$suite->addTestSuite('org_tubepress_util_TimeUtilsTest');
		$suite->addTestSuite('org_tubepress_util_OptionsReferenceTest');
		return $suite;
	}
}
?>
