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
 * @covers tubepress_app_player_impl_PlayerHtml
 */
class tubepress_test_app_player_impl_PlayerHtmlTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_player_impl_PlayerHtml
     */
    private $_sut;

    /**
     * @var tubepress_app_media_item_api_MediaItem
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

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPlayerLocation;

    public function onSetup()
    {
        $this->_mockVideo            = new tubepress_app_media_item_api_MediaItem('video-id');
        $this->_mockEventDispatcher  = $this->mock(tubepress_lib_event_api_EventDispatcherInterface::_);
        $this->_mockExecutionContext = $this->mock(tubepress_app_options_api_ContextInterface::_);
        $this->_mockTemplateFactory  = $this->mock(tubepress_lib_template_api_TemplateFactoryInterface::_);
        $this->_mockPlayerLocation   = $this->mock(tubepress_app_player_api_PlayerLocationInterface::_);

        $this->_sut = new tubepress_app_player_impl_PlayerHtml(

            $this->_mockExecutionContext,
            $this->_mockTemplateFactory,
            $this->_mockEventDispatcher
        );

        $this->_sut->setPlayerLocations(array($this->_mockPlayerLocation));

        $this->_mockPlayerLocation->shouldReceive('getName')->andReturn('x');
    }

    public function testGetHtmlSuitablePlayerLocations()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_player_api_Constants::OPTION_PLAYER_LOCATION)->andReturn('x');

        $mockTemplate = $this->mock('tubepress_lib_template_api_TemplateInterface');
        $mockTemplate->shouldReceive('toString')->once()->andReturn('foobarr');

        $this->_mockPlayerLocation->shouldReceive('getTemplatePathsForStaticContent')->once()->andReturn(array('x'));
        $this->_mockTemplateFactory->shouldReceive('fromFilesystem')->once()->with(array('x'))->andReturn($mockTemplate);

        $mockSelectionEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $mockSelectionEvent->shouldReceive('getArgument')->once()->with('playerLocation')->andReturn($this->_mockPlayerLocation);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($this->_mockVideo, array(

            'playerLocation' => $this->_mockPlayerLocation
        ))->andReturn($mockSelectionEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_player_api_Constants::EVENT_PLAYER_SELECT, $mockSelectionEvent);

        $mockTemplateEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $mockTemplateEvent->shouldReceive('getSubject')->once()->andReturn($mockTemplate);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(
            $mockTemplate, array(
                'item' => $this->_mockVideo,
                'playerLocation' => $this->_mockPlayerLocation,
                'isAjax'                      => false,
            )
        )->andReturn($mockTemplateEvent);

        $mockVideo = $this->_mockVideo;

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_app_player_api_Constants::EVENT_PLAYER_TEMPLATE, $mockTemplateEvent
        );

        $mockHtmlEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $mockHtmlEvent->shouldReceive('getSubject')->once()->andReturn('abc');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(
            'foobarr', array(

                'item' => $mockVideo,
                'playerLocation' => $this->_mockPlayerLocation,
                'isAjax'                      => false,
            )
        )->andReturn($mockHtmlEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_app_player_api_Constants::EVENT_PLAYER_HTML, $mockHtmlEvent);

        $this->assertEquals('abc', $this->_sut->getStaticHtml($this->_mockVideo));
    }

    public function testGetHtmlNoSuitablePlayerLocations()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_player_api_Constants::OPTION_PLAYER_LOCATION)->andReturn('z');

        $mockSelectionEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $mockSelectionEvent->shouldReceive('getArgument')->once()->with('playerLocation')->andReturn(null);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($this->_mockVideo, array(

            'playerLocation' => null,
        ))->andReturn($mockSelectionEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_player_api_Constants::EVENT_PLAYER_SELECT, $mockSelectionEvent);

        $html = $this->_sut->getStaticHtml($this->_mockVideo);

        $this->assertNull($html);
    }
}