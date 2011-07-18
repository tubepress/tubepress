<?php

require_once BASE . '/sys/classes/org/tubepress/impl/player/DefaultPlayerHtmlGenerator.class.php';

class org_tubepress_impl_player_DefaultPlayerHtmlGeneratorTest extends TubePressUnitTest {

    private $_sut;

    function setUp()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_player_DefaultPlayerHtmlGenerator();
        org_tubepress_impl_log_Log::setEnabled(false, array());
        $this->_video = \Mockery::mock('org_tubepress_api_video_Video');
        $this->_video->shouldReceive('getId')->once()->andReturn('video-id');
    }

    function testGetHtml()
    {
        $ioc           = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context       = $ioc->get('org_tubepress_api_exec_ExecutionContext');
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME)->andReturn('current-player-name');

        $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');
        $mockTemplate->shouldReceive('toString')->once()->andReturn('foobarr');

        $themeHandler  = $ioc->get('org_tubepress_api_theme_ThemeHandler');
        $themeHandler->shouldReceive('getTemplateInstance')->once()->with('players/current-player-name.tpl.php')->andReturn($mockTemplate);

        $pc            = $ioc->get('org_tubepress_api_provider_ProviderCalculator');
        $pc->shouldReceive('calculateProviderOfVideoId')->once()->with('video-id')->andReturn('video-provider');

        $pm            = $ioc->get('org_tubepress_api_plugin_PluginManager');
        $pm->shouldReceive('runFilters')->once()->with(
        org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_PLAYER,
        $mockTemplate, $this->_video, 'video-provider', 'current-player-name'
        )->andReturn($mockTemplate);
        $pm->shouldReceive('runFilters')->once()->with(
        org_tubepress_api_const_plugin_FilterPoint::HTML_PLAYER, 'foobarr',
        $this->_video, 'video-provider', 'current-player-name'
        )->andReturn('modified-player-html');
        $pm->shouldReceive('runFilters')->once()->with(
        org_tubepress_api_const_plugin_FilterPoint::HTML_ANY, 'modified-player-html'
        )->andReturn('final-html');

        $this->assertEquals('final-html', $this->_sut->getHtml($this->_video));
    }
}