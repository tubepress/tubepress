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
 * @covers tubepress_core_html_single_impl_listeners_html_SingleVideoListener
 */
class tubepress_test_core_html_single_impl_listeners_html_SingleVideoCommandTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_html_single_impl_listeners_html_SingleVideoListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockProvider;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplateFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    public function onSetup()
    {
        $this->_mockExecutionContext = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_mockEventDispatcher  = $this->mock(tubepress_core_event_api_EventDispatcherInterface::_);
        $this->_mockProvider            = $this->mock(tubepress_core_media_provider_api_CollectorInterface::_);
        $this->_mockLogger                       = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockTemplateFactory     = $this->mock(tubepress_core_template_api_TemplateFactoryInterface::_);
        $this->_mockEvent            = $this->mock('tubepress_core_event_api_EventInterface');

        $this->_sut = new tubepress_core_html_single_impl_listeners_html_SingleVideoListener(

            $this->_mockLogger,
            $this->_mockExecutionContext,
            $this->_mockEventDispatcher,
            $this->_mockProvider,
            $this->_mockTemplateFactory
        );
    }

    public function testExecuteNoVideo()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_single_api_Constants::OPTION_MEDIA_ITEM_ID)->andReturn('');

        $this->_sut->onHtmlGeneration($this->_mockEvent);
        $this->assertTrue(true);
    }

    public function testExecute()
    {
        $this->_mockLogger->shouldReceive('isEnabled')->twice()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_mockExecutionContext->shouldReceive('get')->twice()->with(tubepress_core_html_single_api_Constants::OPTION_MEDIA_ITEM_ID)->andReturn('video-id');

        $mockTemplate = $this->mock('tubepress_core_template_api_TemplateInterface');
        $mockTemplate->shouldReceive('toString')->once()->andReturn('template-string');

        $this->_mockTemplateFactory->shouldReceive('fromFilesystem')->once()->with(array(
            'single_video.tpl.php', TUBEPRESS_ROOT . '/core/themes/web/default/single_video.tpl.php'
        ))->andReturn($mockTemplate);

        $video = new tubepress_core_media_item_api_MediaItem('video-id');

        $this->_mockProvider->shouldReceive('collectSingle')->once()->with('video-id')->andReturn($video);

        $mockTemplateEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockTemplateEvent->shouldReceive('getSubject')->once()->andReturn($mockTemplate);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($mockTemplate, array(

            'item' => $video
        ))->andReturn($mockTemplateEvent);
        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->with(tubepress_core_html_single_api_Constants::EVENT_SINGLE_ITEM_TEMPLATE)->andReturn(true);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_html_single_api_Constants::EVENT_SINGLE_ITEM_TEMPLATE, $mockTemplateEvent);

        $mockHtmlEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockHtmlEvent->shouldReceive('getSubject')->once()->andReturn('foobar');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('template-string')->andReturn($mockHtmlEvent);
        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->with(tubepress_core_html_single_api_Constants::EVENT_SINGLE_ITEM_HTML)->andReturn(true);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_html_single_api_Constants::EVENT_SINGLE_ITEM_HTML, $mockHtmlEvent);

        $this->_mockEvent->shouldReceive('setSubject')->once()->with('foobar');
        $this->_mockEvent->shouldReceive('stopPropagation')->once();

        $this->_sut->onHtmlGeneration($this->_mockEvent);
        $this->assertTrue(true);
    }
}