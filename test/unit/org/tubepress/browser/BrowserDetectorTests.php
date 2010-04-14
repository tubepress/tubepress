<?php
require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once 'BrowserDetectorTest.php';

class BrowserDetectorTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Browser Detector Tests");
		$suite->addTestSuite('org_tubepress_browser_BrowserDetectorTest');
		return $suite;
	}
}
?>