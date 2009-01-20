<?php
class org_tubepress_gdata_retrieval_HTTPRequest2Test extends PHPUnit_Framework_TestCase {
    
	private $_mockCache;
	private $_sut;
	
	function setUp()
	{
		$this->_mockCache = $this->getMock("org_tubepress_cache_CacheService");
		$this->_sut = new org_tubepress_gdata_retrieval_HTTPRequest2();
	}
	
    /**
     * @expectedException Exception
     */
	function testFetchNonXml()
	{
		$this->_sut->fetch("http://www.gnu.org/licenses/gpl-3.0.txt", false);
	}
	
    /**
     * @expectedException Exception
     */	
	function testFetchBadXml()
	{
		$this->_sut->fetch("http://tubepress.org/badxml.test", false);
	}
	
	function testFetchGoodXmlCacheDisabled()
	{
		$this->_sut->fetch("http://tubepress.org/goodxml.test", false);
	}

	function testFetchGoodXmlCacheMiss()
	{
		$this->_mockCache->expects($this->once())
						 ->method("has")
						 ->will($this->returnValue(false));
		$this->_sut->setCacheService($this->_mockCache);
		$this->_sut->fetch("http://tubepress.org/goodxml.test", true);
	}	
	
	/**
     * @expectedException Exception
     */
	function getExpectedNetworkOutput($url)
	{
		$ch = curl_init($url);
        $output = curl_exec($ch);      
        curl_close($ch);
        return $output;
	}
}
?>