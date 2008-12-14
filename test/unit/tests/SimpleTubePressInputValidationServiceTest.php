<?php
include_once dirname(__FILE__) . "/../../../tubepress_classloader.php";

class SimpleTubePressInputValidationServiceTest extends PHPUnit_Framework_TestCase {
	
	private $_sut;
	
	public function setUp()
	{
		$this->_sut = new SimpleTubePressInputValidationService();
	}
	
	public function testThumbHeightOk()
	{
		
	}
}
?>