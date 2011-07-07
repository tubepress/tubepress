<?php

require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/impl/provider/SimpleProvider.class.php';
require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/api/video/Video.class.php';

class org_tubepress_impl_provider_SimpleProviderTest extends TubePressUnitTest
{
    private $_sut;
    private $_fakeVideo;

    function setup()
    {
        parent::setUp();

        $this->_sut       = new org_tubepress_impl_provider_SimpleProvider();
        $this->_fakeVideo = \Mockery::mock('org_tubepress_api_video_Video');

        org_tubepress_impl_log_Log::setEnabled(false, array());
    }

    /**
     * @expectedException Exception
     */
    public function testGetMultipleVideosFactoryBuildsNone()
    {
        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $qss     = $ioc->get('org_tubepress_api_querystring_QueryStringService');
        $qss->shouldReceive('getPageNum')->once()->andReturn(1);

        $pc      = $ioc->get('org_tubepress_api_provider_ProviderCalculator');
        $pc->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('current-video-provider');

        $urlBuilder = $ioc->get('org_tubepress_api_url_UrlBuilder');
        $urlBuilder->shouldReceive('buildGalleryUrl')->once()->with(1)->andReturn('gallery-url');

        $feedRetrievalService = $ioc->get('org_tubepress_api_feed_FeedFetcher');
        $feedRetrievalService->shouldReceive('fetch')->once()->with('gallery-url', false)->andReturn('fetch-result');

        $context = $ioc->get('org_tubepress_api_exec_ExecutionContext');
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Feed::CACHE_ENABLED)->andReturn(false);

        $feedInspectionService = $ioc->get('org_tubepress_api_feed_FeedInspector');
        $feedInspectionService->shouldReceive('getTotalResultCount')->once()->with('fetch-result')->andReturn(596);

        $factory = $ioc->get('org_tubepress_api_factory_VideoFactory');
        $factory->shouldReceive('feedToVideoArray')->once()->with('fetch-result')->andReturn(array());

        $this->assertEquals('final-result', $this->_sut->getMultipleVideos());
    }

    /**
     * @expectedException Exception
     */
    public function testGetMultipleVideosNoVids()
    {
        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $qss     = $ioc->get('org_tubepress_api_querystring_QueryStringService');
        $qss->shouldReceive('getPageNum')->once()->andReturn(1);

        $pc      = $ioc->get('org_tubepress_api_provider_ProviderCalculator');
        $pc->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('current-video-provider');

        $urlBuilder = $ioc->get('org_tubepress_api_url_UrlBuilder');
        $urlBuilder->shouldReceive('buildGalleryUrl')->once()->with(1)->andReturn('gallery-url');

        $feedRetrievalService = $ioc->get('org_tubepress_api_feed_FeedFetcher');
        $feedRetrievalService->shouldReceive('fetch')->once()->with('gallery-url', false)->andReturn('fetch-result');

        $context = $ioc->get('org_tubepress_api_exec_ExecutionContext');
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Feed::CACHE_ENABLED)->andReturn(false);

        $feedInspectionService = $ioc->get('org_tubepress_api_feed_FeedInspector');
        $feedInspectionService->shouldReceive('getTotalResultCount')->once()->with('fetch-result')->andReturn(0);

        $this->assertEquals('final-result', $this->_sut->getMultipleVideos());
    }

    public function testGetMultipleVideos()
    {
        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $qss     = $ioc->get('org_tubepress_api_querystring_QueryStringService');
        $qss->shouldReceive('getPageNum')->once()->andReturn(1);

        $pc      = $ioc->get('org_tubepress_api_provider_ProviderCalculator');
        $pc->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('current-video-provider');

        $urlBuilder = $ioc->get('org_tubepress_api_url_UrlBuilder');
        $urlBuilder->shouldReceive('buildGalleryUrl')->once()->with(1)->andReturn('gallery-url');

        $feedRetrievalService = $ioc->get('org_tubepress_api_feed_FeedFetcher');
        $feedRetrievalService->shouldReceive('fetch')->once()->with('gallery-url', false)->andReturn('fetch-result');

        $context = $ioc->get('org_tubepress_api_exec_ExecutionContext');
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Feed::CACHE_ENABLED)->andReturn(false);

        $feedInspectionService = $ioc->get('org_tubepress_api_feed_FeedInspector');
        $feedInspectionService->shouldReceive('getTotalResultCount')->once()->with('fetch-result')->andReturn(596);

        $fakeVideoArray = array(5, 4, 3, 1);

        $factory = $ioc->get('org_tubepress_api_factory_VideoFactory');
        $factory->shouldReceive('feedToVideoArray')->once()->with('fetch-result')->andReturn($fakeVideoArray);

        $pm      = $ioc->get('org_tubepress_api_plugin_PluginManager');
        $pm->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::PROVIDER_RESULT,
            anInstanceOf('org_tubepress_api_provider_ProviderResult'), 'current-video-provider')->andReturn('final-result');

        $this->assertEquals('final-result', $this->_sut->getMultipleVideos());
    }

    /**
     * @expectedException Exception
     */
    public function testGetSingleVideoNotFound()
    {
        $this->_setupSingleVideoMocks(array());
        $this->_sut->getSingleVideo('video-id');
    }

    public function testGetSingleVideo()
    {
        $val = array($this->_fakeVideo);
        $this->_setupSingleVideoMocks($val);

        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $pc                   = $ioc->get('org_tubepress_api_provider_ProviderCalculator');
        $pc->shouldReceive('calculateProviderOfVideoId')->with('video-id')->andReturn('video-provider');

        $pm                   = $ioc->get('org_tubepress_api_plugin_PluginManager');
        $pm->shouldReceive('runFilters')->with(org_tubepress_api_const_plugin_FilterPoint::PROVIDER_RESULT, anInstanceOf('org_tubepress_api_provider_ProviderResult'), 'video-provider')->once();

        $this->assertEquals($this->_fakeVideo, $this->_sut->getSingleVideo('video-id'));
    }

    private function _setupSingleVideoMocks($factoryResult)
    {
        $ioc        = org_tubepress_impl_ioc_IocContainer::getInstance();

        $urlBuilder = $ioc->get('org_tubepress_api_url_UrlBuilder');
        $urlBuilder->shouldReceive('buildSingleVideoUrl')->once()->with('video-id')->andReturn('video-url');

        $context              = $ioc->get('org_tubepress_api_exec_ExecutionContext');
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Feed::CACHE_ENABLED)->andReturn(false);

        $feedRetrievalService = $ioc->get('org_tubepress_api_feed_FeedFetcher');
        $feedRetrievalService->shouldReceive('fetch')->once()->with('video-url', false)->andReturn('fake-feed');

        $factory              = $ioc->get('org_tubepress_api_factory_VideoFactory');
        $factory->shouldReceive('feedToVideoArray')->once()->with('fake-feed')->andReturn($factoryResult);
    }
}