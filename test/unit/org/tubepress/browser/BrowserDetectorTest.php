<?php
require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/browser/BrowserDetectorImpl.class.php';

class org_tubepress_browser_BrowserDetectorTest extends PHPUnit_Framework_TestCase {

	private $_sut;

	function setUp()
	{
		$this->_sut = new org_tubepress_browser_BrowserDetectorImpl();
	}

	function testNonArray()
	{
		$result = $this->_sut->detectBrowser('fake');	
		$this->assertEquals(org_tubepress_browser_BrowserDetector::UNKNOWN, $result);
	}
	
	function testIphone()
	{
		$result = $this->_sut->detectBrowser(array('HTTP_USER_AGENT' => 'iPhone'));	
		$this->assertEquals(org_tubepress_browser_BrowserDetector::IPHONE, $result);
	}
	
    function testIpod()
	{
		$result = $this->_sut->detectBrowser(array('HTTP_USER_AGENT' => 'iPod'));	
		$this->assertEquals(org_tubepress_browser_BrowserDetector::IPOD, $result);
	}
	
	function testOther()
	{
		$result = $this->_sut->detectBrowser(array('HTTP_USER_AGENT' => 'somethingelse'));	
		$this->assertEquals(org_tubepress_browser_BrowserDetector::UNKNOWN, $result);
	}
}
?>

