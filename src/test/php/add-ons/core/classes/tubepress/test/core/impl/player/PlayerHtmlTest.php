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
 * @covers tubepress_core_impl_player_PlayerHtml
 */
class tubepress_test_core_impl_player_PlayerHtmlTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_player_PlayerHtml
     */
    private $_sut;

    /**
     * @var tubepress_core_api_video_Video
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

    public function onSetup()
    {
        $this->_mockVideo            = new tubepress_core_api_video_Video();
        $this->_mockEventDispatcher  = $this->mock(tubepress_core_api_event_EventDispatcherInterface::_);
        $this->_mockExecutionContext = $this->mock(tubepress_core_api_options_ContextInterface::_);

        $this->_mockVideo->setAttribute(tubepress_core_api_video_Video::ATTRIBUTE_ID, 'video-id');

        $this->_sut = new tubepress_core_impl_player_PlayerHtml($this->_mockExecutionContext, $this->_mockEventDispatcher);
    }

    public function testGetHtmlSuitablePlayerLocations()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::PLAYER_LOCATION)->andReturn('x');

        $mockPlayerLocation = $this->mock(tubepress_core_api_player_PlayerLocationInterface::_);
        $mockPlayerLocation->shouldReceive('getName')->andReturn('x');

        $mockTemplate = $this->mock('tubepress_core_api_template_TemplateInterface');
        $mockTemplate->shouldReceive('toString')->once()->andReturn('foobarr');

        $mockPlayerLocation->shouldReceive('getTemplate')->once()->andReturn($mockTemplate);

        $mockSelectionEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockSelectionEvent->shouldReceive('getArgument')->once()->with('selected')->andReturn($mockPlayerLocation);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($this->_mockVideo, array(

            'selected' => null,
            'requestedPlayerLocation' => 'x'
        ))->andReturn($mockSelectionEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_api_const_event_EventNames::SELECT_PLAYER_LOCATION, $mockSelectionEvent);

        $mockTemplateEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockTemplateEvent->shouldReceive('getSubject')->once()->andReturn($mockTemplate);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(
            $mockTemplate, array(
                'video' => $this->_mockVideo,
                'playerName' => 'x'
            )
        )->andReturn($mockTemplateEvent);

        $mockVideo = $this->_mockVideo;

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_core_api_const_event_EventNames::TEMPLATE_PLAYERLOCATION, $mockTemplateEvent
        );

        $mockHtmlEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockHtmlEvent->shouldReceive('getSubject')->once()->andReturn('abc');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(
            'foobarr', array(

                'video' => $mockVideo,
                'playerName' => 'x'
            )
        )->andReturn($mockHtmlEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_core_api_const_event_EventNames::HTML_PLAYERLOCATION, $mockHtmlEvent);

        $this->assertEquals('abc', $this->_sut->getHtml($this->_mockVideo));
    }

    public function testGetHtmlNoSuitablePlayerLocations()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::PLAYER_LOCATION)->andReturn('x');

        $mockPlayerLocation = $this->mock(tubepress_core_api_player_PlayerLocationInterface::_);
        $mockPlayerLocation->shouldReceive('getName')->andReturn('z');

        $mockSelectionEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockSelectionEvent->shouldReceive('getArgument')->once()->with('selected')->andReturn(null);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($this->_mockVideo, array(

            'selected' => null,
            'requestedPlayerLocation' => 'x'
        ))->andReturn($mockSelectionEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_api_const_event_EventNames::SELECT_PLAYER_LOCATION, $mockSelectionEvent);

        $html = $this->_sut->getHtml($this->_mockVideo);

        $this->assertNull($html);
    }
}