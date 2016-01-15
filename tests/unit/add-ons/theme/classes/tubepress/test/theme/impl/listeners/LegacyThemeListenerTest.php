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
 * @covers tubepress_theme_impl_listeners_LegacyThemeListener
 */
class tubepress_test_theme_impl_listeners_LegacyThemeListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_theme_impl_listeners_LegacyThemeListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThemeRegistry;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    public function onSetup()
    {
        $this->_mockEvent         = $this->mock('tubepress_api_event_EventInterface');
        $this->_mockThemeRegistry = $this->mock(tubepress_api_contrib_RegistryInterface::_);
        $this->_mockLogger        = $this->mock(tubepress_api_log_LoggerInterface::_);

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_theme_impl_listeners_LegacyThemeListener(

            $this->_mockLogger,
            $this->_mockThemeRegistry
        );
    }

    public function testOtherTheme()
    {
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn('vimeo3');

        $mockTheme = $this->mock(tubepress_api_theme_ThemeInterface::_);
        $mockTheme->shouldReceive('getName')->once()->andReturn('unknown/legacy-vimeo2');
        $mockThemes = array($mockTheme);
        $this->_mockThemeRegistry->shouldReceive('getAll')->once()->andReturn($mockThemes);

        $this->_sut->onPreValidationSet($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testOtherLegacyTheme()
    {
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn('vimeo2');
        $this->_mockEvent->shouldReceive('setArgument')->once()->with('optionValue', 'unknown/legacy-vimeo2');

        $mockTheme = $this->mock(tubepress_api_theme_ThemeInterface::_);
        $mockTheme->shouldReceive('getName')->once()->andReturn('unknown/legacy-vimeo2');
        $mockThemes = array($mockTheme);
        $this->_mockThemeRegistry->shouldReceive('getAll')->once()->andReturn($mockThemes);

        $this->_sut->onPreValidationSet($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testTubePressLegacyTheme()
    {
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn('vimeo');

        $this->_mockEvent->shouldReceive('setArgument')->once()->with('optionValue', 'tubepress/legacy-vimeo');

        $this->_sut->onPreValidationSet($this->_mockEvent);

        $this->assertTrue(true);
    }
}