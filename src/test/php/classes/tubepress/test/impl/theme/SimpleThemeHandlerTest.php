<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_test_impl_theme_SimpleThemeHandlerTest extends tubepress_test_TubePressUnitTest
{
    private $_sut;

    private $_mockTemplateBuilder;

    private $_mockContext;

    private $_mockEnvironmentDetector;

    public function onSetup()
    {
        $this->_mockTemplateBuilder     = $this->createMockSingletonService('ehough_contemplate_api_TemplateBuilder');
        $this->_mockContext             = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockEnvironmentDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);

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
        $template = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');

        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::THEME)->andReturn('something');
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn('user-content-dir');
        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with(TUBEPRESS_ROOT . '/src/main/resources/default-themes/default/foo.txt')->andReturn(null);

        $result = $this->_sut->getTemplateInstance('foo.txt', TUBEPRESS_ROOT . '/src/main/resources/default-themes/default');

        $this->assertNull($result);
    }
}