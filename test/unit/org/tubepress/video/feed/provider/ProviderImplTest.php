<?php
class org_tubepress_video_feed_provider_ProviderImplTest extends PHPUnit_Framework_TestCase {
    
    private $_feedInspectionService;
    private $_feedRetrievalService;
    private $_log;
    private $_optionsManager;
    private $_queryStringService;
    private $_urlBuilder;
    private $_videoFactory;
    
    private $_sut;
    
    function setUp()
    {
        $this->_sut = new org_tubepress_video_feed_provider_ProviderImpl();
        $this->_createMocks();
        $this->_applyMocks();
    }
    
    function testGetFeedResult()
    {
        $fakeVideoArray = array($this->getMock('org_tubepress_video_Video'));
        $this->_queryStringService->expects($this->once())
                                  ->method('getPageNum')
                                  ->will($this->returnValue('1'));
        $this->_urlBuilder->expects($this->once())
                          ->method('buildGalleryUrl')
                          ->with(1)
                          ->will($this->returnValue('http://ehough.com'));
        $this->_optionsManager->expects($this->any())
                              ->method('get')
                              ->will($this->returnCallback('_tpomCallbackProviderImplUnitTest'));
        $this->_feedRetrievalService->expects($this->once())
                                    ->method('fetch')
                                    ->will($this->returnValue('xml'));
        $this->_feedInspectionService->expects($this->once())
                                     ->method('getTotalResultCount')
                                     ->with('xml')
                                     ->will($this->returnValue(2));
        $this->_feedInspectionService->expects($this->once())
                                     ->method('getQueryResultCount')
                                     ->with('xml')
                                     ->will($this->returnValue(1));
        $this->_videoFactory->expects($this->once())
                            ->method('feedToVideoArray')
                            ->will($this->returnValue($fakeVideoArray));
        $result = $this->_sut->getFeedResult();
        $this->assertTrue(is_a($result, 'org_tubepress_video_feed_FeedResult'));
        $this->assertTrue($result->getEffectiveDisplayCount() === 1);
    }
    
    function _createMocks()
    {
        $this->_feedInspectionService = $this->getMock('org_tubepress_video_feed_inspection_FeedInspectionService');
        $this->_feedRetrievalService = $this->getMock('org_tubepress_video_feed_retrieval_FeedRetrievalService');
        $this->_log = $this->getMock('org_tubepress_log_Log');
        $this->_urlBuilder = $this->getMock('org_tubepress_url_UrlBuilder');
        $this->_videoFactory = $this->getMock('org_tubepress_video_factory_VideoFactory');
        $this->_queryStringService = $this->getMock('org_tubepress_querystring_QueryStringService');
        $this->_optionsManager = $this->getMock('org_tubepress_options_manager_OptionsManager');
    }
    
    function _applyMocks()
    {
        $this->_sut->setFeedInspectionService($this->_feedInspectionService);    
        $this->_sut->setFeedRetrievalService($this->_feedRetrievalService);
        $this->_sut->setUrlBuilder($this->_urlBuilder);
        $this->_sut->setVideoFactory($this->_videoFactory);
        $this->_sut->setQueryStringService($this->_queryStringService);
        $this->_sut->setOptionsManager($this->_optionsManager);
    }
    
}

function _tpomCallbackProviderImplUnitTest()
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
?>
