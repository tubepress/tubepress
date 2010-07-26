<?php
require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/browser/BrowserDetector.class.php';

class org_tubepress_browser_BrowserDetectorTest extends PHPUnit_Framework_TestCase {

	function testIphone()
	{
		$this->assertTrue(org_tubepress_browser_BrowserDetector::isMobile(array('HTTP_USER_AGENT' => 'iPhone')));
	}
	
    function testIpod()
	{
		$this->assertTrue(org_tubepress_browser_BrowserDetector::isMobile(array('HTTP_USER_AGENT' => 'iPod')));
	}
	
	function testOther()
	{
		$this->assertFalse(org_tubepress_browser_BrowserDetector::isMobile(array('HTTP_USER_AGENT' => 'somethingelse')));
	}
}
?>

