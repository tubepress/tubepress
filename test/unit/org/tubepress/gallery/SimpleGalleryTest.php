<?php

require_once dirname(__FILE__) . '/../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/gallery/SimpleGallery.class.php';

class org_tubepress_gallery_SimpleGalleryTest extends TubePressUnitTest
{
	private $_sut;
	private static $_feedResult;

	function setup()
	{
		self::$_feedResult = new org_tubepress_video_feed_FeedResult();
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_gallery_SimpleGallery();
	}

	public function getMock($className)
	{
		$mock = parent::getMock($className);

		switch ($className) {
			case 'org_tubepress_video_feed_provider_Provider':
				$mock->expects($this->any())
					->method('getMultipleVideos')
					->will($this->returnValue(self::$_feedResult));
				break;
			case 'org_tubepress_querystring_QueryStringService':
				$mock->expects($this->any())
					->method('getGalleryId')
					->will($this->returnValue('FAKEID'));
				break;
		}

		return $mock;
	}

    function testGetHtml()
    {
	$result = $this->_sut->getHtml();
        $this->assertEquals($this->expected(), $result);
    }
    
    function expected()
    {
        return <<<EOT

<div class="tubepress_container" id="tubepress_gallery_FAKEID">
  <div id="tubepress_gallery_FAKEID_thumbnail_area" class="tubepress_thumbnail_area">
    <div class="tubepress_thumbs">
    </div>
      </div>
  <script type="text/javascript">
    jQuery(document).ready(function(){
       TubePressGallery.fluidThumbs("#tubepress_gallery_FAKEID",  120);
    });
    jQuery(window).resize(function(){
       TubePressGallery.fluidThumbs("#tubepress_gallery_FAKEID",  120);
    });
  </script>
</div>

EOT;
    }
}
?>
