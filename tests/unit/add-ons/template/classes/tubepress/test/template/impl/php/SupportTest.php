<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_template_impl_php_Support<extended>
 */
class tubepress_test_app_impl_template_php_SupportTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_template_impl_php_Support
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThemeTemplateLocator;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplateReference;

    public function onSetup()
    {
        $this->_mockThemeTemplateLocator = $this->mock('tubepress_template_impl_ThemeTemplateLocator');
        $this->_mockTemplateReference    = $this->mock('ehough_templating_TemplateReferenceInterface');

        $this->_sut = new tubepress_template_impl_php_Support($this->_mockThemeTemplateLocator);
    }

    public function testIsFresh()
    {
        $this->_mockTemplateReference->shouldReceive('getLogicalName')->once()->andReturn('abc');
        $this->_mockThemeTemplateLocator->shouldReceive('isFresh')->once()->with('abc', 44)->andReturn(true);
        $actual = $this->_sut->isFresh($this->_mockTemplateReference, 44);
        $this->assertTrue($actual);
    }

    public function testLoadExists()
    {
        $this->_mockTemplateReference->shouldReceive('getLogicalName')->twice()->andReturn('abc');
        $this->_mockThemeTemplateLocator->shouldReceive('exists')->once()->with('abc')->andReturn(true);
        $this->_mockThemeTemplateLocator->shouldReceive('getAbsolutePath')->once()->with('abc')->andReturn('hello');
        $actual = $this->_sut->load($this->_mockTemplateReference);
        $this->assertInstanceOf('ehough_templating_storage_FileStorage', $actual);
        $this->assertEquals('hello', "$actual");
    }

    public function testLoadNotExist()
    {
        $this->_mockTemplateReference->shouldReceive('getLogicalName')->once()->andReturn('abc');
        $this->_mockThemeTemplateLocator->shouldReceive('exists')->once()->with('abc')->andReturn(false);
        $actual = $this->_sut->load($this->_mockTemplateReference);
        $this->assertFalse($actual);
    }

    public function testParseName()
    {
        $actual = $this->_sut->parse('name');

        $this->assertEquals('name.tpl.php', $actual);
    }
}