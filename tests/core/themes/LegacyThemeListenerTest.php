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
 * @covers tubepress_core_theme_impl_listeners_options_LegacyThemeListener
 */
class tubepress_test_core_impl_listeners_options_LegacyThemeListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_theme_impl_listeners_options_LegacyThemeListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThemeHandler;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    public function onSetup()
    {
        $this->_mockEvent        = $this->mock('tubepress_core_event_api_EventInterface');
        $this->_mockThemeHandler = $this->mock(tubepress_core_theme_api_ThemeLibraryInterface::_);
        $this->_mockLogger       = $this->mock(tubepress_api_log_LoggerInterface::_);

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_core_theme_impl_listeners_options_LegacyThemeListener(

            $this->_mockLogger,
            $this->_mockThemeHandler
        );
    }

    public function testOtherTheme()
    {
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn('vimeo3');

        $this->_mockThemeHandler->shouldReceive('getMapOfAllThemeNamesToTitles')->once()->andReturn(array('unknown/legacy-vimeo2' => 'something'));

        $this->_sut->onPreValidationSet($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testOtherLegacyTheme()
    {
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn('vimeo2');
        $this->_mockEvent->shouldReceive('setArgument')->once()->with('optionValue', 'unknown/legacy-vimeo2');
        $this->_mockThemeHandler->shouldReceive('getMapOfAllThemeNamesToTitles')->once()->andReturn(array('unknown/legacy-vimeo2' => 'something'));

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