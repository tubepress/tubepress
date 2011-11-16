<?php

require_once BASE . '/sys/classes/org/tubepress/impl/feed/UrlBuilderChain.class.php';

class org_tubepress_impl_feed_UrlBuilderChainTest extends TubePressUnitTest {

    private $_sut;

    function setUp()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_feed_UrlBuilderChain();
    }

    /**
     * @expectedException Exception
     */
    function testBuildGalleryUrlNoCommandsCanHandle()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $pc  = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);
        $pc->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('providerName');

        $mockChainContext = new stdClass();
        $mockChainContext->returnValue = 'stuff';

        $sm  = $ioc->get(org_tubepress_spi_patterns_cor_Chain::_);
        $sm->shouldReceive('createContextInstance')->once()->andReturn($mockChainContext);
        $sm->shouldReceive('execute')->once()->with($mockChainContext, array(
                'org_tubepress_impl_feed_urlbuilding_YouTubeUrlBuilderCommand',
                'org_tubepress_impl_feed_urlbuilding_VimeoUrlBuilderCommand'
        ))->andReturn(false);

        $this->assertEquals('stuff', $this->_sut->buildGalleryUrl(1));
        $this->assertEquals(1, $mockChainContext->arg);
        $this->assertEquals('providerName', $mockChainContext->providerName);
        $this->assertEquals(false, $mockChainContext->single);

    }

    function testBuildSingleVideoUrl()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $pc  = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);
        $pc->shouldReceive('calculateProviderOfVideoId')->once()->with('video-id')->andReturn('providerName');

        $mockChainContext = new stdClass();
        $mockChainContext->returnValue = 'stuff';

        $sm  = $ioc->get(org_tubepress_spi_patterns_cor_Chain::_);
        $sm->shouldReceive('createContextInstance')->once()->andReturn($mockChainContext);
        $sm->shouldReceive('execute')->once()->with($mockChainContext, array(
                'org_tubepress_impl_feed_urlbuilding_YouTubeUrlBuilderCommand',
                'org_tubepress_impl_feed_urlbuilding_VimeoUrlBuilderCommand'
        ))->andReturn(true);

        $this->assertEquals('stuff', $this->_sut->buildSingleVideoUrl('video-id'));
        $this->assertEquals('video-id', $mockChainContext->arg);
        $this->assertEquals('providerName', $mockChainContext->providerName);
        $this->assertEquals(true, $mockChainContext->single);

    }

    function testBuildGalleryUrl()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $pc  = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);
        $pc->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('providerName');

        $mockChainContext = new stdClass();
        $mockChainContext->returnValue = 'stuff';

        $sm  = $ioc->get(org_tubepress_spi_patterns_cor_Chain::_);
        $sm->shouldReceive('createContextInstance')->once()->andReturn($mockChainContext);
        $sm->shouldReceive('execute')->once()->with($mockChainContext, array(
            'org_tubepress_impl_feed_urlbuilding_YouTubeUrlBuilderCommand',
            'org_tubepress_impl_feed_urlbuilding_VimeoUrlBuilderCommand'
        ))->andReturn(true);

        $this->assertEquals('stuff', $this->_sut->buildGalleryUrl(1));
        $this->assertEquals(1, $mockChainContext->arg);
        $this->assertEquals('providerName', $mockChainContext->providerName);
        $this->assertEquals(false, $mockChainContext->single);

    }
}