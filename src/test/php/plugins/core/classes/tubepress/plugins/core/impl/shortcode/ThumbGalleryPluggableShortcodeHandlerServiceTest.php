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
class tubepress_plugins_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerServiceTest extends TubePressUnitTest
{
    /**
     * @var tubepress_plugins_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService
     */
    private $_sut;

    private $_mockExecutionContext;

    private $_mockHttpRequestParameterService;

    private $_mockThemeHandler;

    private $_mockProvider;

    private $_mockEventDispatcher;

    private $_messageService;

	function onSetup()
	{
        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockThemeHandler     = $this->createMockSingletonService(tubepress_spi_theme_ThemeHandler::_);
        $this->_mockProvider = $this->createMockSingletonService(tubepress_spi_collector_VideoCollector::_);
        $this->_mockEventDispatcher  = $this->createMockSingletonService('ehough_tickertape_api_IEventDispatcher');
        $this->_messageService = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);

		$this->_sut = new tubepress_plugins_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService();
	}


	function testExecuteGenerateGalleryId()
	{
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn('page-num');

	    $mockTemplate = \Mockery::mock('ehough_contemplate_api_Template');
	    $mockTemplate->shouldReceive('toString')->once()->andReturn('template-string');

	    $this->_mockThemeHandler->shouldReceive('getTemplateInstance')->once()->with('gallery.tpl.php', TUBEPRESS_ROOT . '/src/main/resources/default-themes/default')->andReturn($mockTemplate);

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('');
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_api_const_options_names_Advanced::GALLERY_ID, Mockery::type('integer'))->andReturn(true);

	    $mockFeedResult = new tubepress_api_video_VideoGalleryPage();
	    $mockFeedResult->setVideos(array('x', 'y'));

	    $this->_mockProvider->shouldReceive('collectVideoGalleryPage')->once()->andReturn($mockFeedResult);

        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->with(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION)->andReturn(true);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION, Mockery::on(function ($arg) use ($mockTemplate) {

            return $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === $mockTemplate;
        }));

        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->with(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_HTML_CONSTRUCTION)->andReturn(true);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_HTML_CONSTRUCTION, Mockery::on(function ($arg) use ($mockTemplate) {

            return $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === 'template-string';
        }));

	    $this->assertEquals('template-string', $this->_sut->getHtml());
	}

	function testExecuteNoVids()
	{
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn('page-num');

	    $mockTemplate = \Mockery::mock('ehough_contemplate_api_Template');

        $this->_mockThemeHandler->shouldReceive('getTemplateInstance')->once()->with('gallery.tpl.php', TUBEPRESS_ROOT . '/src/main/resources/default-themes/default')->andReturn($mockTemplate);

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('gallery-id');

        $mockFeedResult = new tubepress_api_video_VideoGalleryPage();
        $mockFeedResult->setVideos(array());

        $this->_mockProvider->shouldReceive('collectVideoGalleryPage')->once()->andReturn($mockFeedResult);

	    $this->_messageService->shouldReceive('_')->once()->andReturnUsing(function ($key) {
	          return "<<$key>>";
	    });

	    $this->assertEquals('<<No matching videos>>', $this->_sut->getHtml());
	}

	function testExecute()
	{

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn('page-num');

	    $mockTemplate = \Mockery::mock('ehough_contemplate_api_Template');
	    $mockTemplate->shouldReceive('toString')->once()->andReturn('template-string');

        $this->_mockThemeHandler->shouldReceive('getTemplateInstance')->once()->with('gallery.tpl.php', TUBEPRESS_ROOT . '/src/main/resources/default-themes/default')->andReturn($mockTemplate);

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('gallery-id');

        $mockFeedResult = new tubepress_api_video_VideoGalleryPage();
        $mockFeedResult->setVideos(array('x', 'y'));

        $this->_mockProvider->shouldReceive('collectVideoGalleryPage')->once()->andReturn($mockFeedResult);

        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->with(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION)->andReturn(true);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION, Mockery::on(function ($arg) use ($mockTemplate) {

            return $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === $mockTemplate;
        }));

        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->with(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_HTML_CONSTRUCTION)->andReturn(true);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_HTML_CONSTRUCTION, Mockery::on(function ($arg) use ($mockTemplate) {

            return $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === 'template-string';
        }));

	    $this->assertEquals('template-string', $this->_sut->getHtml());
	}
}