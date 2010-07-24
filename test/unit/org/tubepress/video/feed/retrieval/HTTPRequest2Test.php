<?php

require_once dirname(__FILE__) . '/../../../../../../../test/unit/TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/video/feed/retrieval/HTTPRequest2.class.php';

class org_tubepress_video_feed_retrieval_HTTPRequest2Test extends TubePressUnitTest {
    
	private $_sut;
	
	function setUp()
	{
		$this->_sut = new org_tubepress_video_feed_retrieval_HTTPRequest2();
        org_tubepress_log_Log::setEnabled(false, array());
	}
	
	function testFetchGoodXmlCacheDisabled()
	{
		$this->_sut->fetch($this->getIoc(), "http://tubepress.org/goodxml.test", false);
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