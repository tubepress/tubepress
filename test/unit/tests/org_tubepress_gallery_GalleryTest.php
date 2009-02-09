<?php
class org_tubepress_gallery_GalleryTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
	private $_paginationService;
	private $_feedInspectionService;
	private $_feedRetrievalService;
	private $_messageService;
	private $_optionsManager;
	private $_playerFactory;
	private $_qss;
	private $_thumbService;
	private $_tpeps;
	private $_urlBuilderService;
	private $_videoFactory;
	
	function setUp()
	{
		$this->_sut = new org_tubepress_gallery_Gallery();
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
            embeddedstuffgoeshere
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
		$fakeVideo = org_tubepress_video_VideoTest::getFakeInstance(false);
		$fakeHtml = "stuff";
		
		$this->_urlBuilderService->expects($this->once())
								 ->method("buildGalleryUrl")
								 ->will($this->returnValue($fakeUrl));
		$this->_optionsManager->expects($this->any())
							  ->method("get")
							  ->will($this->returnCallback("_tpomCallbackGalleryUnitTest"));
		$this->_playerFactory->expects($this->once())
							 ->method("getInstance")
							 ->will($this->returnValue(new org_tubepress_player_impl_NormalPlayer()));
		$this->_feedRetrievalService->expects($this->once())
									->method("fetch")
									->will($this->returnValue($fakeXml));
		$this->_feedInspectionService->expects($this->once())
									 ->method("getTotalResultCount")
									 ->with($fakeXml)
									 ->will($this->returnValue(4));
		$this->_feedInspectionService->expects($this->once())
									 ->method("getQueryResultCount")
									 ->with($fakeXml)
									 ->will($this->returnValue(4));
		$this->_tpeps->expects($this->once())
					 ->method("toString")
					 ->will($this->returnValue("embeddedstuffgoeshere"));
		$this->_qss->expects($this->once())
				   ->method("getPageNum")
				   ->will($this->returnValue(1));
		$this->_videoFactory->expects($this->once())
							->method("dom2TubePressVideoArray")
							->will($this->returnValue(array($fakeVideo, $fakeVideo, $fakeVideo)));
		$this->_thumbService->expects($this->exactly(3))
							->method("getHtml")
							->will($this->returnValue($fakeHtml));
		$this->_paginationService->expects($this->once())
								 ->method("getHtml")
								 ->will($this->returnValue("Fakepagination"));
	}
	
	private function _applyMocks()
	{
		$this->_sut->setPaginationService($this->_paginationService);
		$this->_sut->setFeedInspectionService($this->_feedInspectionService);
		$this->_sut->setFeedRetrievalService($this->_feedRetrievalService);
		$this->_sut->setMessageService($this->_messageService);
		$this->_sut->setOptionsManager($this->_optionsManager);
		$this->_sut->setPlayerFactory($this->_playerFactory);
		$this->_sut->setQueryStringService($this->_qss);
		$this->_sut->setThumbnailService($this->_thumbService);
		$this->_sut->setUrlBuilderService($this->_urlBuilderService);
		$this->_sut->setVideoFactory($this->_videoFactory);
		$this->_sut->setEmbeddedPlayerService($this->_tpeps);
	}
	
	private function _createMocks()
	{
		$this->_paginationService 		= $this->getMock("org_tubepress_pagination_PaginationService");
		$this->_feedInspectionService 	= $this->getMock("org_tubepress_gdata_inspection_FeedInspectionService");
		$this->_feedRetrievalService 	= $this->getMock("org_tubepress_gdata_retrieval_FeedRetrievalService");
		$this->_messageService 			= $this->getMock("org_tubepress_message_MessageService");
		$this->_optionsManager 			= $this->getMock("org_tubepress_options_manager_OptionsManager");
		$this->_playerFactory			= $this->getMock("org_tubepress_player_factory_PlayerFactory");
		$this->_qss						= $this->getMock("org_tubepress_querystring_QueryStringService");
		$this->_thumbService 			= $this->getMock("org_tubepress_thumbnail_ThumbnailService");
		$this->_urlBuilderService 		= $this->getMock("org_tubepress_url_UrlBuilder");
		$this->_videoFactory 			= $this->getMock("org_tubepress_video_factory_VideoFactory");
		$this->_tpeps					= $this->getMock("org_tubepress_video_embed_EmbeddedPlayerService");
	}
	
}

function _tpomCallbackGalleryUnitTest()
{
	$args = func_get_args();
	$vals = array(
		org_tubepress_options_category_Feed::CACHE_ENABLED => true,
		org_tubepress_options_category_Display::RESULTS_PER_PAGE => 3,
		org_tubepress_options_category_Display::ORDER_BY => "relevance",
		org_tubepress_options_category_Display::CURRENT_PLAYER_NAME => "normal",
		org_tubepress_options_category_Embedded::EMBEDDED_WIDTH => 500,
		org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT => 600,
        org_tubepress_options_category_Embedded::SHOW_RELATED => false,
        org_tubepress_options_category_Embedded::PLAYER_COLOR => "/",
        org_tubepress_options_category_Embedded::AUTOPLAY => true,
        org_tubepress_options_category_Embedded::LOOP => true,
        org_tubepress_options_category_Embedded::GENIE => false,
        org_tubepress_options_category_Embedded::BORDER => true
	);
	return $vals[$args[0]];
}
?>
