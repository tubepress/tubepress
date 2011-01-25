<?php
require_once dirname(__FILE__) . '/../../../../../../test/unit/TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/http/MobileEspBrowserDetector.class.php';

class org_tubepress_impl_http_MobileEspAgentDetectorTest extends TubePressUnitTest {

    private $_sut;
    
    function setup()
    {
        $this->_sut = new org_tubepress_impl_http_MobileEspBrowserDetector();
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

