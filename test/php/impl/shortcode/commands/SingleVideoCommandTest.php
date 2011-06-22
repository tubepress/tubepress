<?php

require_once dirname(__FILE__) . '/../../../../../sys/classes/org/tubepress/impl/shortcode/commands/SingleVideoCommand.class.php';

class org_tubepress_impl_shortcode_commands_SingleVideoCommandTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_shortcode_commands_SingleVideoCommand();
	}

	function testExecute()
	{
	    $mockChainContext = new stdClass();

	    $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();

	    $execContext = $ioc->get('org_tubepress_api_exec_ExecutionContext');
	    $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Output::VIDEO)->andReturn('video-id');

	    $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');
	    $mockTemplate->shouldReceive('toString')->once()->andReturn('template-string');

	    $th       = $ioc->get('org_tubepress_api_theme_ThemeHandler');
	    $th->shouldReceive('getTemplateInstance')->once()->with('single_video.tpl.php')->andReturn($mockTemplate);

	    $pc            = $ioc->get('org_tubepress_api_provider_ProviderCalculator');
	    $pc->shouldReceive('calculateProviderOfVideoId')->once()->with('video-id')->andReturn('video-provider');

	    $video = \Mockery::mock('org_tubepress_api_video_Video');

	    $provider      = $ioc->get('org_tubepress_api_provider_Provider');
	    $provider->shouldReceive('getSingleVideo')->once()->with('video-id')->andReturn($video);

	    $pluginManager = $ioc->get('org_tubepress_api_plugin_PluginManager');
	    $pluginManager->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_SINGLEVIDEO, $mockTemplate, $video, 'video-provider')->andReturn($mockTemplate);
	    $pluginManager->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::HTML_SINGLEVIDEO, 'template-string', $video, 'video-provider')->andReturn('final-value');

	    $this->assertTrue($this->_sut->execute($mockChainContext));
        $this->assertEquals('final-value', $mockChainContext->returnValue);
	}
}