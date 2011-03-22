<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/api/feed/FeedResult.class.php';
require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';

class org_tubepress_api_feed_FeedResultTest extends TubePressUnitTest {
    
	private $_sut;
	
	function setUp()
	{
		$this->_sut = new org_tubepress_api_feed_FeedResult();
	}
	

}
?>
