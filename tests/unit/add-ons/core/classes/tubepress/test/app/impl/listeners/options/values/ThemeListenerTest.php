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
 * @covers tubepress_app_impl_listeners_options_values_ThemeListener
 */
class tubepress_test_app_impl_listeners_options_values_ThemeListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_listeners_options_values_ThemeListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThemeRegistry;

    public function onSetup()
    {
        $this->_mockThemeRegistry = $this->mock(tubepress_platform_api_contrib_RegistryInterface::_);
        $this->_sut               = new tubepress_app_impl_listeners_options_values_ThemeListener($this->_mockThemeRegistry);
    }

    public function testOnAcceptableValues()
    {
        $mockTheme  = $this->mock(tubepress_app_api_theme_ThemeInterface::_);
        $mockThemes = array($mockTheme);

        $mockTheme->shouldReceive('getName')->once()->andReturn('theme-name');
        $mockTheme->shouldReceive('getTitle')->once()->andReturn('theme title');

        $this->_mockThemeRegistry->shouldReceive('getAll')->once()->andReturn($mockThemes);

        $mockEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn(array('aaa' => 'display name'));
        $mockEvent->shouldReceive('setSubject')->once()->with(array(
            'aaa'        => 'display name',
            'theme-name' => 'theme title',
        ));

        $this->_sut->onAcceptableValues($mockEvent);

        $this->assertTrue(true);
    }
}

