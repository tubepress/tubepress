<?php
class TubePressGalleryTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
	function setUp()
	{
		$this->_sut = new TubePressGallery();
		
		$tpom = $this->getMock("TubePressOptionsManager");
		$tpom->expects($this->any())
			 ->method("get")
			 ->will($this->returnCallback("_tpomCallback"));
		$messageService = $this->getMock("TubePressMessageService");
		$messageService->expects($this->any())
					   ->method("_")
					   ->will($this->returnCallback("_msgCallback"));
		
        $thumbService = new SimpleTubePressThumbnailService();
        $thumbService->setOptionsManager($tpom);
        $thumbService->setMessageService($messageService);
        
        $queryStringService = new SimpleTubePressQueryStringService();
        
        $urlBuilderService = new SimpleTubePressUrlBuilder();
        $urlBuilderService->setOptionsManager($tpom);
        $urlBuilderService->setQueryStringService($queryStringService);
        
        $paginationService = new TubePressPaginationService_DiggStyle();
        $paginationService->setMessageService($messageService);
        $paginationService->setOptionsManager($tpom);
        $paginationService->setQueryStringService($queryStringService);
        
        $this->_sut->setCacheService(             new SimpleTubePressCacheService());
        $this->_sut->setFeedInspectionService( new SimpleTubePressFeedInspectionService());
        $this->_sut->setFeedRetrievalService(     new TubePressFeedRetrievalService_HTTP_Request2());
        $this->_sut->setOptionsManager(         $tpom);
        $this->_sut->setPaginationService(     $paginationService);
        $this->_sut->setThumbnailService(         $thumbService);
        $this->_sut->setUrlBuilderService(     $urlBuilderService);
        $this->_sut->setVideoFactory(             new SimpleTubePressVideoFactory());
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
    		TubePressAdvancedOptions::DATEFORMAT 		 => "M j, Y",
    		TubePressAdvancedOptions::DEBUG_ON 			 => true,
    		TubePressAdvancedOptions::FILTER 			 => false,
    		TubePressAdvancedOptions::CACHE_ENABLED 	 => false,
    		TubePressAdvancedOptions::NOFOLLOW_LINKS 	 => true,
    		TubePressAdvancedOptions::KEYWORD 			 => "tubepress",
    		TubePressAdvancedOptions::RANDOM_THUMBS 	 => true,
    		TubePressAdvancedOptions::CLIENT_KEY 		 => "ytapi-EricHough-TubePress-ki6oq9tc-0",
    		TubePressAdvancedOptions::RANDOM_THUMBS 	 => true,
    		TubePressAdvancedOptions::CLIENT_KEY 		 => "ytapi-EricHough-TubePress-ki6oq9tc-0",
    		TubePressAdvancedOptions::DEV_KEY 			 => "AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg",
    		TubePressDisplayOptions::CURRENT_PLAYER_NAME => "normal",
    		TubePressDisplayOptions::DESC_LIMIT 		 => 80,
    		TubePressDisplayOptions::ORDER_BY 			 => "viewCount",
    		TubePressDisplayOptions::RELATIVE_DATES 	 => false,
    		TubePressDisplayOptions::RESULTS_PER_PAGE 	 => 20,
    		TubePressDisplayOptions::THUMB_HEIGHT 		 => 90,
    		TubePressDisplayOptions::THUMB_WIDTH 		 => 120,
    		TubePressEmbeddedOptions::AUTOPLAY 			 => false,
    		TubePressEmbeddedOptions::BORDER 			 => false,
    		TubePressEmbeddedOptions::EMBEDDED_HEIGHT 	 => 355,
    		TubePressEmbeddedOptions::EMBEDDED_WIDTH 	 => 425,
    		TubePressEmbeddedOptions::GENIE 			 => false,
    		TubePressEmbeddedOptions::LOOP 				 => false,
    		TubePressEmbeddedOptions::PLAYER_COLOR 		 => "/",
    		TubePressEmbeddedOptions::SHOW_RELATED 		 => true,
    		TubePressGalleryOptions::MODE 				 => "featured",
    		TubePressGalleryOptions::FAVORITES_VALUE 	 => "mrdeathgod",
    		TubePressGalleryOptions::MOST_VIEWED_VALUE 	 => "today",
    		TubePressGalleryOptions::PLAYLIST_VALUE 	 => "D2B04665B213AE35",
    		TubePressGalleryOptions::TAG_VALUE 			 => "stewart daily show",
    		TubePressGalleryOptions::TOP_RATED_VALUE 	 => "today",
    		TubePressGalleryOptions::USER_VALUE 		 => "3hough",
    		TubePressMetaOptions::AUTHOR 				 => false,
    		TubePressMetaOptions::CATEGORY 				 => false,
    		TubePressMetaOptions::DESCRIPTION 			 => false,
    		TubePressMetaOptions::ID 					 => false,
    		TubePressMetaOptions::LENGTH 				 => true,
    		TubePressMetaOptions::RATING 				 => false,
    		TubePressMetaOptions::RATINGS 				 => false,
    		TubePressMetaOptions::TAGS 					 => false,
    		TubePressMetaOptions::TITLE 				 => true,
    		TubePressMetaOptions::UPLOADED 				 => false,
    		TubePressMetaOptions::URL 					 => false,
    		TubePressMetaOptions::VIEWS 				 => true,
    		TubePressWidgetOptions::TITLE 				 => "TubePress",
    		TubePressWidgetOptions::TAGSTRING 			 => "[tubepress thumbHeight='105', thumbWidth='135']"
    	);
	return $vals[$args[0]];
}

function _msgCallback()
{
	$args = func_get_args();
	return $args[0];
}
?>
