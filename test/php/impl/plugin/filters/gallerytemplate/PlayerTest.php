<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/plugin/filters/gallerytemplate/Player.class.php';

class org_tubepress_impl_plugin_filters_gallerytemplate_PlayerTest extends TubePressUnitTest
{
    private $_sut;

    function setup()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_plugin_filters_gallerytemplate_Player();
    }

    function testNonPlayerLoadOnPage()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn('player-name');
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('gallery-id');

        $fakeVideo = \Mockery::mock('org_tubepress_api_video_Video');

        $providerResult = \Mockery::mock('org_tubepress_api_provider_ProviderResult');
        $providerResult->shouldReceive('getVideoArray')->once()->andReturn(array($fakeVideo));

        $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::PLAYER_HTML, '');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::PLAYER_NAME, 'player-name');

        $this->assertEquals($mockTemplate, $this->_sut->alter_galleryTemplate($mockTemplate, $providerResult, 1, org_tubepress_api_provider_Provider::YOUTUBE));
    }

    function testAlterTemplateStaticPlayer()
    {
        $this->_testPlayerLoadOnPage(org_tubepress_api_const_options_values_PlayerLocationValue::STATICC);
    }

    function testAlterTemplateNormalPlayer()
    {
        $this->_testPlayerLoadOnPage(org_tubepress_api_const_options_values_PlayerLocationValue::NORMAL);
    }

    private function _testPlayerLoadOnPage($name)
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn($name);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('gallery-id');

        $fakeVideo = \Mockery::mock('org_tubepress_api_video_Video');

        $providerResult = \Mockery::mock('org_tubepress_api_provider_ProviderResult');
        $providerResult->shouldReceive('getVideoArray')->once()->andReturn(array($fakeVideo));

        $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::PLAYER_HTML, 'player-html');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::PLAYER_NAME, $name);

        $htmlGenerator = $ioc->get(org_tubepress_api_player_PlayerHtmlGenerator::_);
        $htmlGenerator->shouldReceive('getHtml')->once()->with($fakeVideo, 'gallery-id')->andReturn('player-html');

        $this->assertEquals($mockTemplate, $this->_sut->alter_galleryTemplate($mockTemplate, $providerResult, 1, org_tubepress_api_provider_Provider::YOUTUBE));
    }
}
