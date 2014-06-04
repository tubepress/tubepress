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
 * @covers tubepress_core_player_impl_PlayerHtml
 */
class tubepress_test_core_player_impl_PlayerHtmlTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_player_impl_PlayerHtml
     */
    private $_sut;

    /**
     * @var tubepress_core_provider_api_MediaItem
     */
    private $_mockVideo;

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
    private $_mockTemplateFactory;

    public function onSetup()
    {
        $this->_mockVideo            = new tubepress_core_provider_api_MediaItem();
        $this->_mockEventDispatcher  = $this->mock(tubepress_core_event_api_EventDispatcherInterface::_);
        $this->_mockExecutionContext = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_mockTemplateFactory  = $this->mock(tubepress_core_template_api_TemplateFactoryInterface::_);

        $this->_mockVideo->setAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_ID, 'video-id');

        $this->_sut = new tubepress_core_player_impl_PlayerHtml($this->_mockExecutionContext, $this->_mockTemplateFactory, $this->_mockEventDispatcher);
    }

    public function testGetHtmlSuitablePlayerLocations()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION)->andReturn('x');

        $mockPlayerLocation = $this->mock(tubepress_core_player_api_PlayerLocationInterface::_);
        $mockPlayerLocation->shouldReceive('getName')->andReturn('x');

        $mockTemplate = $this->mock('tubepress_core_template_api_TemplateInterface');
        $mockTemplate->shouldReceive('toString')->once()->andReturn('foobarr');

        $mockPlayerLocation->shouldReceive('getPathsForTemplateFactory')->once()->andReturn(array('x'));
        $this->_mockTemplateFactory->shouldReceive('fromFilesystem')->once()->with(array('x'))->andReturn($mockTemplate);

        $mockSelectionEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockSelectionEvent->shouldReceive('getArgument')->once()->with('playerLocation')->andReturn($mockPlayerLocation);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($this->_mockVideo, array(

            'playerLocation' => null,
            'requestedPlayerLocationName' => 'x'
        ))->andReturn($mockSelectionEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_player_api_Constants::EVENT_PLAYER_SELECT, $mockSelectionEvent);

        $mockTemplateEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockTemplateEvent->shouldReceive('getSubject')->once()->andReturn($mockTemplate);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(
            $mockTemplate, array(
                'item' => $this->_mockVideo,
                'playerLocation' => $mockPlayerLocation
            )
        )->andReturn($mockTemplateEvent);

        $mockVideo = $this->_mockVideo;

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_core_player_api_Constants::EVENT_PLAYER_TEMPLATE, $mockTemplateEvent
        );

        $mockHtmlEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockHtmlEvent->shouldReceive('getSubject')->once()->andReturn('abc');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(
            'foobarr', array(

                'item' => $mockVideo,
                'playerLocation' => $mockPlayerLocation
            )
        )->andReturn($mockHtmlEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_core_player_api_Constants::EVENT_PLAYER_HTML, $mockHtmlEvent);

        $this->assertEquals('abc', $this->_sut->getHtml($this->_mockVideo));
    }

    public function testGetHtmlNoSuitablePlayerLocations()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION)->andReturn('x');

        $mockPlayerLocation = $this->mock(tubepress_core_player_api_PlayerLocationInterface::_);
        $mockPlayerLocation->shouldReceive('getName')->andReturn('z');

        $mockSelectionEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockSelectionEvent->shouldReceive('getArgument')->once()->with('playerLocation')->andReturn(null);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($this->_mockVideo, array(

            'playerLocation' => null,
            'requestedPlayerLocationName' => 'x'
        ))->andReturn($mockSelectionEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_player_api_Constants::EVENT_PLAYER_SELECT, $mockSelectionEvent);

        $html = $this->_sut->getHtml($this->_mockVideo);

        $this->assertNull($html);
    }
}