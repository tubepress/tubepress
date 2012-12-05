<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_impl_player_DefaultPlayerHtmlGeneratorTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockVideo;

    private $_mockExecutionContext;

    private $_mockThemeHandler;

    private $_mockEventDispatcher;

    function onSetup()
    {
        $this->_mockVideo            = new tubepress_api_video_Video();
        $this->_mockEventDispatcher  = $this->createMockSingletonService('ehough_tickertape_api_IEventDispatcher');
        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockThemeHandler     = $this->createMockSingletonService(tubepress_spi_theme_ThemeHandler::_);

        $this->_mockVideo->setAttribute(tubepress_api_video_Video::ATTRIBUTE_ID, 'video-id');

        $this->_sut = new tubepress_impl_player_DefaultPlayerHtmlGenerator();
    }

    function testGetHtmlSuitablePlayerLocations()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn('x');

        $mockPlayerLocation = $this->createMockPluggableService(tubepress_spi_player_PluggablePlayerLocationService::_);
        $mockPlayerLocation->shouldReceive('getName')->andReturn('x');

        $mockTemplate = Mockery::mock('ehough_contemplate_api_Template');
        $mockTemplate->shouldReceive('toString')->once()->andReturn('foobarr');

        $mockPlayerLocation->shouldReceive('getTemplate')->once()->with($this->_mockThemeHandler)->andReturn($mockTemplate);

        $mockPlayerTemplateEvent = new tubepress_api_event_TubePressEvent(

            $mockTemplate, array(
                'video' => $this->_mockVideo,
                'playerName' => 'x'
            )
        );

        $mockVideo = $this->_mockVideo;

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_api_const_event_CoreEventNames::PLAYER_TEMPLATE_CONSTRUCTION,
            Mockery::on(function ($arg) use ($mockTemplate, $mockVideo) {

                return $arg->getSubject() == $mockTemplate && $arg->getArgument('video') == $mockVideo
                    && $arg->getArgument('playerName') === 'x';
            })
        )->andReturn($mockPlayerTemplateEvent);

        $mockPlayerHtmlEvent = new tubepress_api_event_TubePressEvent('foobarr', array(

            'video' => $mockVideo,
            'playerName' => 'current-player-name'
        ));

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_api_const_event_CoreEventNames::PLAYER_HTML_CONSTRUCTION,
            Mockery::on(function ($arg) use ($mockVideo) {

                return $arg->getSubject() == 'foobarr' && $arg->getArgument('video') == $mockVideo
                    && $arg->getArgument('playerName') === 'x';
            })
        )->andReturn($mockPlayerHtmlEvent);

        $mockHtmlEvent = new tubepress_api_event_TubePressEvent('foobarr');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_api_const_event_CoreEventNames::HTML_CONSTRUCTION,
            Mockery::on(function ($arg) {

                return $arg->getSubject() === 'foobarr';
            })
        )->andReturn($mockHtmlEvent);

        $this->assertEquals('foobarr', $this->_sut->getHtml($this->_mockVideo));
    }

    function testGetHtmlNoSuitablePlayerLocations()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn('x');

        $mockPlayerLocation = $this->createMockPluggableService(tubepress_spi_player_PluggablePlayerLocationService::_);
        $mockPlayerLocation->shouldReceive('getName')->andReturn('z');

        $html = $this->_sut->getHtml($this->_mockVideo);

        $this->assertNull($html);
    }

    function testGetHtmlNoPlayerLocations()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn('x');

        $html = $this->_sut->getHtml($this->_mockVideo);

        $this->assertNull($html);
    }
}