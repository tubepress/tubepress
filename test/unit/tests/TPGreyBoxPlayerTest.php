<?php
include_once dirname(__FILE__) . "/../../../tubepress_classloader.php";

class TPGreyBoxPlayerTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
	function setUp()
	{
		$this->_sut = new TPGreyBoxPlayer();
		$tpom = $this->getMock("TubePressOptionsManager");
		
	}
	
	
	
	function testGetPlayLink()
	{
		
	}
}