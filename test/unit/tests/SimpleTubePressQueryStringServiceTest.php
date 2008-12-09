<?php
class SimpleTubePressQueryStringServiceTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
	public function setUp()
	{
		$this->_sut = new SimpleTubePressQueryStringService();
	}
	
	public function testGetPageNumNothingSet()
	{
		$this->assertEquals(1, $this->_sut->getPageNum(array()));
	}
}
?>