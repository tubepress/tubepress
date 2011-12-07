<?php

require_once BASE . '/sys/classes/org/tubepress/impl/shortcode/commands/ThumbGalleryCommand.class.php';

class org_tubepress_impl_shortcode_commands_ThumbGalleryCommandTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_shortcode_commands_ThumbGalleryCommand();
	}

	function testExecuteGenerateGalleryId()
	{
	    $mockChainContext = new stdClass();

	    $ioc       = org_tubepress_impl_ioc_IocContainer::getInstance();

	    $qss = $ioc->get(org_tubepress_api_querystring_QueryStringService::_);
	    $qss->shouldReceive('getPageNum')->once()->andReturn('page-num');

	    $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');
	    $mockTemplate->shouldReceive('toString')->once()->andReturn('template-string');

	    $themeHandler  = $ioc->get(org_tubepress_api_theme_ThemeHandler::_);
	    $themeHandler->shouldReceive('getTemplateInstance')->once()->with('gallery.tpl.php')->andReturn($mockTemplate);

	    $pc            = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);
	    $pc->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('provider-name');

	    $execContext   = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
	    $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('');
	    $execContext->shouldReceive('set')->once()->with(org_tubepress_api_const_options_names_Advanced::GALLERY_ID, integerValue());

	    $mockFeedResult = \Mockery::mock('org_tubepress_api_provider_ProviderResult');
	    $mockFeedResult->shouldReceive('getVideoArray')->once()->andReturn(array('x', 'y'));

	    $provider      = $ioc->get(org_tubepress_api_provider_Provider::_);
	    $provider->shouldReceive('getMultipleVideos')->once()->andReturn($mockFeedResult);

	    $pluginManager = $ioc->get(org_tubepress_api_plugin_PluginManager::_);
	    $pluginManager->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_GALLERY, $mockTemplate, $mockFeedResult, 'page-num', 'provider-name')->andReturn($mockTemplate);
	    $pluginManager->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::HTML_GALLERY, 'template-string', $mockFeedResult, 'page-num', 'provider-name')->andReturn('filtered-html');

	    $this->assertTrue($this->_sut->execute($mockChainContext));
	    $this->assertEquals('filtered-html', $mockChainContext->returnValue);
	}

	function testExecuteNoVids()
	{
	    $mockChainContext = new stdClass();

	    $ioc       = org_tubepress_impl_ioc_IocContainer::getInstance();

	    $qss = $ioc->get(org_tubepress_api_querystring_QueryStringService::_);
	    $qss->shouldReceive('getPageNum')->once()->andReturn('page-num');

	    $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');

	    $themeHandler  = $ioc->get(org_tubepress_api_theme_ThemeHandler::_);
	    $themeHandler->shouldReceive('getTemplateInstance')->once()->with('gallery.tpl.php')->andReturn($mockTemplate);

	    $pc            = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);
	    $pc->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('provider-name');

	    $execContext   = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
	    $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('gallery-id');

	    $mockFeedResult = \Mockery::mock('org_tubepress_api_provider_ProviderResult');
	    $mockFeedResult->shouldReceive('getVideoArray')->once()->andReturn(array());

	    $provider      = $ioc->get(org_tubepress_api_provider_Provider::_);
	    $provider->shouldReceive('getMultipleVideos')->once()->andReturn($mockFeedResult);

	    $ms = $ioc->get(org_tubepress_api_message_MessageService::_);
	    $ms->shouldReceive('_')->once()->andReturnUsing(function ($key) {
	          return "<<$key>>";
	    });

	    $this->assertTrue($this->_sut->execute($mockChainContext));
	    $this->assertEquals('<<no-videos-found>>', $mockChainContext->returnValue);
	}

	function testExecute()
	{
	    $mockChainContext = new stdClass();

	    $ioc       = org_tubepress_impl_ioc_IocContainer::getInstance();

	    $qss = $ioc->get(org_tubepress_api_querystring_QueryStringService::_);
	    $qss->shouldReceive('getPageNum')->once()->andReturn('page-num');

	    $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');
	    $mockTemplate->shouldReceive('toString')->once()->andReturn('template-string');

	    $themeHandler  = $ioc->get(org_tubepress_api_theme_ThemeHandler::_);
	    $themeHandler->shouldReceive('getTemplateInstance')->once()->with('gallery.tpl.php')->andReturn($mockTemplate);

	    $pc            = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);
	    $pc->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('provider-name');

	    $execContext   = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
	    $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('gallery-id');

	    $mockFeedResult = \Mockery::mock('org_tubepress_api_provider_ProviderResult');
	    $mockFeedResult->shouldReceive('getVideoArray')->once()->andReturn(array('x', 'y'));

	    $provider      = $ioc->get(org_tubepress_api_provider_Provider::_);
	    $provider->shouldReceive('getMultipleVideos')->once()->andReturn($mockFeedResult);

	    $pluginManager = $ioc->get(org_tubepress_api_plugin_PluginManager::_);
	    $pluginManager->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_GALLERY, $mockTemplate, $mockFeedResult, 'page-num', 'provider-name')->andReturn($mockTemplate);
	    $pluginManager->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::HTML_GALLERY, 'template-string', $mockFeedResult, 'page-num', 'provider-name')->andReturn('filtered-html');

	    $this->assertTrue($this->_sut->execute($mockChainContext));
	    $this->assertEquals('filtered-html', $mockChainContext->returnValue);
	}
}