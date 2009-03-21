<?php
require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once 'org/tubepress/cache/CacheTests.php';
require_once 'org/tubepress/embedded/EmbeddedTests.php';

class TubePressUnitTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Unit Tests");
		$suite->addTest(CacheTests::suite());
		$suite->addTest(EmbeddedTests::suite());
		return $suite;
	}
}

?>
