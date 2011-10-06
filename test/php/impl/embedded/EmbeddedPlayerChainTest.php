<?php

require_once BASE . '/sys/classes/org/tubepress/impl/embedded/EmbeddedPlayerChain.class.php';

class org_tubepress_impl_embedded_EmbeddedPlayerChainTest extends TubePressUnitTest {

    private $_sut;
    private $_context;

    function setUp()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_embedded_EmbeddedPlayerChain();
    }

    function testGetHtml()
    {
        $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');
        $mockTemplate->shouldReceive('toString')->once()->andReturn('templateAsString');

        $mockChainContext                             = \Mockery::mock('stdClass');
        $mockChainContext->template                   = $mockTemplate;
        $mockChainContext->dataUrl                    = 'dataurl';
        $mockChainContext->embeddedImplementationName = 'implname';

        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $pc  = $ioc->get('org_tubepress_api_provider_ProviderCalculator');
        $pc->shouldReceive('calculateProviderOfVideoId')->with('videoid')->once()->andReturn('video_provider');

        $chain = $ioc->get('org_tubepress_spi_patterns_cor_Chain');
        $chain->shouldReceive('execute')->once()->with(anInstanceOf('stdClass'), array(
             'org_tubepress_impl_embedded_commands_JwFlvCommand',
             'org_tubepress_impl_embedded_commands_EmbedPlusCommand',
             'org_tubepress_impl_embedded_commands_YouTubeIframeCommand',
             'org_tubepress_impl_embedded_commands_VimeoCommand',
        ))->andReturn(true);
        $chain->shouldReceive('createContextInstance')->once()->andReturn($mockChainContext);

        $pm = $ioc->get(org_tubepress_api_plugin_PluginManager::_);
        $pm->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_EMBEDDED,
            $mockTemplate, 'videoid', 'video_provider', 'dataurl', 'implname')->andReturn($mockTemplate);
        $pm->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::HTML_EMBEDDED,
            'templateAsString', 'videoid', 'video_provider', 'implname')->andReturn('final_result');

        $result = $this->_sut->getHtml('videoid');
        $this->assertEquals('final_result', $result);
    }

}
