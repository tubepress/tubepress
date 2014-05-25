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
 * @runTestsInSepar ateProcesses
 * @covers tubepress_impl_boot_helper_ContainerSupplier<extended>
 */
class tubepress_test_impl_boot_helper_ContainerSupplierTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_boot_helper_ContainerSupplier
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

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockClassLoader;


    public function onSetup()
    {
        $this->_mockLogger              = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockClassLoader        = $this->mock('ehough_pulsar_ComposerClassLoader');
        $this->_mockSettingsFileReader = $this->mock('tubepress_impl_boot_BootSettings');

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_impl_boot_helper_ContainerSupplier($this->_mockLogger, $this->_mockSettingsFileReader, $this->_mockClassLoader);
    }

    public function testCacheEnabled()
    {
        $mockContainer = $this->mock('ehough_iconic_ContainerInterface');
        $mockContainer->shouldReceive('set')->once()->with('ehough_iconic_ContainerInterface', $mockContainer);

        $tempFile = tempnam('tubepress', 'test');
        file_put_contents($tempFile, '<?php class TubePressServiceContainer{}');

        $this->_mockSettingsFileReader->shouldReceive('getPathToContainerCacheFile')->once()->andReturn($tempFile);

        $mockUncachedProvider = $this->mock('tubepress_impl_boot_helper_secondary_CachedContainerSupplier');
        $mockUncachedProvider->shouldReceive('getServiceContainer')->once()->andReturn($mockContainer);
        $this->_sut->___setCachedContainerSupplier($mockUncachedProvider);

        $this->_mockSettingsFileReader->shouldReceive('isContainerCacheEnabled')->once()->andReturn(true);

        $result = $this->_sut->getServiceContainer();

        $this->assertInstanceOf('tubepress_impl_ioc_IconicContainer', $result);
    }

    public function testCacheDisabled()
    {
        $mockContainer = $this->mock('ehough_iconic_ContainerInterface');
        $mockContainer->shouldReceive('set')->once()->with('ehough_iconic_ContainerInterface', $mockContainer);

        $mockUncachedProvider = $this->mock('tubepress_impl_boot_helper_secondary_UncachedContainerSupplier');
        $mockUncachedProvider->shouldReceive('getServiceContainer')->once()->andReturn($mockContainer);
        $this->_sut->___setUncachedContainerSupplier($mockUncachedProvider);

        $this->_mockSettingsFileReader->shouldReceive('isContainerCacheEnabled')->once()->andReturn(false);

        $result = $this->_sut->getServiceContainer();

        $this->assertInstanceOf('tubepress_impl_ioc_IconicContainer', $result);
    }
}