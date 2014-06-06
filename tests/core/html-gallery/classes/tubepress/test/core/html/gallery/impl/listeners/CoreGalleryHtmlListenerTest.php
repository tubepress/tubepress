<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_core_html_gallery_impl_listeners_CoreGalleryHtmlListener<extended>
 */
class tubepress_test_core_html_gallery_impl_listeners_GalleryInitJsBaseParamsTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_html_gallery_impl_listeners_CoreGalleryHtmlListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionProvider;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplateFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockCollector;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    public function onSetup()
    {
        $this->_mockExecutionContext            = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_mockOptionProvider              = $this->mock(tubepress_core_options_api_ReferenceInterface::_);
        $this->_mockEnvironmentDetector         = $this->mock(tubepress_core_environment_api_EnvironmentInterface::_);
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_core_http_api_RequestParametersInterface::_);
        $this->_mockEvent                       = $this->mock('tubepress_core_event_api_EventInterface');
        $this->_mockLogger                      = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockEventDispatcher             = $this->mock(tubepress_core_event_api_EventDispatcherInterface::_);
        $this->_mockCollector                   = $this->mock(tubepress_core_media_provider_api_CollectorInterface::_);
        $this->_mockTemplateFactory             = $this->mock(tubepress_core_template_api_TemplateFactoryInterface::_);

        $this->_sut = new tubepress_core_html_gallery_impl_listeners_CoreGalleryHtmlListener(

            $this->_mockLogger,
            $this->_mockExecutionContext,
            $this->_mockEventDispatcher,
            $this->_mockOptionProvider,
            $this->_mockEnvironmentDetector,
            $this->_mockHttpRequestParameterService,
            $this->_mockCollector,
            $this->_mockTemplateFactory
        );
    }

    public function testOnGalleryHtml()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_api_Constants::OPTION_GALLERY_ID)->andReturn('gallery-id');

        $fakeArgs = array('yo' => 'mamma', 'is' => '"so fat"', 'x' => array('foo' => 500, 'html' => '<>\'"'));

        $internalEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $internalEvent->shouldReceive('getSubject')->once()->andReturn($fakeArgs);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(array())->andReturn($internalEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_html_gallery_api_Constants::EVENT_GALLERY_INIT_JS, $internalEvent);

        $event = $this->mock('tubepress_core_event_api_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn('hello');
        $event->shouldReceive('setSubject')->once()->with($this->_expectedAsyncJs());

        $this->_sut->onGalleryHtml($event);

        $this->assertTrue(true);
    }

    public function testOnHtmlGeneration()
    {
        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_core_http_api_Constants::PARAM_NAME_PAGE, 1)->andReturn('page-num');

        $mockTemplate = $this->mock('tubepress_core_template_api_TemplateInterface');
        $mockTemplate->shouldReceive('toString')->once()->andReturn('template-string');

        $this->_mockTemplateFactory->shouldReceive('fromFilesystem')->once()->with(array(
            'gallery.tpl.php', TUBEPRESS_ROOT . '/core/themes/web/default/gallery.tpl.php'))->andReturn($mockTemplate);

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_api_Constants::OPTION_GALLERY_ID)->andReturn('');
        $this->_mockExecutionContext->shouldReceive('setEphemeralOption')->once()->with(tubepress_core_html_api_Constants::OPTION_GALLERY_ID, ehough_mockery_Mockery::type('integer'))->andReturn(true);

        $mockFeedResult = new tubepress_core_media_provider_api_Page();
        $mockFeedResult->setItems(array('x', 'y'));

        $this->_mockCollector->shouldReceive('collectPage')->once()->andReturn($mockFeedResult);

        $mockTemplateEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockTemplateEvent->shouldReceive('getSubject')->once()->andReturn($mockTemplate);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($mockTemplate, array(

            'pageNumber'             => 'page-num',
            'page' => $mockFeedResult
        ))->andReturn($mockTemplateEvent);
        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->with(tubepress_core_html_gallery_api_Constants::EVENT_TEMPLATE_THUMBNAIL_GALLERY)->andReturn(true);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_html_gallery_api_Constants::EVENT_TEMPLATE_THUMBNAIL_GALLERY, $mockTemplateEvent);

        $mockHtmlEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockHtmlEvent->shouldReceive('getSubject')->once()->andReturn('bla');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('template-string', array(

            'pageNumber'             => 'page-num',
            'page' => $mockFeedResult
        ))->andReturn($mockHtmlEvent);
        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->with(tubepress_core_html_gallery_api_Constants::EVENT_HTML_THUMBNAIL_GALLERY)->andReturn(true);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_html_gallery_api_Constants::EVENT_HTML_THUMBNAIL_GALLERY, $mockHtmlEvent);

        $this->_mockEvent->shouldReceive('setSubject')->once()->with('bla');
        $this->_mockEvent->shouldReceive('stopPropagation')->once();

        $this->_sut->onHtmlGeneration($this->_mockEvent);
        $this->assertTrue(true);
    }

    public function testOnBeforeCssPage2()
    {
        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn('html');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_core_http_api_Constants::PARAM_NAME_PAGE, 1)->andReturn(2);

        $this->_mockEvent->shouldReceive('setSubject')->once()->with('html
<meta name="robots" content="noindex, nofollow" />');

        $this->_sut->onBeforeCssHtml($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testOnBeforeCssPage1()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_core_http_api_Constants::PARAM_NAME_PAGE, 1)->andReturn(1);

        $this->_mockEvent->shouldReceive('setSubject')->once()->with('html');
        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn('html');

        $this->_sut->onBeforeCssHtml($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testGalleryInitJs()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_gallery_api_Constants::OPTION_AJAX_PAGINATION)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT)->andReturn(999);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_WIDTH)->andReturn(888);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_gallery_api_Constants::OPTION_FLUID_THUMBS)->andReturn(false);
        $this->_mockExecutionContext->shouldReceive('get')->twice()->with(tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION)->andReturn('player-loc');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_http_api_Constants::OPTION_HTTP_METHOD)->andReturn('some-http-method');
        $this->_mockExecutionContext->shouldReceive('getEphemeralOptions')->once()->andReturn(array('x' => 'y', 'foo' => 'bar'));

        $this->_mockOptionProvider->shouldReceive('optionExists')->once()->with(tubepress_core_html_gallery_api_Constants::OPTION_AJAX_PAGINATION)->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('optionExists')->once()->with(tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT)->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('optionExists')->once()->with(tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_WIDTH)->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('optionExists')->once()->with(tubepress_core_html_gallery_api_Constants::OPTION_FLUID_THUMBS)->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('optionExists')->once()->with(tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION)->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('optionExists')->once()->with(tubepress_core_http_api_Constants::OPTION_HTTP_METHOD)->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('optionExists')->once()->with('x')->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('optionExists')->once()->with('foo')->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('optionExists')->once()->with('playerLocationJsUrl')->andReturn(false);
        $this->_mockOptionProvider->shouldReceive('optionExists')->once()->with('playerLocationProducesHtml')->andReturn(false);

        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with(tubepress_core_html_gallery_api_Constants::OPTION_AJAX_PAGINATION)->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with(tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT)->andReturn(false);
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with(tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_WIDTH)->andReturn(false);
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with(tubepress_core_html_gallery_api_Constants::OPTION_FLUID_THUMBS)->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with(tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION)->andReturn(false);
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with(tubepress_core_http_api_Constants::OPTION_HTTP_METHOD)->andReturn(false);
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with('x')->andReturn(false);
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with('foo')->andReturn(false);

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn(array('yo' => 'mamma'));
        $this->_mockEvent->shouldReceive('setSubject')->once()->with(array(

            'yo' => 'mamma',

            'nvpMap' => array(

                'embeddedHeight' => 999,
                'embeddedWidth' => 888,
                'playerLocation' => 'player-loc',
                'x' => 'y',
                'foo' => 'bar'
            ),

            'jsMap' => array(

                'playerLocationJsUrl' => '/abc',
                'playerLocationProducesHtml' => true,
                'ajaxPagination' => true,
                'fluidThumbs' => false,
                'httpMethod' => 'some-http-method',
            )
        ));

        $mockPlayer = $this->mock(tubepress_core_player_api_PlayerLocationInterface::_);
        $mockPlayer->shouldReceive('getName')->andReturn('player-loc');
        $mockPlayer->shouldReceive('getPlayerJsUrl')->andReturn('/abc/');
        $mockPlayer->shouldReceive('producesHtml')->once()->andReturn(true);
        $this->_sut->setPlayerLocations(array($mockPlayer));

        $this->_sut->onGalleryInitJs($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function _expectedAsyncJs()
    {
        return <<<EOT
hello<script type="text/javascript">
   var tubePressDomInjector = tubePressDomInjector || [], tubePressGalleryRegistrar = tubePressGalleryRegistrar || [];
       tubePressDomInjector.push(['loadGalleryJs']);
       tubePressGalleryRegistrar.push(['register', 'gallery-id', {"yo":"mamma","is":"\"so fat\"","x":{"foo":500,"html":"<>'\""}} ]);
</script>
EOT;
    }
}