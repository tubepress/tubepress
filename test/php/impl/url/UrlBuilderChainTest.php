<?php

require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/impl/url/UrlBuilderChain.class.php';

class org_tubepress_impl_url_UrlBuilderChainTest extends TubePressUnitTest {

    private $_sut;

    function setUp()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_url_UrlBuilderChain();
    }

    /**
     * @expectedException Exception
     */
    function testBuildGalleryUrlNoCommandsCanHandle()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $pc  = $ioc->get('org_tubepress_api_provider_ProviderCalculator');
        $pc->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('providerName');

        $mockChainContext = new stdClass();
        $mockChainContext->returnValue = 'stuff';

        $sm  = $ioc->get('org_tubepress_api_patterns_cor_Chain');
        $sm->shouldReceive('createContextInstance')->once()->andReturn($mockChainContext);
        $sm->shouldReceive('execute')->once()->with($mockChainContext, array(
                'org_tubepress_impl_url_commands_YouTubeUrlBuilderCommand',
                'org_tubepress_impl_url_commands_VimeoUrlBuilderCommand'
        ))->andReturn(false);

        $this->assertEquals('stuff', $this->_sut->buildGalleryUrl(1));
        $this->assertEquals(1, $mockChainContext->arg);
        $this->assertEquals('providerName', $mockChainContext->providerName);
        $this->assertEquals(false, $mockChainContext->single);

    }

    function testBuildSingleVideoUrl()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $pc  = $ioc->get('org_tubepress_api_provider_ProviderCalculator');
        $pc->shouldReceive('calculateProviderOfVideoId')->once()->with('video-id')->andReturn('providerName');

        $mockChainContext = new stdClass();
        $mockChainContext->returnValue = 'stuff';

        $sm  = $ioc->get('org_tubepress_api_patterns_cor_Chain');
        $sm->shouldReceive('createContextInstance')->once()->andReturn($mockChainContext);
        $sm->shouldReceive('execute')->once()->with($mockChainContext, array(
                'org_tubepress_impl_url_commands_YouTubeUrlBuilderCommand',
                'org_tubepress_impl_url_commands_VimeoUrlBuilderCommand'
        ))->andReturn(true);

        $this->assertEquals('stuff', $this->_sut->buildSingleVideoUrl('video-id'));
        $this->assertEquals('video-id', $mockChainContext->arg);
        $this->assertEquals('providerName', $mockChainContext->providerName);
        $this->assertEquals(true, $mockChainContext->single);

    }

    function testBuildGalleryUrl()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $pc  = $ioc->get('org_tubepress_api_provider_ProviderCalculator');
        $pc->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('providerName');

        $mockChainContext = new stdClass();
        $mockChainContext->returnValue = 'stuff';

        $sm  = $ioc->get('org_tubepress_api_patterns_cor_Chain');
        $sm->shouldReceive('createContextInstance')->once()->andReturn($mockChainContext);
        $sm->shouldReceive('execute')->once()->with($mockChainContext, array(
            'org_tubepress_impl_url_commands_YouTubeUrlBuilderCommand',
            'org_tubepress_impl_url_commands_VimeoUrlBuilderCommand'
        ))->andReturn(true);

        $this->assertEquals('stuff', $this->_sut->buildGalleryUrl(1));
        $this->assertEquals(1, $mockChainContext->arg);
        $this->assertEquals('providerName', $mockChainContext->providerName);
        $this->assertEquals(false, $mockChainContext->single);

    }
}