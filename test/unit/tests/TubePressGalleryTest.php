<?php
include_once dirname(__FILE__) . "/../../../tubepress_classloader.php";
class TubePressGalleryTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
	private $_cacheService;
	private $_paginationService;
	private $_feedInspectionService;
	private $_feedRetrievalService;
	private $_messageService;
	private $_optionsManager;
	private $_thumbService;
	private $_urlBuilderService;
	private $_videoFactory;
	
	function setUp()
	{
		$this->_sut = new TubePressGallery();
		$this->_createMocks();
		$this->_setupMocks();
		$this->_applyMocks();
	}
	
	function testGenerate()
	{	
		$fakeHtmlResult = <<<EOT
<div class="tubepress_container">
    
	<div id="tubepress_mainvideo">
	<div id="tubepress_inner" style="width: 500px">
    	<div id="tubepress_btitle"></div>
            <object type="application/x-shockwave-flash" style="width:500px;height:600px" data="http://www.youtube.com/v/&amp;rel=0&amp;autoplay=1&amp;loop=1&amp;egm=0&amp;border=1"><param name="wmode" value="transparent" /><param name="movie" value="http://www.youtube.com/v/&amp;rel=0&amp;autoplay=1&amp;loop=1&amp;egm=0&amp;border=1" /></object>
    </div><!-- tubepress_inner -->
</div> <!--tubepress_mainvideo--> <br />
	
	<div class="pagination">
	    Fakepagination
	</div>
	
	<div class="tubepress_video_thumbs">
		stuffstuffstuff
	</div><!-- tubepress_video_thumbs -->
	
	<div class="pagination">
	    Fakepagination
	</div>

</div><!-- tubepress_container -->
EOT;
		$this->assertEquals($fakeHtmlResult, $this->_sut->generate());
	}
	
	private function _setupMocks()
	{
		$fakeUrl = "http://fakeurl";
		$fakeXml = DOMDocument::load(dirname(__FILE__) . "/../sample_feed.xml");
		$fakeVideo = TubePressVideoTest::getFakeInstance(false);
		$fakeHtml = "stuff";
		
		$this->_urlBuilderService->expects($this->once())
								 ->method("buildGalleryUrl")
								 ->will($this->returnValue($fakeUrl));
		$this->_optionsManager->expects($this->any())
							  ->method("get")
							  ->will($this->returnCallback("_tpomCallback"));
		$this->_cacheService->expects($this->once())
							->method("has")
							->with($this->equalTo($fakeUrl))
							->will($this->returnValue(true));
		$this->_cacheService->expects($this->once())
							->method("get")
							->with($this->equalTo($fakeUrl))
							->will($this->returnValue($fakeXml));
							
		$this->_feedInspectionService->expects($this->once())
									 ->method("getTotalResultCount")
									 ->with($fakeXml)
									 ->will($this->returnValue(4));
		$this->_feedInspectionService->expects($this->once())
									 ->method("getQueryResultCount")
									 ->with($fakeXml)
									 ->will($this->returnValue(4));
		$this->_videoFactory->expects($this->exactly(3))
							->method("generate")
							->will($this->returnValue($fakeVideo));
		$this->_thumbService->expects($this->exactly(3))
							->method("getHtml")
							->will($this->returnValue($fakeHtml));
		$this->_paginationService->expects($this->once())
								 ->method("getHtml")
								 ->will($this->returnValue("Fakepagination"));
	}
	
	private function _applyMocks()
	{
		$this->_sut->setCacheService($this->_cacheService);
		$this->_sut->setPaginationService($this->_paginationService);
		$this->_sut->setFeedInspectionService($this->_feedInspectionService);
		$this->_sut->setFeedRetrievalService($this->_feedRetrievalService);
		$this->_sut->setMessageService($this->_messageService);
		$this->_sut->setOptionsManager($this->_optionsManager);
		$this->_sut->setThumbnailService($this->_thumbService);
		$this->_sut->setUrlBuilderService($this->_urlBuilderService);
		$this->_sut->setVideoFactory($this->_videoFactory);
	}
	
	private function _createMocks()
	{
		$this->_cacheService 			= $this->getMock("TubePressCacheService");
		$this->_paginationService 		= $this->getMock("TubePressPaginationService");
		$this->_feedInspectionService 	= $this->getMock("TubePressFeedInspectionService");
		$this->_feedRetrievalService 	= $this->getMock("TubePressFeedRetrievalService");
		$this->_messageService 			= $this->getMock("TubePressMessageService");
		$this->_optionsManager 			= $this->getMock("TubePressOptionsManager");
		$this->_thumbService 			= $this->getMock("TubePressThumbnailService");
		$this->_urlBuilderService 		= $this->getMock("TubePressUrlBuilder");
		$this->_videoFactory 			= $this->getMock("TubePressVideoFactory");
	}
	
}

function _tpomCallback()
{
	$args = func_get_args();
	$vals = array(
		TubePressAdvancedOptions::CACHE_ENABLED => true,
		TubePressDisplayOptions::RESULTS_PER_PAGE => 3,
		TubePressDisplayOptions::ORDER_BY => "relevance",
		TubePressDisplayOptions::CURRENT_PLAYER_NAME => "normal",
		TubePressEmbeddedOptions::EMBEDDED_WIDTH => 500,
		TubePressEmbeddedOptions::EMBEDDED_HEIGHT => 600,
        TubePressEmbeddedOptions::SHOW_RELATED => false,
        TubePressEmbeddedOptions::PLAYER_COLOR => "/",
        TubePressEmbeddedOptions::AUTOPLAY => true,
        TubePressEmbeddedOptions::LOOP => true,
        TubePressEmbeddedOptions::GENIE => false,
        TubePressEmbeddedOptions::BORDER => true
	);
	return $vals[$args[0]];
}
?>
