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
 * @covers tubepress_theme_impl_listeners_AcceptableValuesListener
 */
class tubepress_test_theme_impl_listeners_AcceptableValuesListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_theme_impl_listeners_AcceptableValuesListener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockThemeRegistry;

    public function onSetup()
    {
        $this->_mockThemeRegistry = $this->mock(tubepress_api_contrib_RegistryInterface::_);
        $this->_sut               = new tubepress_theme_impl_listeners_AcceptableValuesListener($this->_mockThemeRegistry);
    }

    public function testOnAcceptableValues()
    {
        $mockTheme  = $this->mock(tubepress_api_theme_ThemeInterface::_);
        $mockThemes = array($mockTheme);

        $mockTheme->shouldReceive('getName')->once()->andReturn('theme-name');
        $mockTheme->shouldReceive('getTitle')->once()->andReturn('theme title');

        $this->_mockThemeRegistry->shouldReceive('getAll')->once()->andReturn($mockThemes);

        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn(array('aaa' => 'display name'));
        $mockEvent->shouldReceive('setSubject')->once()->with(array(
            'aaa'        => 'display name',
            'theme-name' => 'theme title',
        ));

        $this->_sut->onAcceptableValues($mockEvent);

        $this->assertTrue(true);
    }
}

