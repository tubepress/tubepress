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
	
	public function testGetPageNumLessThanOne()
	{
		$this->assertEquals(1, $this->_sut->getPageNum(array("tubepress_page" => -1)));
	}
	
	public function testGetPageNumNonNumeric()
	{
		$this->assertEquals(1, $this->_sut->getPageNum(array("tubepress_page" => "fake")));
	}
	
	public function testGetFullUrlHttpsOn()
	{
		$serverVars = array("HTTPS" => "on",
							"SERVER_PORT" => "443",
							"SERVER_NAME" => "fake.com",
							"REQUEST_URI" => "/index.html");
		$this->assertEquals("https://fake.com:443/index.html", $this->_sut->getFullUrl($serverVars));
	}
	
	public function testGetFullUrlHttpsOff()
	{
		$serverVars = array("HTTPS" => "off",
							"SERVER_PORT" => "80",
							"SERVER_NAME" => "fake.com",
							"REQUEST_URI" => "/index.html");
		$this->assertEquals("http://fake.com/index.html", $this->_sut->getFullUrl($serverVars));
	}
}
?>