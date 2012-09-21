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
class org_tubepress_impl_player_DefaultPlayerHtmlGeneratorTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockVideo;

    private $_mockExecutionContext;

    private $_mockThemeHandler;

    private $_mockProviderCalculator;

    private $_mockEventDispatcher;

    function setUp()
    {
        $this->_mockVideo              = new tubepress_api_video_Video();
        $this->_mockVideo->setAttribute(tubepress_api_video_Video::ATTRIBUTE_ID, 'video-id');
        $this->_mockEventDispatcher    = Mockery::mock('ehough_tickertape_api_IEventDispatcher');
        $this->_mockExecutionContext   = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        $this->_mockProviderCalculator = Mockery::mock(tubepress_spi_provider_ProviderCalculator::_);
        $this->_mockThemeHandler       = Mockery::mock(tubepress_spi_theme_ThemeHandler::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setEventDispatcher($this->_mockEventDispatcher);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setVideoProviderCalculator($this->_mockProviderCalculator);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setThemeHandler($this->_mockThemeHandler);

        $this->_sut = new tubepress_impl_player_DefaultPlayerHtmlGenerator();
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

            $mockTemplate, array(
            tubepress_api_event_PlayerTemplateConstruction::ARGUMENT_VIDEO => $mockVideo,
            tubepress_api_event_PlayerTemplateConstruction::ARGUMENT_PROVIDER_NAME => 'video-provider',
            tubepress_api_event_PlayerTemplateConstruction::ARGUMENT_PLAYER_NAME => 'current-player-name'
            )
        );

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_api_event_PlayerTemplateConstruction::EVENT_NAME,
            Mockery::on(function ($arg) use ($mockTemplate, $mockVideo) {

                return $arg->getSubject() == $mockTemplate && $arg->getArgument(tubepress_api_event_PlayerTemplateConstruction::ARGUMENT_VIDEO) == $mockVideo
                    && $arg->getArgument(tubepress_api_event_PlayerTemplateConstruction::ARGUMENT_PROVIDER_NAME) === 'video-provider'
                    && $arg->getArgument(tubepress_api_event_PlayerTemplateConstruction::ARGUMENT_PLAYER_NAME) === 'current-player-name';
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

        $mockHtmlEvent = new tubepress_api_event_TubePressEvent('foobarr');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_api_const_event_CoreEventNames::HTML_CONSTRUCTION,
            Mockery::on(function ($arg) {

                return $arg->getSubject() === 'foobarr';
            })
        )->andReturn($mockHtmlEvent);

        $this->assertEquals('foobarr', $this->_sut->getHtml($this->_mockVideo));
    }
}