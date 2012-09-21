<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class org_tubepress_impl_shortcode_commands_ThumbGalleryCommandTest extends TubePressUnitTest
{
	private $_sut;

    private $_mockExecutionContext;

    private $_mockHttpRequestParameterService;

    private $_mockThemeHandler;

    private $_mockProviderCalculator;

    private $_mockProvider;

    private $_mockEventDispatcher;

    private $_messageService;

	function setup()
	{
        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        $this->_mockHttpRequestParameterService = Mockery::mock(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockThemeHandler     = Mockery::mock(tubepress_spi_theme_ThemeHandler::_);
        $this->_mockProviderCalculator = Mockery::mock(tubepress_spi_provider_ProviderCalculator::_);
        $this->_mockProvider = Mockery::mock(tubepress_spi_provider_Provider::_);
        $this->_mockEventDispatcher  = Mockery::mock('ehough_tickertape_api_IEventDispatcher');
        $this->_messageService = Mockery::mock(tubepress_spi_message_MessageService::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpRequestParameterService($this->_mockHttpRequestParameterService);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setThemeHandler($this->_mockThemeHandler);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setVideoProviderCalculator($this->_mockProviderCalculator);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setVideoProvider($this->_mockProvider);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEventDispatcher($this->_mockEventDispatcher);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setMessageService($this->_messageService);

		$this->_sut = new tubepress_impl_shortcode_commands_ThumbGalleryCommand();
	}



	function testExecuteGenerateGalleryIdExecContextSetFails()
	{
	    $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('');
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_api_const_options_names_Advanced::GALLERY_ID, Mockery::type('integer'))->andReturn('something');

	    $this->assertFalse($this->_sut->execute(new ehough_chaingang_impl_StandardContext()));
	}

	function testExecuteGenerateGalleryId()
	{
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn('page-num');

	    $mockTemplate = \Mockery::mock('ehough_contemplate_api_Template');
	    $mockTemplate->shouldReceive('toString')->once()->andReturn('template-string');

	    $this->_mockThemeHandler->shouldReceive('getTemplateInstance')->once()->with('gallery.tpl.php')->andReturn($mockTemplate);
	    $this->_mockProviderCalculator->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('provider-name');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('');
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_api_const_options_names_Advanced::GALLERY_ID, Mockery::type('integer'))->andReturn(true);

	    $mockFeedResult = new tubepress_api_video_VideoGalleryPage();
	    $mockFeedResult->setVideos(array('x', 'y'));

	    $this->_mockProvider->shouldReceive('getMultipleVideos')->once()->andReturn($mockFeedResult);

        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->with(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION)->andReturn(true);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION, Mockery::on(function ($arg) use ($mockTemplate) {

            return $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === $mockTemplate;
        }));

        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->with(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_HTML_CONSTRUCTION)->andReturn(true);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_HTML_CONSTRUCTION, Mockery::on(function ($arg) use ($mockTemplate) {

            return $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === 'template-string';
        }));

        $mockContext = new ehough_chaingang_impl_StandardContext();

	    $this->assertTrue($this->_sut->execute($mockContext));
	    $this->assertEquals('template-string', $mockContext->get(tubepress_impl_shortcode_ShortcodeHtmlGeneratorChain::CHAIN_KEY_GENERATED_HTML));
	}

	function testExecuteNoVids()
	{
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn('page-num');

	    $mockTemplate = \Mockery::mock('ehough_contemplate_api_Template');

        $this->_mockThemeHandler->shouldReceive('getTemplateInstance')->once()->with('gallery.tpl.php')->andReturn($mockTemplate);
        $this->_mockProviderCalculator->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('provider-name');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('gallery-id');

        $mockFeedResult = new tubepress_api_video_VideoGalleryPage();
        $mockFeedResult->setVideos(array());

        $this->_mockProvider->shouldReceive('getMultipleVideos')->once()->andReturn($mockFeedResult);

	    $this->_messageService->shouldReceive('_')->once()->andReturnUsing(function ($key) {
	          return "<<$key>>";
	    });



        $mockContext = new ehough_chaingang_impl_StandardContext();

	    $this->assertTrue($this->_sut->execute($mockContext));
	    $this->assertEquals('<<no-videos-found>>', $mockContext->get(tubepress_impl_shortcode_ShortcodeHtmlGeneratorChain::CHAIN_KEY_GENERATED_HTML));
	}

	function testExecute()
	{

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn('page-num');

	    $mockTemplate = \Mockery::mock('ehough_contemplate_api_Template');
	    $mockTemplate->shouldReceive('toString')->once()->andReturn('template-string');

        $this->_mockThemeHandler->shouldReceive('getTemplateInstance')->once()->with('gallery.tpl.php')->andReturn($mockTemplate);
        $this->_mockProviderCalculator->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('provider-name');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('gallery-id');

        $mockFeedResult = new tubepress_api_video_VideoGalleryPage();
        $mockFeedResult->setVideos(array('x', 'y'));

        $this->_mockProvider->shouldReceive('getMultipleVideos')->once()->andReturn($mockFeedResult);

        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->with(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION)->andReturn(true);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION, Mockery::on(function ($arg) use ($mockTemplate) {

            return $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === $mockTemplate;
        }));

        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->with(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_HTML_CONSTRUCTION)->andReturn(true);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_HTML_CONSTRUCTION, Mockery::on(function ($arg) use ($mockTemplate) {

            return $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === 'template-string';
        }));

        $mockContext = new ehough_chaingang_impl_StandardContext();

	    $this->assertTrue($this->_sut->execute($mockContext));
	    $this->assertEquals('template-string', $mockContext->get(tubepress_impl_shortcode_ShortcodeHtmlGeneratorChain::CHAIN_KEY_GENERATED_HTML));
	}
}