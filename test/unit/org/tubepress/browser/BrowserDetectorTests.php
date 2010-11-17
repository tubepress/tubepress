<?php
require dirname(__FILE__) . '/../../../PhpUnitLoader.php';
require_once 'BrowserDetectorTest.php';

class BrowserDetectorTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Browser Detector Tests");
		$suite->addTestSuite('org_tubepress_api_http_AgentDetectorTest');
		return $suite;
	}
}
?>
