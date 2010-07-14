<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/querystring/QueryStringService.class.php';

class org_tubepress_querystring_SimpleQueryStringServiceTest extends PHPUnit_Framework_TestCase {
	
	public function testGetPageNumNothingSet()
	{
		$this->assertEquals(1, org_tubepress_querystring_QueryStringService::getPageNum(array()));
	}
	
	public function testGetPageNumLessThanOne()
	{
		$this->assertEquals(1, org_tubepress_querystring_QueryStringService::getPageNum(array("tubepress_page" => -1)));
	}
	
	public function testGetPageNumNonNumeric()
	{
		$this->assertEquals(1, org_tubepress_querystring_QueryStringService::getPageNum(array("tubepress_page" => "fake")));
	}
	
	public function testGetFullUrlHttpsOn()
	{
		$serverVars = array("HTTPS" => "on",
							"SERVER_PORT" => "443",
							"SERVER_NAME" => "fake.com",
							"REQUEST_URI" => "/index.html");
		$this->assertEquals("https://fake.com:443/index.html", 
		    org_tubepress_querystring_QueryStringService::getFullUrl($serverVars));
	}
	
	public function testGetFullUrlHttpsOff()
	{
		$serverVars = array("HTTPS" => "off",
							"SERVER_PORT" => "80",
							"SERVER_NAME" => "fake.com",
							"REQUEST_URI" => "/index.html");
		$this->assertEquals("http://fake.com/index.html", 
		    org_tubepress_querystring_QueryStringService::getFullUrl($serverVars));
	}
}
?>