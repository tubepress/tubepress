<?php

require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/feed/HTTPRequest2FeedFetcher.class.php';

class org_tubepress_impl_feed_HTTPRequest2FeedFetcherTest extends TubePressUnitTest {
    
	private $_sut;
	
	function setUp()
	{
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_impl_feed_HTTPRequest2FeedFetcher();
        org_tubepress_util_Log::setEnabled(false, array());
	}
	
	function testFetchGoodXmlCacheDisabled()
	{
		$this->_sut->fetch("http://www.ietf.org/css/ietf.css", false);
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
