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
 * @covers tubepress_impl_boot_helper_secondary_ClassLoaderPrimer<extended>
 */
class tubepress_test_impl_boot_helper_secondary_ClassLoaderPrimerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_boot_helper_secondary_ClassLoaderPrimer
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockSettingsFileReader;

    public function onSetup()
    {
        $this->_mockLogger             = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);
        $this->_sut                    = new tubepress_impl_boot_helper_secondary_ClassLoaderPrimer($this->_mockLogger);
        $this->_mockSettingsFileReader = $this->mock('tubepress_impl_boot_BootSettings');
        $this->_mockClassLoader        = $this->mock('ehough_pulsar_ComposerClassLoader');
    }

    public function testGetPsrRoots()
    {
        $mockAddons = $this->getMockAddons();

        $actual = $this->_sut->getPsr0Roots($mockAddons);

        $this->assertEquals(array('some root' => 'something else', 'prefix' => 'something'), $actual);
    }

    public function testGetPsrFallbacks()
    {
        $mockAddons = $this->getMockAddons();

        $actual = $this->_sut->getPsr0Fallbacks($mockAddons);

        $this->assertEquals(array('something'), $actual);
    }

    public function testGetClassMapFromAddons()
    {
        $mockAddons = $this->getMockAddons();

        $actual = $this->_sut->getClassMapFromAddons($mockAddons);

        $this->assertEquals(array('foo' => 'bar'), $actual);
    }

    private function getMockAddons()
    {
        $mockAddon1 = $this->mock(tubepress_api_addon_AddonInterface::_);
        $mockAddon2 = $this->mock(tubepress_api_addon_AddonInterface::_);
        $mockAddon1->shouldReceive('getName')->andReturn('mock add-on 1');
        $mockAddon2->shouldReceive('getName')->andReturn('mock add-on 2');

        $mockAddon1->shouldReceive('getPsr0ClassPathRoots')->once()->andReturn(array('some root' => 'something else', 'prefix' => 'something'));
        $mockAddon2->shouldReceive('getPsr0ClassPathRoots')->once()->andReturn(array('something'));
        $mockAddon1->shouldReceive('getClassMap')->once()->andReturn(array());
        $mockAddon2->shouldReceive('getClassMap')->once()->andReturn(array('foo' => 'bar'));

        return array($mockAddon1, $mockAddon2);
    }
}