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
class tubepress_plugins_core_impl_http_PlayerPluggableAjaxCommandServiceTest extends TubePressUnitTest
{
    /**
     * @var tubepress_plugins_core_impl_http_PlayerPluggableAjaxCommandService
     */
    private $_sut;

    private $_mockExecutionContext;

    private $_mockPlayerHtmlGenerator;

    private $_mockVideoCollector;

    private $_mockHttpRequestParameterService;

    private $_mockShortcodeParser;

    public function setUp()
    {
        $this->_sut = new tubepress_plugins_core_impl_http_PlayerPluggableAjaxCommandService();

        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        $this->_mockHttpRequestParameterService = Mockery::mock(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockVideoCollector = Mockery::mock(tubepress_spi_collector_VideoCollector::_);
        $this->_mockPlayerHtmlGenerator = Mockery::mock(tubepress_spi_player_PlayerHtmlGenerator::_);
        $this->_mockShortcodeParser = Mockery::mock(tubepress_spi_shortcode_ShortcodeParser::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpRequestParameterService($this->_mockHttpRequestParameterService);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setVideoProvider($this->_mockVideoCollector);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setPlayerHtmlGenerator($this->_mockPlayerHtmlGenerator);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setShortcodeHtmlParser($this->_mockShortcodeParser);
    }

    public function testVideoFound()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::SHORTCODE)->andReturn('-shortcode-');
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::VIDEO)->andReturn('-video-');

        $this->_mockShortcodeParser->shouldReceive('parse')->once()->with('-shortcode-');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::LAZYPLAY)->andReturn(false);

        $mockVideo = new tubepress_api_video_Video();
        $mockVideo->setAttribute(tubepress_api_video_Video::ATTRIBUTE_TITLE, 'video title');

        $this->_mockPlayerHtmlGenerator->shouldReceive('getHtml')->once()->with($mockVideo)->andReturn('player-html');

        $this->_mockVideoCollector->shouldReceive('collectSingleVideo')->once()->andReturn($mockVideo);

        $this->_sut->handle();

        $this->assertEquals(200, $this->_sut->getHttpStatusCode());
        $this->assertEquals('{ "title" : "video%20title", "html" : "player-html" }', $this->_sut->getOutput());
    }

    public function testLazyPlay()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::SHORTCODE)->andReturn('-shortcode-');
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::VIDEO)->andReturn('-video-');

        $this->_mockShortcodeParser->shouldReceive('parse')->once()->with('-shortcode-');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::LAZYPLAY)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_api_const_options_names_Embedded::AUTOPLAY, true);

        $this->_mockVideoCollector->shouldReceive('collectSingleVideo')->once()->andReturn(null);

        $this->_sut->handle();

        $this->assertEquals(404, $this->_sut->getHttpStatusCode());
        $this->assertEquals('Video -video- not found', $this->_sut->getOutput());
    }

    public function testVideoNotFound()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::SHORTCODE)->andReturn('-shortcode-');
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::VIDEO)->andReturn('-video-');

        $this->_mockShortcodeParser->shouldReceive('parse')->once()->with('-shortcode-');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::LAZYPLAY)->andReturn(false);

        $this->_mockVideoCollector->shouldReceive('collectSingleVideo')->once()->andReturn(null);

        $this->_sut->handle();

        $this->assertEquals(404, $this->_sut->getHttpStatusCode());
        $this->assertEquals('Video -video- not found', $this->_sut->getOutput());
    }

    public function testGetCommandName()
    {
        $this->assertEquals('playerHtml', $this->_sut->getCommandName());
    }

}
