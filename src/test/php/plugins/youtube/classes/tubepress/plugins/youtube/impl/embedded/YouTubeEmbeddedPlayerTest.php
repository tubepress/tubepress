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
class tubepress_plugins_youtube_impl_embedded_YouTubeEmbeddedPlayerTest extends TubePressUnitTest
{
    /**
     * @var tubepress_plugins_youtube_impl_embedded_YouTubePluggableEmbeddedPlayerService
     */
    private $_sut;

    public function setUp()
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
        $mockThemeHandler = Mockery::mock(tubepress_spi_theme_ThemeHandler::_);

        $mockThemeHandler->shouldReceive('getTemplateInstance')->once()->with(

            'embedded/youtube.tpl.php',
            TUBEPRESS_ROOT . '/src/main/php/plugins/youtube/resources/templates'
        )->andReturn('abc');

        $result = $this->_sut->getTemplate($mockThemeHandler);

        $this->assertEquals('abc', $result);
    }

    public function testGetDataUrl()
    {
        $mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($mockExecutionContext);

        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::AUTOPLAY)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::LOOP)->andReturn(false);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::SHOW_INFO)->andReturn(true);

        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_plugins_youtube_api_const_options_names_Embedded::AUTOHIDE)->andReturn(false);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_plugins_youtube_api_const_options_names_Embedded::SHOW_ANNOTATIONS)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_plugins_youtube_api_const_options_names_Embedded::CLOSED_CAPTIONS)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_plugins_youtube_api_const_options_names_Embedded::SHOW_CONTROLS)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_plugins_youtube_api_const_options_names_Embedded::DISABLE_KEYBOARD)->andReturn(false);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::ENABLE_JS_API)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_plugins_youtube_api_const_options_names_Embedded::FULLSCREEN)->andReturn(false);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_plugins_youtube_api_const_options_names_Embedded::MODEST_BRANDING)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_plugins_youtube_api_const_options_names_Embedded::SHOW_RELATED)->andReturn(false);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_plugins_youtube_api_const_options_names_Embedded::THEME)->andReturn(tubepress_plugins_youtube_api_const_options_values_ThemeValue::DARK);

        $result = $this->_sut->getDataUrlForVideo('xx');

        $this->assertTrue($result instanceof ehough_curly_Url);
        $this->assertEquals('http://www.youtube.com/embed/xx?autohide=0&autoplay=1&cc_load_policy=1&controls=2&disablekb=0&enablejsapi=1&fs=0&iv_load_policy=1&loop=0&modestbranding=1&rel=0&showinfo=1&theme=dark', $result->toString());
    }
}
