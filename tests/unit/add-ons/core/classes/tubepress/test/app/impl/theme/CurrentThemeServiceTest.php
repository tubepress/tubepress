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
 * @covers tubepress_app_impl_theme_CurrentThemeService<extended>
 */
class tubepress_test_app_impl_theme_ThemeTemplateLocatorTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_theme_CurrentThemeService
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThemeRegistry;

    public function onSetup()
    {
        $this->_mockContext             = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockThemeRegistry       = $this->mock(tubepress_platform_api_contrib_RegistryInterface::_);

        $this->_sut = new tubepress_app_impl_theme_CurrentThemeService(
            $this->_mockContext,
            $this->_mockThemeRegistry,
            'tubepress/default',
            'option-name'
        );
    }

    /**
     * @dataProvider getData
     */
    public function testGetCurrentTheme($storedValue, $expectedIndex)
    {
        $mockTheme1 = $this->mock(tubepress_app_api_theme_ThemeInterface::_);
        $mockTheme2 = $this->mock(tubepress_app_api_theme_ThemeInterface::_);
        $mockTheme3 = $this->mock(tubepress_app_api_theme_ThemeInterface::_);
        $mockTheme4 = $this->mock(tubepress_app_api_theme_ThemeInterface::_);

        $mockTheme1->shouldReceive('getName')->once()->andReturn('theme1');
        $mockTheme2->shouldReceive('getName')->once()->andReturn('tubepress/default');
        $mockTheme3->shouldReceive('getName')->once()->andReturn('tubepress/legacy-foobar');
        $mockTheme4->shouldReceive('getName')->once()->andReturn('unknown/legacy-hiya');

        $mockThemes = array($mockTheme1, $mockTheme2, $mockTheme3, $mockTheme4);

        $this->_mockContext->shouldReceive('get')->once()->with('option-name')->andReturn($storedValue);
        $this->_mockThemeRegistry->shouldReceive('getAll')->once()->andReturn($mockThemes);

        $actual = $this->_sut->getCurrentTheme();
        $this->assertSame($mockThemes[$expectedIndex], $actual);
    }

    public function getData()
    {
        return array(

            array('template-name', 1),
            array('theme1', 0),
            array('', 1),
            array('foobar', 2),
            array('hiya', 3)
        );
    }
}