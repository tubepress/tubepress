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
class org_tubepress_plugins_wordpresscore_lib_impl_DefaultFrontEndCssAndJsInjectorTest extends TubePressUnitTest
{
    private $_mockWpFunctionWrapper;

    private $_sut;

    private $_mockHeadHtmlGenerator;

    private $_mockEnvironmentDetector;

    public function setUp()
    {
        $this->_sut = new tubepress_plugins_wordpresscore_lib_impl_DefaultFrontEndCssAndJsInjector();

        $this->_mockWpFunctionWrapper = Mockery::mock(tubepress_plugins_wordpresscore_lib_spi_WordPressFunctionWrapper::_);
        $this->_mockHeadHtmlGenerator = Mockery::mock(tubepress_spi_html_HeadHtmlGenerator::_);
        $this->_mockEnvironmentDetector = Mockery::mock(tubepress_spi_environment_EnvironmentDetector::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setHeadHtmlGenerator($this->_mockHeadHtmlGenerator);
        tubepress_plugins_wordpresscore_lib_impl_patterns_ioc_WordPressServiceLocator::setWordPressFunctionWrapper($this->_mockWpFunctionWrapper);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEnvironmentDetector($this->_mockEnvironmentDetector);
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
        $this->_mockEnvironmentDetector->shouldReceive('getTubePressInstallationDirectoryBaseName')->once()->andReturn('base_name');

        $this->_mockWpFunctionWrapper->shouldReceive('is_admin')->once()->andReturn(false);
        $this->_mockWpFunctionWrapper->shouldReceive('plugins_url')->once()->with('base_name/src/main/web/js/tubepress.js', 'base_name')->andReturn('<tubepressjs>');
        $this->_mockWpFunctionWrapper->shouldReceive('plugins_url')->once()->with('base_name/src/main/resources/default-themes/default/style.css', 'base_name')->andReturn('<tubepresscss>');

        $this->_mockWpFunctionWrapper->shouldReceive('wp_register_script')->once()->with('tubepress', '<tubepressjs>');
        $this->_mockWpFunctionWrapper->shouldReceive('wp_register_style')->once()->with('tubepress', '<tubepresscss>');

        $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('tubepress');
        $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_style')->once()->with('tubepress');
        $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('jquery');

        $this->_sut->registerStylesAndScripts();

        $this->assertTrue(true);
    }
}
