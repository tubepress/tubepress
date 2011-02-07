<?php

require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/impl/filters/template/Player.class.php';

class org_tubepress_impl_filters_template_PlayerTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_impl_filters_template_Player();
	}
	
	function getMock($className)
	{
	    $mock = parent::getMock($className);

	    
	    return $mock;
	}

	function testPlayerAboveAndBelow()
	{
	    $fakeTemplate = $this->getMock('org_tubepress_api_template_Template');
	    $feedResult = $this->getMock('org_tubepress_api_feed_FeedResult');
	    $feedResult->expects($this->once())
	               ->method('getVideoArray')
	               ->will($this->returnValue(array(new org_tubepress_api_video_Video())));
	    $this->_sut->filter($fakeTemplate, $feedResult, 3);
	}
	
}
?>
