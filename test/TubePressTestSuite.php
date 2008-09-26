<?php

require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit.php';
include dirname(__FILE__) . "/../tubepress_classloader.php";

require_once "common/class/TubePressVideoTest.php";
require_once "common/class/util/TubePressStringUtilsTest.php";
require_once "common/class/util/TubePressShortcodeTest.php";
require_once "common/class/options/TubePressStorageManagerTest.php";

class TubePressTestSuite
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Tests");
		$suite->addTestSuite("TubePressVideoTest");
		$suite->addTestSuite("TubePressStringUtilsTest");
		$suite->addTestSuite("TubePressShortcodeTest");
		$suite->addTestSuite("TubePressStorageManagerTest");
		return $suite;
	}
}

?>
