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
class tubepress_plugins_vimeo_impl_embedded_VimeoEmbeddedPlayerTest extends TubePressUnitTest
{
    private $_sut;

    public function setUp() {

        $this->_sut = new tubepress_plugins_vimeo_impl_embedded_VimeoPluggableEmbeddedPlayerService();
    }

    public function testGetName()
    {
        $this->assertEquals('vimeo', $this->_sut->getName());
    }

    public function testGetProviderName()
    {
        $this->assertEquals('vimeo', $this->_sut->getHandledProviderName());
    }

    public function testGetTemplate()
    {
        $mockThemeHandler = Mockery::mock(tubepress_spi_theme_ThemeHandler::_);

        $mockThemeHandler->shouldReceive('getTemplateInstance')->once()->with(

            'embedded/vimeo.tpl.php',
            TUBEPRESS_ROOT . '/src/main/php/plugins/addon/vimeo/resources/templates'
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
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::ENABLE_JS_API)->andReturn(true);

        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_plugins_vimeo_api_const_options_names_Embedded::PLAYER_COLOR)->andReturn('ABCDEF');

        $result = $this->_sut->getDataUrlForVideo('xx');

        $this->assertTrue($result instanceof ehough_curly_Url);
        $this->assertEquals('http://player.vimeo.com/video/xx?autoplay=1&color=ABCDEF&loop=0&portrait=1&byline=1&title=1&api=1&player_id=tubepress-vimeo-player-xx', $result->toString());
    }

}

