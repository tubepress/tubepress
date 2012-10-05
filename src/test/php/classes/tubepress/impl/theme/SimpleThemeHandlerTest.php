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

class org_tubepress_impl_theme_SimpleThemeHandlerTest extends TubePressUnitTest
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

        tubepress_impl_patterns_ioc_KernelServiceLocator::setTemplateBuilder($this->_mockTemplateBuilder);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockContext);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEnvironmentDetector($this->_mockEnvironmentDetector);

        $this->_sut = new tubepress_impl_theme_SimpleThemeHandler();
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
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn('user-content-dir');
        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with(TUBEPRESS_ROOT . '/src/main/resources/default-themes/default/foo.txt')->andReturn(null);

        $result = $this->_sut->getTemplateInstance('foo.txt', TUBEPRESS_ROOT . '/src/main/resources/default-themes/default');

        $this->assertNull($result);
    }
}