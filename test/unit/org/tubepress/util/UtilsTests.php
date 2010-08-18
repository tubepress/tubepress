<?php
require_once 'PHPUnit/Framework.php';
require_once 'StringUtilsTest.php';
require_once 'FilesystemUtilsTest.php';
require_once 'TimeUtilsTest.php';

class UtilsTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress Utility Tests');
		$suite->addTestSuite('org_tubepress_util_StringUtilsTest');
		$suite->addTestSuite('org_tubepress_util_FilesystemUtilsTest');
		$suite->addTestSuite('org_tubepress_util_TimeUtilsTest');
		return $suite;
	}
}
?>
