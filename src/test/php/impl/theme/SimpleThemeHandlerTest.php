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

class org_tubepress_impl_theme_SimpleThemeHandlerTest extends PHPUnit_Framework_TestCase
{
    private $_sut;

    private $_mockTemplateBuilder;

    private $_mockContext;

    private $_mockEnvironmentDetector;

    public function setup()
    {
        $this->_mockTemplateBuilder     = Mockery::mock('ehough_contemplate_api_TemplateBuilder');
        $this->_mockContext             = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        $this->_mockEnvironmentDetector = Mockery::mock(tubepress_spi_environment_EnvironmentDetector::_);

        $this->_sut = new tubepress_impl_theme_SimpleThemeHandler($this->_mockTemplateBuilder,
            $this->_mockContext, $this->_mockEnvironmentDetector);
    }

    public function testCalculateCurrentThemeNameNoCustomTheme()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::THEME)->andReturn('');

        $result = $this->_sut->calculateCurrentThemeName();

        $this->assertEquals('default', $result);
    }

    public function testCalculateCurrentThemeNameCustomTheme()
    {

        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::THEME)->andReturn('foo');

        $result = $this->_sut->calculateCurrentThemeName();
        $this->assertEquals('foo', $result);
    }

    public function testGetTemplateInstance()
    {
        $template = Mockery::mock('ehough_contemplate_api_Template');

        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::THEME)->andReturn('something');
        $this->_mockEnvironmentDetector->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('base-path');
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn('user-content-dir');
        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with('base-path/src/main/resources/default-themes/default/foo.txt')->andReturn(null);
        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with('user-content-dir/themes/something/foo.txt')->andReturn($template);

        $result = $this->_sut->getTemplateInstance('foo.txt');

        $this->assertNull($result);
    }
}