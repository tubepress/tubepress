<?php
function_exists('tubepress_load_classes')
    || require(dirname(__FILE__) . '/../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_gallery_Gallery',
    'org_tubepress_pagination_PaginationService',
    'org_tubepress_gdata_inspection_FeedInspectionService',
    'org_tubepress_gdata_retrieval_FeedRetrievalService',
    'org_tubepress_message_MessageService',
    'org_tubepress_options_manager_OptionsManager',
    'org_tubepress_querystring_QueryStringService',
    'org_tubepress_thumbnail_ThumbnailService',
    'org_tubepress_url_UrlBuilder',
    'org_tubepress_video_factory_VideoFactory',
    'org_tubepress_embedded_EmbeddedPlayerService',
    'org_tubepress_ioc_IocService'));

require_once dirname(__FILE__) . '/../video/org_tubepress_video_VideoTest.php';

class org_tubepress_gallery_GalleryTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
	private $_paginationService;
	private $_feedInspectionService;
	private $_feedRetrievalService;
	private $_ioc;
	private $_messageService;
	private $_optionsManager;
	private $_qss;
	private $_thumbService;
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
    
	pregallerystuff
	
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
		$fakeXml = DOMDocument::load(dirname(__FILE__) . "/../../../sample_feed.xml");
		$fakeVideo = org_tubepress_video_VideoTest::getFakeInstance(false);
		$fakeHtml = "stuff";
		
		$this->_urlBuilderService->expects($this->once())
								 ->method("buildGalleryUrl")
								 ->will($this->returnValue($fakeUrl));
		$this->_optionsManager->expects($this->any())
							  ->method("get")
							  ->will($this->returnCallback("_tpomCallbackGalleryUnitTest"));
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
	    $fakePlayer = $this->getMock('org_tubepress_player_Player');
	    $fakePlayer->expects($this->once())
	               ->method('getPreGalleryHtml')
	               ->will($this->returnValue('pregallerystuff'));
        $this->_ioc->expects($this->once())
                   ->method('safeGet')
                   ->will($this->returnValue($fakePlayer));
	}
	
	private function _applyMocks()
	{
		$this->_sut->setPaginationService($this->_paginationService);
		$this->_sut->setFeedInspectionService($this->_feedInspectionService);
		$this->_sut->setFeedRetrievalService($this->_feedRetrievalService);
		$this->_sut->setMessageService($this->_messageService);
		$this->_sut->setOptionsManager($this->_optionsManager);
		$this->_sut->setQueryStringService($this->_qss);
		$this->_sut->setThumbnailService($this->_thumbService);
		$this->_sut->setUrlBuilderService($this->_urlBuilderService);
		$this->_sut->setVideoFactory($this->_videoFactory);
		$this->_sut->setContainer($this->_ioc);
	}
	
	private function _createMocks()
	{
		$this->_paginationService 		= $this->getMock("org_tubepress_pagination_PaginationService");
		$this->_feedInspectionService 	= $this->getMock("org_tubepress_gdata_inspection_FeedInspectionService");
		$this->_feedRetrievalService 	= $this->getMock("org_tubepress_gdata_retrieval_FeedRetrievalService");
		$this->_messageService 			= $this->getMock("org_tubepress_message_MessageService");
		$this->_optionsManager 			= $this->getMock("org_tubepress_options_manager_OptionsManager");
		$this->_qss						= $this->getMock("org_tubepress_querystring_QueryStringService");
		$this->_thumbService 			= $this->getMock("org_tubepress_thumbnail_ThumbnailService");
		$this->_urlBuilderService 		= $this->getMock("org_tubepress_url_UrlBuilder");
		$this->_videoFactory 			= $this->getMock("org_tubepress_video_factory_VideoFactory");
		$this->_tpeps					= $this->getMock("org_tubepress_embedded_EmbeddedPlayerService");
		$this->_ioc                     = $this->getMock('org_tubepress_ioc_IocService');
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
