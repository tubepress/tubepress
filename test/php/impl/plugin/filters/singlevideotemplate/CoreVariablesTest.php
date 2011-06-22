<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/plugin/filters/singlevideotemplate/CoreVariables.class.php';

class org_tubepress_impl_plugin_filters_singlevideotemplate_CoreVariablesTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_plugin_filters_singlevideotemplate_CoreVariables();
	}

	function testYouTubeFavorites()
	{
	    $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context      = $ioc->get('org_tubepress_api_exec_ExecutionContext');
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH)->andReturn(889);

        $video = \Mockery::mock('org_tubepress_api_video_Video');
        $video->shouldReceive('getId')->once()->andReturn('video-id');

        $embedded       = $ioc->get('org_tubepress_api_embedded_EmbeddedHtmlGenerator');
        $embedded->shouldReceive('getHtml')->once()->with('video-id')->andReturn('embedded-html');

        $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::EMBEDDED_SOURCE, 'embedded-html');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::EMBEDDED_WIDTH, 889);
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::VIDEO, $video);

        $this->assertEquals($mockTemplate, $this->_sut->alter_singleVideoTemplate($mockTemplate, $video, 'provider-name'));
	}

}

