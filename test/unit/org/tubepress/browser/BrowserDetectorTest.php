<?php
require_once dirname(__FILE__) . '/../../../../../test/unit/TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/browser/MobileEspBrowserDetector.class.php';

class org_tubepress_api_http_AgentDetectorTest extends TubePressUnitTest {

    private $_sut;
    
    function setup()
    {
        $this->_sut = new org_tubepress_browser_MobileEspBrowserDetector();
    }
    
	function testIphone()
	{
		$this->assertTrue($this->_sut->isMobileQuick(array('HTTP_USER_AGENT' => 'iPhone')));
	}
	
    function testIpod()
	{
		$this->assertTrue($this->_sut->isMobileQuick(array('HTTP_USER_AGENT' => 'iPod')));
	}
	
	function testOther()
	{
		$this->assertFalse($this->_sut->isMobileQuick(array('HTTP_USER_AGENT' => 'somethingelse')));
	}
}
?>

