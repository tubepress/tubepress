<?php

require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
include dirname(__FILE__) . "/../tubepress_classloader.php";

$testNames = array(
	"SimpleTubePressVideoFactoryTest",
	"TubePressStringUtilsTest",
	"TubePressShortcodeTest",
	"SimpleTubePressOptionsManagerTest",
	"TubePressValidatorTest",
	"TubePressVideoTest",
	"TubePressAdvancedOptionsTest",
	"TubePressEmbeddedPlayerTest",
	"TubePressPaginationService_DiggStyleTest",
	"SimpleTubePressQueryStringServiceTest"
);

foreach ($testNames as $test) {
	require_once "tests/" . $test . ".php";
}

function __($key) {
	return $key;
}

class TubePressTestSuite
{
	public static function suite()
	{
		global $testNames;
		$suite = new PHPUnit_Framework_TestSuite("TubePress Tests");
		foreach ($testNames as $test) {
			$suite->addTestSuite($test);
		}
		return $suite;
	}
}

?>
