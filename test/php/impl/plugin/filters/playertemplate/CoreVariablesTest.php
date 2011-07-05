<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/plugin/filters/playertemplate/CoreVariables.class.php';

class org_tubepress_impl_plugin_filters_playertemplate_CoreVariablesTest extends TubePressUnitTest
{
    private $_sut;

    public function setup()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_plugin_filters_playertemplate_CoreVariables();
    }

    function testAlterTemplate()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $video = \Mockery::mock('org_tubepress_api_video_Video');
        $video->shouldReceive('getId')->once()->andReturn('video-id');

        $context   = $ioc->get('org_tubepress_api_exec_ExecutionContext');
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_ExecutionContextVariables::GALLERY_ID)->andReturn('gallery-id');
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH)->andReturn(668);

        $embedded  = $ioc->get('org_tubepress_api_embedded_EmbeddedHtmlGenerator');
        $embedded->shouldReceive('getHtml')->once()->with('video-id')->andReturn('embedded-html');

        $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::EMBEDDED_SOURCE, 'embedded-html');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::GALLERY_ID, 'gallery-id');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::VIDEO, $video);
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::EMBEDDED_WIDTH, 668);

        $this->assertEquals($mockTemplate, $this->_sut->alter_playerTemplate($mockTemplate, $video, 'provider-name', 'player-name'));
    }
}