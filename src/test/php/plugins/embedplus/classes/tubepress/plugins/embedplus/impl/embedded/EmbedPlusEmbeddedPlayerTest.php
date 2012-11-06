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
class tubepress_plugins_embedplus_impl_embedded_EmbedPlusEmbeddedPlayerTest extends TubePressUnitTest
{
    private $_sut;

    public function setUp() {

        $this->_sut = new tubepress_plugins_embedplus_impl_embedded_EmbedPlusPluggableEmbeddedPlayerService();
    }

    public function testGetName()
    {
        $this->assertEquals('embedplus', $this->_sut->getName());
    }

    public function testGetProviderName()
    {
        $this->assertEquals('youtube', $this->_sut->getHandledProviderName());
    }

    public function testGetTemplate()
    {
        $mockThemeHandler = Mockery::mock(tubepress_spi_theme_ThemeHandler::_);

        $mockThemeHandler->shouldReceive('getTemplateInstance')->once()->with(

            'embedded/embedplus.tpl.php',
            TUBEPRESS_ROOT . '/src/main/php/plugins/embedplus/resources/templates'
        )->andReturn('abc');

        $result = $this->_sut->getTemplate($mockThemeHandler);

        $this->assertEquals('abc', $result);
    }

    public function testGetDataUrl()
    {
        $mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($mockExecutionContext);


        $result = $this->_sut->getDataUrlForVideo('xx');

        $this->assertTrue($result instanceof ehough_curly_Url);
        $this->assertEquals('http://www.youtube.com/embed/xx', $result->toString());
    }

}

