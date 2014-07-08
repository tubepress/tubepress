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
 * @covers tubepress_app_feature_gallery_impl_listeners_html_GenerationListener<extended>
 */
class tubepress_test_app_feature_gallery_impl_listeners_html_GenerationListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_feature_gallery_impl_listeners_html_GenerationListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

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
        $this->_mockExecutionContext            = $this->mock(tubepress_app_options_api_ContextInterface::_);
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_app_http_api_RequestParametersInterface::_);
        $this->_mockEvent                       = $this->mock('tubepress_lib_event_api_EventInterface');
        $this->_mockLogger                      = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $this->_mockEventDispatcher             = $this->mock(tubepress_lib_event_api_EventDispatcherInterface::_);
        $this->_mockCollector                   = $this->mock(tubepress_app_media_provider_api_CollectorInterface::_);
        $this->_mockTemplateFactory             = $this->mock(tubepress_lib_template_api_TemplateFactoryInterface::_);

        $this->_sut = new tubepress_app_feature_gallery_impl_listeners_html_GenerationListener(

            $this->_mockLogger,
            $this->_mockExecutionContext,
            $this->_mockEventDispatcher,
            $this->_mockHttpRequestParameterService,
            $this->_mockCollector,
            $this->_mockTemplateFactory
        );
    }

    public function testOnHtmlGeneration()
    {
        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_lib_http_api_Constants::PARAM_NAME_PAGE, 1)->andReturn('page-num');

        $mockTemplate = $this->mock('tubepress_lib_template_api_TemplateInterface');
        $mockTemplate->shouldReceive('toString')->once()->andReturn('template-string');

        $this->_mockTemplateFactory->shouldReceive('fromFilesystem')->once()->with(array(
            'gallery.tpl.php', TUBEPRESS_ROOT . '/web/themes/default/gallery.tpl.php'))->andReturn($mockTemplate);

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_html_api_Constants::OPTION_GALLERY_ID)->andReturn('');
        $this->_mockExecutionContext->shouldReceive('setEphemeralOption')->once()->with(tubepress_app_html_api_Constants::OPTION_GALLERY_ID, ehough_mockery_Mockery::type('integer'))->andReturn(true);

        $mockFeedResult = new tubepress_app_media_provider_api_Page();
        $mockFeedResult->setItems(array('x', 'y'));

        $this->_mockCollector->shouldReceive('collectPage')->once()->andReturn($mockFeedResult);

        $mockTemplateEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $mockTemplateEvent->shouldReceive('getSubject')->once()->andReturn($mockTemplate);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($mockTemplate, array(

            'pageNumber'             => 'page-num',
            'page' => $mockFeedResult
        ))->andReturn($mockTemplateEvent);
        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->with(tubepress_app_feature_gallery_api_Constants::EVENT_TEMPLATE_THUMBNAIL_GALLERY)->andReturn(true);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_feature_gallery_api_Constants::EVENT_TEMPLATE_THUMBNAIL_GALLERY, $mockTemplateEvent);

        $mockHtmlEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $mockHtmlEvent->shouldReceive('getSubject')->once()->andReturn('bla');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('template-string', array(

            'pageNumber'             => 'page-num',
            'page' => $mockFeedResult
        ))->andReturn($mockHtmlEvent);
        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->with(tubepress_app_feature_gallery_api_Constants::EVENT_HTML_THUMBNAIL_GALLERY)->andReturn(true);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_feature_gallery_api_Constants::EVENT_HTML_THUMBNAIL_GALLERY, $mockHtmlEvent);

        $this->_mockEvent->shouldReceive('setSubject')->once()->with('bla');
        $this->_mockEvent->shouldReceive('stopPropagation')->once();

        $this->_sut->onHtmlGeneration($this->_mockEvent);
        $this->assertTrue(true);
    }
}