<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_test_impl_player_DefaultPlayerHtmlGeneratorTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_player_DefaultPlayerHtmlGenerator
     */
    private $_sut;

    /**
     * @var tubepress_api_video_Video
     */
    private $_mockVideo;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThemeHandler;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_mockVideo            = new tubepress_api_video_Video();
        $this->_mockEventDispatcher  = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockThemeHandler     = $this->createMockSingletonService(tubepress_spi_theme_ThemeHandler::_);

        $this->_mockVideo->setAttribute(tubepress_api_video_Video::ATTRIBUTE_ID, 'video-id');

        $this->_sut = new tubepress_impl_player_DefaultPlayerHtmlGenerator();
    }

    public function testGetHtmlSuitablePlayerLocations()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn('x');

        $mockPlayerLocation = $this->createMockPluggableService(tubepress_spi_player_PluggablePlayerLocationService::_);
        $mockPlayerLocation->shouldReceive('getName')->andReturn('x');

        $mockTemplate = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');
        $mockTemplate->shouldReceive('toString')->once()->andReturn('foobarr');

        $mockPlayerLocation->shouldReceive('getTemplate')->once()->with($this->_mockThemeHandler)->andReturn($mockTemplate);

        $mockPlayerTemplateEvent = new tubepress_spi_event_EventBase(

            $mockTemplate, array(
                'video' => $this->_mockVideo,
                'playerName' => 'x'
            )
        );

        $mockVideo = $this->_mockVideo;

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_api_const_event_EventNames::TEMPLATE_PLAYERLOCATION,
            ehough_mockery_Mockery::on(function ($arg) use ($mockTemplate, $mockVideo) {

                return $arg->getSubject() == $mockTemplate && $arg->getArgument('video') == $mockVideo
                    && $arg->getArgument('playerName') === 'x';
            })
        )->andReturn($mockPlayerTemplateEvent);

        $mockPlayerHtmlEvent = new tubepress_spi_event_EventBase('foobarr', array(

            'video' => $mockVideo,
            'playerName' => 'current-player-name'
        ));

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_api_const_event_EventNames::HTML_PLAYERLOCATION,
            ehough_mockery_Mockery::on(function ($arg) use ($mockVideo) {

                return $arg->getSubject() == 'foobarr' && $arg->getArgument('video') == $mockVideo
                    && $arg->getArgument('playerName') === 'x';
            })
        )->andReturn($mockPlayerHtmlEvent);

        $this->assertEquals('foobarr', $this->_sut->getHtml($this->_mockVideo));
    }

    public function testGetHtmlNoSuitablePlayerLocations()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn('x');

        $mockPlayerLocation = $this->createMockPluggableService(tubepress_spi_player_PluggablePlayerLocationService::_);
        $mockPlayerLocation->shouldReceive('getName')->andReturn('z');

        $html = $this->_sut->getHtml($this->_mockVideo);

        $this->assertNull($html);
    }

    public function testGetHtmlNoPlayerLocations()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn('x');

        $html = $this->_sut->getHtml($this->_mockVideo);

        $this->assertNull($html);
    }
}