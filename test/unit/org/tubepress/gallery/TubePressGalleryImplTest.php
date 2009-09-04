<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/gallery/TubePressGalleryImpl.class.php';

class org_tubepress_gallery_TubePressGalleryImplTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
    private $_iocContainer;
    private $_template;
    private $_log;
    private $_messageService;
    private $_optionsManager;
    private $_paginationService;
    private $_player;
    private $_queryStringService;
    private $_thumbnailService;
    private $_videoProvider;
	
	function setUp()
	{
	    $this->_sut = new org_tubepress_gallery_TubePressGalleryImpl();
		$this->_createMocks();
		$this->_setupMocks();
		$this->_applyMocks();
	}
	
   private function _createMocks()
    {
        $this->_iocContainer       = $this->getMock('org_tubepress_ioc_IocService');
        $this->_template           = $this->getMock('org_tubepress_template_Template');
        $this->_log                = $this->getMock('org_tubepress_log_Log');
        $this->_messageService     = $this->getMock('org_tubepress_message_MessageService');
        $this->_optionsManager     = $this->getMock('org_tubepress_options_manager_OptionsManager');
        $this->_paginationService  = $this->getMock('org_tubepress_pagination_PaginationService');
        $this->_player             = $this->getMock('org_tubepress_player_Player');
        $this->_queryStringService = $this->getMock('org_tubepress_querystring_QueryStringService');
        $this->_thumbnailService   = $this->getMock('org_tubepress_thumbnail_ThumbnailService');
        $this->_videoProvider      = $this->getMock('org_tubepress_video_feed_provider_Provider');
    }
	
    private function _applyMocks()
    {
        $this->_sut->setContainer($this->_iocContainer);
        $this->_sut->setTemplate($this->_template);         
        $this->_sut->setLog($this->_log);              
        $this->_sut->setMessageService($this->_messageService);     
        $this->_sut->setOptionsManager($this->_optionsManager);    
        $this->_sut->setPaginationService($this->_paginationService); 
        $this->_sut->setQueryStringService($this->_queryStringService); 
        $this->_sut->setThumbnailService($this->_thumbnailService);   
        $this->_sut->setVideoProvider($this->_videoProvider);      
    }
    
	private function _setupMocks()
	{
	    $fakeVideos = array(
	       org_tubepress_video_VideoTest::getFakeInstance(true),
	       org_tubepress_video_VideoTest::getFakeInstance(true)
	    );
	    
	    $feedResult = new org_tubepress_video_feed_FeedResult();
	    $feedResult->setVideoArray($fakeVideos);
	    $feedResult->setEffectiveDisplayCount(2);
	    $feedResult->setEffectiveTotalResultCount(200);
	    
	    /* set up the fake videos */
	    $this->_videoProvider->expects($this->once())
	                         ->method('getFeedResult')
	                         ->will($this->returnValue($feedResult));
	    
        /* set up the IOC container */
        $this->_iocContainer->expects($this->once())
                            ->method('safeGet')
                            ->will($this->returnValue($this->_player));                      
                              
        /* make sure pre-gallery video is sent to the player */
        $this->_player->expects($this->once())
                      ->method('getPreGalleryHtml')
                      ->will($this->returnValue('pre gallery html'));

        /* no custom video this time */
        $this->_queryStringService->expects($this->once())
                                  ->method('getCustomVideo')
                                  ->will($this->returnValue(''));
                                  
        /* make sure each thumb gets printed */
        $this->_thumbnailService->expects($this->exactly(sizeof($fakeVideos)))
                                ->method('getHtml')
                                ->will($this->returnValue('fake thumbnail html'));

        /* make sure pagination gets printed */
        $this->_paginationService->expects($this->once())
                                 ->method('getHtml')
                                 ->will($this->returnValue('Fakepagination'));                        

        /* make sure the template returns a value */
        $this->_template->expects($this->once())
                                 ->method('getHtml')
                                 ->will($this->returnValue('gallery html'));
	}
	
    public function testRegularGalleryHtml()
    {
        /* set up the options manager */
        $this->_optionsManager->expects($this->any())
                              ->method('get')
                              ->will($this->returnCallback('_tpomCallbackGalleryUnitTest'));
        
        $this->assertEquals('gallery html', $this->_sut->getHtml(22));    
    }

    public function testCustomTemplateGalleryHtml()
    {
        /* set up the options manager */
        $this->_optionsManager->expects($this->any())
                              ->method('get')
                              ->will($this->returnCallback('_tpomCallbackGalleryUnitTestCustomTemplate'));
        
        $this->assertEquals('gallery html', $this->_sut->getHtml(22));    
    }   
                                 
//		$fakeUrl = 'http://fakeurl';
//		$fakeXml = DOMDocument::load(dirname(__FILE__) . '/../../../sample_feed.xml');
//		$fakeVideo = org_tubepress_video_VideoTest::getFakeInstance(false);
//		$fakeHtml = 'stuff';
//		
//		$this->_urlBuilderService->expects($this->once())
//								 ->method('buildGalleryUrl')
//								 ->will($this->returnValue($fakeUrl));
//		
//		$this->_feedRetrievalService->expects($this->once())
//									->method('fetch')
//									->will($this->returnValue($fakeXml));
//		$this->_feedInspectionService->expects($this->once())
//									 ->method('getTotalResultCount')
//									 ->with($fakeXml)
//									 ->will($this->returnValue(4));
//		$this->_feedInspectionService->expects($this->once())
//									 ->method('getQueryResultCount')
//									 ->with($fakeXml)
//									 ->will($this->returnValue(4));
//		$this->_qss->expects($this->once())
//				   ->method('getPageNum')
//				   ->will($this->returnValue(1));
//		$this->_videoFactory->expects($this->once())
//							->method('dom2TubePressVideoArray')
//							->will($this->returnValue(array($fakeVideo, $fakeVideo, $fakeVideo)));
//		$this->_thumbService->expects($this->exactly(3))
//							->method('getHtml')
//							->will($this->returnValue($fakeHtml));
//	    $fakePlayer = $this->getMock('org_tubepress_player_Player');
//	    $fakePlayer->expects($this->once())
//	               ->method('getPreGalleryHtml')
//	               ->will($this->returnValue('pregallerystuff'));
//        $this->_ioc->expects($this->once())
//                   ->method('safeGet')
//                   ->will($this->returnValue($fakePlayer));
	
}

function _tpomCallbackGalleryUnitTest()
{
	$args = func_get_args();
	$vals = array(
		org_tubepress_options_category_Feed::CACHE_ENABLED => true,
		org_tubepress_options_category_Display::RESULTS_PER_PAGE => 3,
		org_tubepress_options_category_Display::ORDER_BY => 'relevance',
		org_tubepress_options_category_Display::CURRENT_PLAYER_NAME => 'normal',
		org_tubepress_options_category_Embedded::EMBEDDED_WIDTH => 500,
		org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT => 600,
        org_tubepress_options_category_Embedded::SHOW_RELATED => false,
        org_tubepress_options_category_Embedded::PLAYER_COLOR => '/',
        org_tubepress_options_category_Embedded::AUTOPLAY => true,
        org_tubepress_options_category_Embedded::LOOP => true,
        org_tubepress_options_category_Embedded::GENIE => false,
        org_tubepress_options_category_Embedded::BORDER => true,
        org_tubepress_options_category_Display::PAGINATE_ABOVE => true,
        org_tubepress_options_category_Display::PAGINATE_BELOW => true,
        org_tubepress_options_category_Feed::RESULT_COUNT_CAP => 300,
        org_tubepress_options_category_Template::TEMPLATE => ''
	);
	return $vals[$args[0]];
}

function _tpomCallbackGalleryUnitTestCustomTemplate()
{
    $args = func_get_args();
    $vals = array(
        org_tubepress_options_category_Feed::CACHE_ENABLED => true,
        org_tubepress_options_category_Display::RESULTS_PER_PAGE => 3,
        org_tubepress_options_category_Display::ORDER_BY => 'relevance',
        org_tubepress_options_category_Display::CURRENT_PLAYER_NAME => 'normal',
        org_tubepress_options_category_Embedded::EMBEDDED_WIDTH => 500,
        org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT => 600,
        org_tubepress_options_category_Embedded::SHOW_RELATED => false,
        org_tubepress_options_category_Embedded::PLAYER_COLOR => '/',
        org_tubepress_options_category_Embedded::AUTOPLAY => true,
        org_tubepress_options_category_Embedded::LOOP => true,
        org_tubepress_options_category_Embedded::GENIE => false,
        org_tubepress_options_category_Embedded::BORDER => true,
        org_tubepress_options_category_Display::PAGINATE_ABOVE => true,
        org_tubepress_options_category_Display::PAGINATE_BELOW => true,
        org_tubepress_options_category_Feed::RESULT_COUNT_CAP => 300,
        org_tubepress_options_category_Template::TEMPLATE => 'foobar'
    );
    return $vals[$args[0]];
}
?>
