<?php
require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/browser/BrowserDetector.class.php';

class org_tubepress_browser_BrowserDetectorTest extends PHPUnit_Framework_TestCase {

	function testNonArray()
	{
		$result = org_tubepress_browser_BrowserDetector::detectBrowser('fake');	
		$this->assertEquals(org_tubepress_browser_BrowserDetector::UNKNOWN, $result);
	}
	
	function testIphone()
	{
		$result = org_tubepress_browser_BrowserDetector::detectBrowser(array('HTTP_USER_AGENT' => 'iPhone'));	
		$this->assertEquals(org_tubepress_browser_BrowserDetector::IPHONE, $result);
	}
	
    function testIpod()
	{
		$result = org_tubepress_browser_BrowserDetector::detectBrowser(array('HTTP_USER_AGENT' => 'iPod'));	
		$this->assertEquals(org_tubepress_browser_BrowserDetector::IPOD, $result);
	}
	
	function testOther()
	{
		$result = org_tubepress_browser_BrowserDetector::detectBrowser(array('HTTP_USER_AGENT' => 'somethingelse'));	
		$this->assertEquals(org_tubepress_browser_BrowserDetector::UNKNOWN, $result);
	}
}
?>

