<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/gallery/TubePressGalleryImpl.class.php';

class org_tubepress_gallery_TubePressGalleryImplTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
    private $_iocContainer;
    private $_template;
    private $_log;
    private $_messageService;
    private $_optionsManager;
    private $_optionsReference;
    private $_paginationService;
    private $_player;
    private $_queryStringService;
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
        $this->_videoProvider      = $this->getMock('org_tubepress_video_feed_provider_Provider');
        $this->_optionsReference   = $this->getMock('org_tubepress_options_reference_OptionsReference');
    }
	
    private function _applyMocks()
    {
        $this->_sut->setContainer($this->_iocContainer);
        $this->_sut->setTemplate($this->_template);         
        $this->_sut->setMessageService($this->_messageService);     
        $this->_sut->setOptionsManager($this->_optionsManager);    
        $this->_sut->setPaginationService($this->_paginationService); 
        $this->_sut->setQueryStringService($this->_queryStringService); 
        $this->_sut->setVideoProvider($this->_videoProvider);      
        $this->_sut->setOptionsReference($this->_optionsReference);
    }
    
	private function _setupMocks()
	{
	    $fakeVideos = array(
	       $this->getMock('org_tubepress_video_Video'),
	       $this->getMock('org_tubepress_video_Video')
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

        /* make sure pagination gets printed */
        $this->_paginationService->expects($this->once())
                                 ->method('getHtml')
                                 ->will($this->returnValue('Fakepagination'));                        

        /* make sure the template returns a value */
        $this->_template->expects($this->once())
                                 ->method('toString')
                                 ->will($this->returnValue('gallery html'));
                                 
        $this->_optionsReference->expects($this->once())
                                ->method('getOptionNamesForCategory')
                                ->will($this->returnValue(array()));
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
        org_tubepress_options_category_Display::AJAX_PAGINATION => false,
        org_tubepress_options_category_Feed::RESULT_COUNT_CAP => 300,
        org_tubepress_options_category_Template::TEMPLATE => '',
        org_tubepress_options_category_Embedded::PLAYER_IMPL => 'youtube',
        org_tubepress_options_category_Display::THUMB_HEIGHT => 40,
        org_tubepress_options_category_Display::THUMB_WIDTH => 50
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
        org_tubepress_options_category_Display::AJAX_PAGINATION => false,
        org_tubepress_options_category_Feed::RESULT_COUNT_CAP => 300,
        org_tubepress_options_category_Template::TEMPLATE => 'foobar',
        org_tubepress_options_category_Embedded::PLAYER_IMPL => 'youtube',
        org_tubepress_options_category_Display::THUMB_HEIGHT => 40,
        org_tubepress_options_category_Display::THUMB_WIDTH => 50
    );
    return $vals[$args[0]];
}
?>
