<?php

require_once dirname(__FILE__) . '/../../../../../sys/classes/org/tubepress/impl/shortcode/commands/ThumbGalleryCommand.class.php';

class org_tubepress_impl_shortcode_commands_ThumbGalleryCommandTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_shortcode_commands_ThumbGalleryCommand();
	}


	function testExecute()
	{
	    $mockChainContext = new stdClass();

	    $ioc       = org_tubepress_impl_ioc_IocContainer::getInstance();

	    $qss       = $ioc->get('org_tubepress_api_querystring_QueryStringService');
	    $qss->shouldReceive('getGalleryId')->once()->andReturn('gallery-id');
	    $qss->shouldReceive('getPageNum')->once()->andReturn('page-num');

	    $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');
	    $mockTemplate->shouldReceive('toString')->once()->andReturn('template-string');

	    $themeHandler  = $ioc->get('org_tubepress_api_theme_ThemeHandler');
	    $themeHandler->shouldReceive('getTemplateInstance')->once()->with('gallery.tpl.php')->andReturn($mockTemplate);

	    $pc            = $ioc->get('org_tubepress_api_provider_ProviderCalculator');
	    $pc->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('provider-name');

	    $execContext   = $ioc->get('org_tubepress_api_exec_ExecutionContext');
	    $execContext->shouldReceive('set')->once()->with(org_tubepress_api_const_ExecutionContextVariables::GALLERY_ID, 'gallery-id');

	    $mockFeedResult = \Mockery::mock('org_tubepress_api_provider_ProviderResult');
	    $mockFeedResult->shouldReceive('getVideoArray')->once()->andReturn(array('x', 'y'));

	    $provider      = $ioc->get('org_tubepress_api_provider_Provider');
	    $provider->shouldReceive('getMultipleVideos')->once()->andReturn($mockFeedResult);

	    $pluginManager = $ioc->get('org_tubepress_api_plugin_PluginManager');
	    $pluginManager->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_GALLERY, $mockTemplate, $mockFeedResult, 'page-num', 'provider-name')->andReturn($mockTemplate);
	    $pluginManager->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::HTML_GALLERY, 'template-string', $mockFeedResult, 'page-num', 'provider-name')->andReturn('filtered-html');

	    $this->assertTrue($this->_sut->execute($mockChainContext));
	    $this->assertEquals('filtered-html', $mockChainContext->returnValue);
	}
}