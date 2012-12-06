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
class tubepress_plugins_youtube_impl_embedded_YouTubeEmbeddedPlayerTest extends TubePressUnitTest
{
    /**
     * @var tubepress_plugins_youtube_impl_embedded_YouTubePluggableEmbeddedPlayerService
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_plugins_youtube_impl_embedded_YouTubePluggableEmbeddedPlayerService();
    }

    public function testGetName()
    {
        $this->assertEquals('youtube', $this->_sut->getName());
    }

    public function testGetProviderName()
    {
        $this->assertEquals('youtube', $this->_sut->getHandledProviderName());
    }

    public function testGetTemplate()
    {
        $mockThemeHandler = $this->createMockSingletonService(tubepress_spi_theme_ThemeHandler::_);

        $mockThemeHandler->shouldReceive('getTemplateInstance')->once()->with(

            'embedded/youtube.tpl.php',
            TUBEPRESS_ROOT . '/src/main/php/plugins/youtube/resources/templates'
        )->andReturn('abc');

        $result = $this->_sut->getTemplate($mockThemeHandler);

        $this->assertEquals('abc', $result);
    }

    public function testGetDataUrl()
    {
        $mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);

        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::AUTOPLAY)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::LOOP)->andReturn(false);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::SHOW_INFO)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_plugins_youtube_api_const_options_names_Embedded::AUTOHIDE)->andReturn(false);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::ENABLE_JS_API)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_plugins_youtube_api_const_options_names_Embedded::FULLSCREEN)->andReturn(false);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_plugins_youtube_api_const_options_names_Embedded::MODEST_BRANDING)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_plugins_youtube_api_const_options_names_Embedded::SHOW_RELATED)->andReturn(false);

        $result = $this->_sut->getDataUrlForVideo('xx');

        $this->assertTrue($result instanceof ehough_curly_Url);
        $this->assertEquals('http://www.youtube.com/embed/xx?autohide=0&autoplay=1&enablejsapi=1&fs=0&loop=0&modestbranding=1&rel=0&showinfo=1', $result->toString());
    }
}
