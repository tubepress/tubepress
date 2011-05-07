<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'StringUtilsTest.php';
require_once 'TimeUtilsTest.php';

class UtilsTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress Utility Tests');
		$suite->addTestSuite('org_tubepress_impl_util_StringUtilsTest');
		$suite->addTestSuite('org_tubepress_impl_util_TimeUtilsTest');
		return $suite;
	}
}

