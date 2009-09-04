<?php
class TubePressGalleryTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
	function setUp()
	{
		$this->_sut = new org_tubepress_gallery_TubePressGallery();
		
		$tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$tpom->expects($this->any())
			 ->method("get")
			 ->will($this->returnCallback("_tpomCallback"));
		$messageService = $this->getMock("org_tubepress_message_MessageService");
		$messageService->expects($this->any())
					   ->method("_")
					   ->will($this->returnCallback("_msgCallback"));
		
        $thumbService = new org_tubepress_thumbnail_SimpleThumbnailService();
        $thumbService->setOptionsManager($tpom);
        $thumbService->setMessageService($messageService);
        
        $queryStringService = new org_tubepress_querystring_SimpleQueryStringService();
        
        $urlBuilderService = new org_tubepress_url_SimpleUrlBuilder();
        $urlBuilderService->setOptionsManager($tpom);
        $urlBuilderService->setQueryStringService($queryStringService);
        
        $paginationService = new org_tubepress_pagination_DiggStylePaginationService();
        $paginationService->setMessageService($messageService);
        $paginationService->setOptionsManager($tpom);
        $paginationService->setQueryStringService($queryStringService);
        
        $this->_sut->setCacheService(             new org_tubepress_cache_SimpleCacheService());
        $this->_sut->setFeedInspectionService( new org_tubepress_video_feed_inspection_SimpleFeedInspectionService());
        $this->_sut->setFeedRetrievalService(     new org_tubepress_video_feed_retrieval_HTTPRequest2());
        $this->_sut->setOptionsManager(         $tpom);
        $this->_sut->setPaginationService(     $paginationService);
        $this->_sut->setThumbnailService(         $thumbService);
        $this->_sut->setUrlBuilderService(     $urlBuilderService);
        $this->_sut->setVideoFactory(             new org_tubepress_video_factory_SimpleVideoFactory());
	}
	
	function testGenerate()
	{	
		$htmlResult = <<<EOT
bla
EOT;
		$this->assertEquals($htmlResult, $this->_sut->generate());
	}
}

function _tpomCallback()
{
	$args = func_get_args();
	$vals = array(
    		org_tubepress_options_category_Advanced::DATEFORMAT 		 => "M j, Y",
    		org_tubepress_options_category_Advanced::DEBUG_ON 			 => true,
    		org_tubepress_options_category_Feed::FILTER 			 => false,
    		org_tubepress_options_category_Feed::CACHE_ENABLED 	 => false,
    		org_tubepress_options_category_Advanced::NOFOLLOW_LINKS 	 => true,
    		org_tubepress_options_category_Advanced::KEYWORD 			 => "tubepress",
    		org_tubepress_options_category_Advanced::RANDOM_THUMBS 	 => true,
    		org_tubepress_options_category_Feed::CLIENT_KEY 		 => "ytapi-EricHough-TubePress-ki6oq9tc-0",
    		org_tubepress_options_category_Advanced::RANDOM_THUMBS 	 => true,
    		org_tubepress_options_category_Feed::CLIENT_KEY 		 => "ytapi-EricHough-TubePress-ki6oq9tc-0",
    		org_tubepress_options_category_Feed::DEV_KEY 			 => "AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg",
    		org_tubepress_options_category_Display::CURRENT_PLAYER_NAME => "normal",
    		org_tubepress_options_category_Display::DESC_LIMIT 		 => 80,
    		org_tubepress_options_category_Display::ORDER_BY 			 => "viewCount",
    		org_tubepress_options_category_Display::RELATIVE_DATES 	 => false,
    		org_tubepress_options_category_Display::RESULTS_PER_PAGE 	 => 20,
    		org_tubepress_options_category_Display::THUMB_HEIGHT 		 => 90,
    		org_tubepress_options_category_Display::THUMB_WIDTH 		 => 120,
    		org_tubepress_options_category_Embedded::AUTOPLAY 			 => false,
    		org_tubepress_options_category_Embedded::BORDER 			 => false,
    		org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT 	 => 355,
    		org_tubepress_options_category_Embedded::EMBEDDED_WIDTH 	 => 425,
    		org_tubepress_options_category_Embedded::GENIE 			 => false,
    		org_tubepress_options_category_Embedded::LOOP 				 => false,
    		org_tubepress_options_category_Embedded::PLAYER_COLOR 		 => "/",
    		org_tubepress_options_category_Embedded::SHOW_RELATED 		 => true,
    		org_tubepress_options_category_Gallery::MODE 				 => "featured",
    		org_tubepress_options_category_Gallery::FAVORITES_VALUE 	 => "mrdeathgod",
    		org_tubepress_options_category_Gallery::MOST_VIEWED_VALUE 	 => "today",
    		org_tubepress_options_category_Gallery::PLAYLIST_VALUE 	 => "D2B04665B213AE35",
    		org_tubepress_options_category_Gallery::TAG_VALUE 			 => "stewart daily show",
    		org_tubepress_options_category_Gallery::TOP_RATED_VALUE 	 => "today",
    		org_tubepress_options_category_Gallery::USER_VALUE 		 => "3hough",
    		org_tubepress_options_category_Meta::AUTHOR 				 => false,
    		org_tubepress_options_category_Meta::CATEGORY 				 => false,
    		org_tubepress_options_category_Meta::DESCRIPTION 			 => false,
    		org_tubepress_options_category_Meta::ID 					 => false,
    		org_tubepress_options_category_Meta::LENGTH 				 => true,
    		org_tubepress_options_category_Meta::RATING 				 => false,
    		org_tubepress_options_category_Meta::RATINGS 				 => false,
    		org_tubepress_options_category_Meta::TAGS 					 => false,
    		org_tubepress_options_category_Meta::TITLE 				 => true,
    		org_tubepress_options_category_Meta::UPLOADED 				 => false,
    		org_tubepress_options_category_Meta::URL 					 => false,
    		org_tubepress_options_category_Meta::VIEWS 				 => true,
    		org_tubepress_options_category_Widget::TITLE 				 => "TubePress",
    		org_tubepress_options_category_Widget::TAGSTRING 			 => "[tubepress thumbHeight='105', thumbWidth='135']",
    		org_tubepress_options_category_Gallery::TEMPLATE    => 'foo'
    	);
	return $vals[$args[0]];
}

function _msgCallback()
{
	$args = func_get_args();
	return $args[0];
}
?>
