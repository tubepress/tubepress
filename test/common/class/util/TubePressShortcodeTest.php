<?php
class TubePressShortcodeTest extends PHPUnit_Framework_TestCase
{
	private $_tpom;
	
	
	function testParseWithCommas()
	{
		$this->_tpomSetup();
		$this->_tpom->expects($this->any())
			 ->method("setCustomOptions")
			 ->with($this->equalTo(array(TubePressMetaOptions::AUTHOR => true)));
			 
		TubePressShortcode::parse("[tubepress author='true']", $this->_tpom);
	}
	
	function testSomethingToParse()
	{
		$this->assertTrue(TubePressShortcode::somethingToParse("[tubepress", "tubepress"));
	}
	
	function testNoBracketsParse()
	{
		$this->assertFalse(TubePressShortcode::somethingToParse("tubepress", "tubepress"));
	}
	
	function testNoValidOptionsParse()
	{
		
	}
	
	function testSomethingToParseEmptyTrigger()
	{
		$this->assertTrue(TubePressShortcode::somethingToParse("[tubepress", ""));
	}
	
	private function _tpomSetup()
	{
		$this->_tpom = $this->getMock("TubePressOptionsManager");
		$this->_tpom->expects($this->any())
			 ->method("get")
			 ->with($this->equalTo(TubePressAdvancedOptions::KEYWORD))
			 ->will($this->returnValue("tubepress"));
	}
}
?>