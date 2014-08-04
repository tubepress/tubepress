<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_app_impl_template_ThemeTemplateLocator<extended>
 */
class tubepress_test_app_impl_template_ThemeTemplateLocatorTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_template_ThemeTemplateLocator
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThemeRegistry;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockCurrentThemeService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockChildTheme;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockParentTheme;

    public function onSetup()
    {
        $this->_mockLogger              = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $this->_mockContext             = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockThemeRegistry       = $this->mock(tubepress_platform_api_contrib_RegistryInterface::_);
        $this->_mockCurrentThemeService = $this->mock('tubepress_app_impl_theme_CurrentThemeService');
        $this->_mockChildTheme          = $this->mock(tubepress_app_api_theme_ThemeInterface::_);
        $this->_mockParentTheme         = $this->mock(tubepress_app_api_theme_ThemeInterface::_);

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_app_impl_template_ThemeTemplateLocator(

            $this->_mockLogger,
            $this->_mockContext,
            $this->_mockThemeRegistry,
            $this->_mockCurrentThemeService
        );
    }

    /**
     * @dataProvider trueAndFalse
     */
    public function testCacheKey($fresh)
    {
        $this->_mockCurrentThemeService->shouldReceive('getCurrentTheme')->once()->andReturn($this->_mockChildTheme);
        $this->_mockChildTheme->shouldReceive('hasTemplateSource')->once()->with('template-name')->andReturn($fresh);
        $this->_mockChildTheme->shouldReceive('getName')->atLeast(1)->andReturn('abc');

        if ($fresh) {

            $this->_mockChildTheme->shouldReceive('getTemplateCacheKey')->once()->andReturn($fresh);
            $expected = $fresh;

        } else {

            $this->_mockChildTheme->shouldReceive('getParentThemeName')->once()->andReturnNull();
            $this->setExpectedException('InvalidArgumentException');
        }

        $actual = $this->_sut->getCacheKey('template-name');
        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider trueAndFalse
     */
    public function testIsFresh($fresh)
    {
        $this->_mockCurrentThemeService->shouldReceive('getCurrentTheme')->once()->andReturn($this->_mockChildTheme);
        $this->_mockChildTheme->shouldReceive('hasTemplateSource')->once()->with('template-name')->andReturn($fresh);
        $this->_mockChildTheme->shouldReceive('getName')->atLeast(1)->andReturn('abc');

        if ($fresh) {

            $this->_mockChildTheme->shouldReceive('isTemplateSourceFresh')->once()->andReturn($fresh);
            $expected = $fresh;

        } else {

            $this->_mockChildTheme->shouldReceive('getParentThemeName')->once()->andReturnNull();
            $this->setExpectedException('InvalidArgumentException');
        }

        $actual = $this->_sut->isFresh('template-name', PHP_INT_MAX);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider trueAndFalse
     */
    public function testGetTemplateSource($exists)
    {
        $this->_mockCurrentThemeService->shouldReceive('getCurrentTheme')->once()->andReturn($this->_mockChildTheme);
        $this->_mockChildTheme->shouldReceive('hasTemplateSource')->once()->with('template-name')->andReturn($exists);
        $this->_mockChildTheme->shouldReceive('getName')->atLeast(1)->andReturn('abc');

        if ($exists) {

            $this->_mockChildTheme->shouldReceive('getTemplateSource')->once()->andReturn('foobar');
            $expected = 'foobar';

        } else {

            $this->_mockChildTheme->shouldReceive('getParentThemeName')->once()->andReturnNull();
            $expected = null;
        }

        $actual = $this->_sut->getSource('template-name');
        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider trueAndFalse
     */
    public function testExistInParent($exists)
    {
        $this->_mockCurrentThemeService->shouldReceive('getCurrentTheme')->once()->andReturn($this->_mockChildTheme);
        $this->_mockChildTheme->shouldReceive('hasTemplateSource')->once()->with('template-name')->andReturn(false);
        $this->_mockChildTheme->shouldReceive('getParentThemeName')->once()->andReturn('xyz');
        $this->_mockChildTheme->shouldReceive('getName')->once()->andReturn('abc');
        $this->_mockThemeRegistry->shouldReceive('getInstanceByName')->with('xyz')->andReturn($this->_mockParentTheme);
        $this->_mockParentTheme->shouldReceive('hasTemplateSource')->once()->with('template-name')->andReturn($exists);

        if (!$exists) {

            $this->_mockParentTheme->shouldReceive('getParentThemeName')->once()->andReturnNull();
        } else {
            $this->_mockParentTheme->shouldReceive('getName')->times(1)->andReturn('xyz');
        }

        $this->assertTrue($this->_sut->exists('template-name') === $exists);
        $this->assertTrue($this->_sut->exists('template-name') === $exists);
    }

    public function trueAndFalse()
    {
        return array(

            array(true),
            array(false)
        );
    }

}