<?php

require_once dirname(__FILE__) . '/../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/single/SimpleSingleVideo.class.php';

class org_tubepress_single_VideoTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_single_SimpleSingleVideo();
	    org_tubepress_log_Log::setEnabled(false, array());
	}
    
	public function getMock($className)
	{
		$mock = parent::getMock($className);

		switch ($className) {
			case 'org_tubepress_video_feed_provider_Provider':
				$mock->expects($this->any())
					->method('getSingleVideo')
					->will($this->returnValue(new org_tubepress_video_Video()));
		}

		return $mock;
	}

	function testGetHtml()
	{
		$result = $this->_sut->getSingleVideoHtml('someid');
		
		$this->assertEquals($this->expected(), $result);
	}
	
	function expected()
	{
	    return '
<div class="tubepress_single_video">
        <div class="tubepress_embedded_title"></div>
    <dl class="tubepress_meta_group" style="width: 425px">
    <dt class="tubepress_meta tubepress_meta_runtime">video-length</dt><dd class="tubepress_meta tubepress_meta_runtime"></dd>
    <dt class="tubepress_meta tubepress_meta_views">video-views</dt><dd class="tubepress_meta tubepress_meta_views"></dd>
</dl>
</div>
';
	}
}
?>
