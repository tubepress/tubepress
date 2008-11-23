<?php

require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
include dirname(__FILE__) . "/../tubepress_classloader.php";

require_once "common/class/TubePressVideoTest.php";
require_once "common/class/util/TubePressStringUtilsTest.php";
require_once "common/class/util/TubePressShortcodeTest.php";
require_once "common/class/options/SimpleTubePressOptionsManagerTest.php";
require_once "common/class/options/TubePressValidatorTest.php";
require_once "common/class/options/category/TubePressAdvancedOptionsTest.php";

function __($key) {
	return $key;
}

class TubePressTestSuite
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Tests");
		$suite->addTestSuite("TubePressVideoTest");
		$suite->addTestSuite("TubePressStringUtilsTest");
		$suite->addTestSuite("TubePressShortcodeTest");
		$suite->addTestSuite("SimpleTubePressOptionsManagerTest");
		$suite->addTestSuite("TubePressValidatorTest");
		$suite->addTestSuite("TubePressAdvancedOptionsTest");
		return $suite;
	}
}

?>
