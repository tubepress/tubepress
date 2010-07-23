<?php

require_once dirname(__FILE__) . '/../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/single/Video.class.php';

class org_tubepress_single_VideoTest extends TubePressUnitTest
{
	
	function testGetHtml()
	{
		$result = org_tubepress_single_Video::getSingleVideoHtml('someid', $this->getIoc());	
		$this->assertEquals($this->expected(), $result);
	}
	
	function expected()
	{
	    return <<<EOT

<div class="tubepress_single_video">
        <span class="tubepress_embedded_title"></span>
    embedded html source
    <dl class="tubepress_meta_group" style="width: 425px">
    <dt class="tubepress_meta tubepress_meta_runtime">video-length</dt><dd class="tubepress_meta tubepress_meta_runtime"></dd>
    <dt class="tubepress_meta tubepress_meta_views">video-views</dt><dd class="tubepress_meta tubepress_meta_views"></dd>
</dl>
</div>
EOT;
	}
}
?>