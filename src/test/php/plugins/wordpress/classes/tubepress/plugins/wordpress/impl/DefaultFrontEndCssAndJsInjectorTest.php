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
class tubepress_plugins_wordpress_impl_DefaultFrontEndCssAndJsInjectorTest extends TubePressUnitTest
{
    private $_mockWpFunctionWrapper;

    private $_sut;

    private $_mockHeadHtmlGenerator;

    private $_mockEnvironmentDetector;

    public function onSetup()
    {
        $this->_sut = new tubepress_plugins_wordpress_impl_DefaultFrontEndCssAndJsInjector();

        $this->_mockWpFunctionWrapper   = $this->createMockSingletonService(tubepress_plugins_wordpress_spi_WordPressFunctionWrapper::_);
        $this->_mockHeadHtmlGenerator   = $this->createMockSingletonService(tubepress_spi_html_HeadHtmlGenerator::_);
        $this->_mockEnvironmentDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
    }

    function testHeadAction()
    {
        $this->_mockWpFunctionWrapper->shouldReceive('is_admin')->once()->andReturn(false);

        $this->_mockHeadHtmlGenerator->shouldReceive('getHeadInlineJs')->once()->andReturn('inline js');
        $this->_mockHeadHtmlGenerator->shouldReceive('getHeadHtmlMeta')->once()->andReturn('html meta');

        ob_start();

        $this->_sut->printInHtmlHead();

        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('inline js
html meta', $contents);
    }

    function testInitAction()
    {
        $this->_mockWpFunctionWrapper->shouldReceive('is_admin')->once()->andReturn(false);
        $this->_mockWpFunctionWrapper->shouldReceive('plugins_url')->once()->with('tubepress/src/main/web/js/tubepress.js', 'tubepress')->andReturn('<tubepressjs>');
        $this->_mockWpFunctionWrapper->shouldReceive('plugins_url')->once()->with('tubepress/src/main/web/css/tubepress.css', 'tubepress')->andReturn('<tubepresscss>');

        $this->_mockWpFunctionWrapper->shouldReceive('wp_register_script')->once()->with('tubepress', '<tubepressjs>');
        $this->_mockWpFunctionWrapper->shouldReceive('wp_register_style')->once()->with('tubepress', '<tubepresscss>');

        $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('tubepress');
        $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_style')->once()->with('tubepress');
        $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('jquery');

        $this->_sut->registerStylesAndScripts();

        $this->assertTrue(true);
    }
}