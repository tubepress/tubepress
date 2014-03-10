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
 * @covers tubepress_addons_core_impl_listeners_options_LegacyThemeListener
 */
class tubepress_test_addons_core_impl_listeners_options_LegacyThemeListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_listeners_options_LegacyThemeListener
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

    public function onSetup()
    {
        $this->_mockEvent = ehough_mockery_Mockery::mock('tubepress_api_event_EventInterface');
        $this->_mockThemeHandler = $this->createMockSingletonService(tubepress_spi_theme_ThemeHandlerInterface::_);

        $this->_sut = new tubepress_addons_core_impl_listeners_options_LegacyThemeListener();
    }

    public function testOtherTheme()
    {
        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn('vimeo3');

        $this->_mockThemeHandler->shouldReceive('getMapOfAllThemeNamesToTitles')->once()->andReturn(array('unknown/legacy-vimeo2' => 'something'));

        $this->_sut->onPreValidationSet($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testOtherLegacyTheme()
    {
        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn('vimeo2');
        $this->_mockEvent->shouldReceive('setSubject')->once()->with('unknown/legacy-vimeo2');
        $this->_mockThemeHandler->shouldReceive('getMapOfAllThemeNamesToTitles')->once()->andReturn(array('unknown/legacy-vimeo2' => 'something'));

        $this->_sut->onPreValidationSet($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testTubePressLegacyTheme()
    {
        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn('vimeo');

        $this->_mockEvent->shouldReceive('setSubject')->once()->with('tubepress/legacy-vimeo');

        $this->_sut->onPreValidationSet($this->_mockEvent);

        $this->assertTrue(true);
    }
}