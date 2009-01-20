<?php

require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
include dirname(__FILE__) . "/../../common/tubepress_classloader.php";

$testNames = array(
	"org_tubepress_gallery_Gallery"
);

foreach ($testNames as $test) {
	require_once "tests/" . $test . "Test.php";
}

function __($key) {
	return $key;
}

class TubePressIntegrationTests
{
	public static function suite()
	{
		global $testNames;
		$suite = new PHPUnit_Framework_TestSuite("TubePress Integration Tests");
		foreach ($testNames as $test) {
			$suite->addTestSuite($test . "Test");
		}
		return $suite;
	}
}

?>
