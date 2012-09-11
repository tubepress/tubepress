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
class org_tubepress_impl_player_DefaultPlayerHtmlGeneratorTest extends PHPUnit_Framework_TestCase
{
    private $_sut;

    private $_mockVideo;

    private $_mockExecutionContext;

    private $_mockThemeHandler;

    private $_mockProviderCalculator;

    private $_mockEventDispatcher;

    function setUp()
    {
        $this->_mockVideo              = Mockery::mock('tubepress_api_video_Video');
        $this->_mockEventDispatcher    = Mockery::mock('ehough_tickertape_api_IEventDispatcher');
        $this->_mockExecutionContext   = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        $this->_mockProviderCalculator = Mockery::mock(tubepress_spi_provider_ProviderCalculator::_);
        $this->_mockThemeHandler       = Mockery::mock(tubepress_spi_theme_ThemeHandler::_);

        $this->_sut = new tubepress_impl_player_DefaultPlayerHtmlGenerator(

            $this->_mockExecutionContext,
            $this->_mockThemeHandler,
            $this->_mockProviderCalculator,
            $this->_mockEventDispatcher
        );

        $this->_mockVideo->shouldReceive('getId')->once()->andReturn('video-id');
    }

    public function testGetHtml()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn('current-player-name');

        $mockTemplate = Mockery::mock('ehough_contemplate_api_Template');
        $mockTemplate->shouldReceive('toString')->once()->andReturn('foobarr');

        $this->_mockThemeHandler->shouldReceive('getTemplateInstance')->once()->with('players/current-player-name.tpl.php')->andReturn($mockTemplate);

        $this->_mockProviderCalculator->shouldReceive('calculateProviderOfVideoId')->once()->with('video-id')->andReturn('video-provider');

        $mockVideo = $this->_mockVideo;
        $mockPlayerTemplateEvent = new tubepress_api_event_PlayerTemplateConstruction(

            $mockTemplate, $mockVideo, 'video-provider', 'current-player-name'
        );

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_api_event_PlayerTemplateConstruction::EVENT_NAME,
            Mockery::on(function ($arg) use ($mockTemplate, $mockVideo) {

                return $arg->template == $mockTemplate && $arg->video == $mockVideo
                    && $arg->providerName === 'video-provider' && $arg->playerName === 'current-player-name';
            })
        )->andReturn($mockPlayerTemplateEvent);

        $mockPlayerHtmlEvent = new tubepress_api_event_PlayerHtmlConstruction('foobarr', $mockVideo, 'video-provider', 'current-player-name');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_api_event_PlayerHtmlConstruction::EVENT_NAME,
            Mockery::on(function ($arg) use ($mockVideo) {

                return $arg->playerHtml == 'foobarr' && $arg->video == $mockVideo
                    && $arg->providerName === 'video-provider' && $arg->playerName === 'current-player-name';
            })
        )->andReturn($mockPlayerHtmlEvent);

        $mockHtmlEvent = new tubepress_api_event_HtmlConstruction('foobarr');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_api_event_HtmlConstruction::EVENT_NAME,
            Mockery::on(function ($arg) {

                return $arg->html === 'foobarr';
            })
        )->andReturn($mockHtmlEvent);

        $this->assertEquals('foobarr', $this->_sut->getHtml($this->_mockVideo));
    }
}