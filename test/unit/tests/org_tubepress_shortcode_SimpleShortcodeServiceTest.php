<?php
class org_tubepress_shortcode_SimpleShortcodeServiceTest extends PHPUnit_Framework_TestCase
{
	private $_sut;
	private $_tpom;
	
	function setUp()
	{
		$this->_tpom = $this->getMock("TubePressOptionsManager");
		$this->_sut = new org_tubepress_shortcode_SimpleShortcodeService();
	}
	
	function testParse()
	{
		$content = "bla bla bla [butters mode='playlist'] bla bla bla";
		$this->_tpom->expects($this->once())
					->method("get")
					->will($this->returnValue("butters"));
		
		$this->_sut->parse($content, $this->_tpom);
	}
}
?>