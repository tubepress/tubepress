<?php

require_once dirname(__FILE__) . '/../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/single/Video.class.php';

class org_tubepress_single_VideoTest extends TubePressUnitTest
{
	function setup()
	{
	    org_tubepress_log_Log::setEnabled(false, array());
	}
    
	function testGetHtml()
	{
	   $ioc = $this->getIoc();
	   $urlBuilder = $ioc->get(org_tubepress_ioc_IocService::URL_BUILDER_YOUTUBE);
       $urlBuilder->expects($this->once())
                          ->method('buildSingleVideoUrl')
                          ->will($this->returnValue('fakeurl'));

       $factory = $ioc->get(org_tubepress_ioc_IocService::VIDEO_FACTORY_YOUTUBE);
       $factory->expects($this->once())
                               ->method('convertSingleVideo')
                               ->will($this->returnValue($this->fakeVideos()));

		$result = org_tubepress_single_Video::getSingleVideoHtml('someid', $ioc);
		
		$this->assertEquals($this->expected(), $result);
	}
	
	function expected()
	{
	    return <<<EOT

<div class="tubepress_single_video">
        <span class="tubepress_embedded_title"></span>
    <dl class="tubepress_meta_group" style="width: 425px">
    <dt class="tubepress_meta tubepress_meta_runtime">video-length</dt><dd class="tubepress_meta tubepress_meta_runtime"></dd>
    <dt class="tubepress_meta tubepress_meta_views">video-views</dt><dd class="tubepress_meta tubepress_meta_views"></dd>
</dl>
</div>
EOT;
	}
}
?>