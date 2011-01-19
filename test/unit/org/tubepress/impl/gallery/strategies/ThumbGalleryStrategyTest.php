<?php

require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/impl/gallery/strategies/ThumbGalleryStrategy.class.php';

class org_tubepress_impl_gallery_strategies_ThumbGalleryStrategyTest extends TubePressUnitTest
{
	private $_sut;
	private $_galleryId;
	private $_feedResult;

	function setup()
	{
		$this->initFakeIoc();
		$this->_feedResult = new org_tubepress_api_feed_FeedResult();
		$this->_feedResult->setVideoArray(array(new org_tubepress_api_video_Video()));
		$this->_sut = new org_tubepress_impl_gallery_strategies_ThumbGalleryStrategy();
	}

	function getMock($className)
	{
	    $mock = parent::getMock($className);
	    
	    if ($className === 'org_tubepress_api_querystring_QueryStringService') {
	        $mock->expects($this->any())
	             ->method('getGalleryId')
	             ->will($this->returnCallback(array($this, 'galleryIdCallback')));
	    }
	    if ($className === 'org_tubepress_api_provider_Provider') {
	        $mock->expects($this->any())
	             ->method('getMultipleVideos')
	             ->will($this->returnValue($this->_feedResult));
	    }
	    if ($className === 'org_tubepress_api_patterns_FilterManager') {
                $mock->expects($this->exactly(3))
                     ->method('runFilters')
                     ->will($this->returnCallback(array($this, 'callback')));
	    }
	    
	    return $mock;
	}
	
	function galleryIdCallback()
	{
	    return $this->_galleryId;
	}
	
	function testExecute()
	{
	    $this->_sut->start();
	    
	    $this->_galleryId = '390298742';
	    $result = $this->_sut->execute();
	    
	    $this->assertEquals($this->expected(), $result);
	    
	    $this->_sut->stop();
	}

    function testCanHandle()
    {
        $this->_sut->start();
        $this->assertTrue($this->_sut->canHandle());
        $this->_sut->stop();
    }
    
    function callback()
    {
        $args = func_get_args();
        return $args[1];
    }
    
    function expected()
    {
        return <<<EOT

<div class="tubepress_container" id="tubepress_gallery_390298742">
  <div id="tubepress_gallery_390298742_thumbnail_area" class="tubepress_thumbnail_area">
    <div class="tubepress_thumbs">
      <div class="tubepress_thumb">
        <a id="tubepress_image__390298742" rel="tubepress___390298742"> 
          <img alt="" src="" width="120" height="90" />
        </a>
        <dl class="tubepress_meta_group">
        </dl>
      </div>
    </div>
      </div>
  <script type="text/javascript">
    jQuery(document).ready(function(){
       TubePressGallery.fluidThumbs("#tubepress_gallery_390298742",  120);
    });
    jQuery(window).resize(function(){
       TubePressGallery.fluidThumbs("#tubepress_gallery_390298742",  120);
    });
  </script>
</div>

EOT;
    }
    

}
?>
