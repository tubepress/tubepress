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
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 * @covers tubepress_internal_boot_helper_ContainerSupplier<extended>
 */
class tubepress_test_internal_boot_helper_ContainerSupplierTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_internal_boot_helper_ContainerSupplier
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
        $this->_mockLogger              = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockSettingsFileReader = $this->mock('tubepress_internal_boot_BootSettings');

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_internal_boot_helper_ContainerSupplier(

            $this->_mockLogger,
            $this->_mockSettingsFileReader
        );
    }

    public function onTeardown()
    {
        $this->recursivelyDeleteDirectory(sys_get_temp_dir() . '/tubepress-container-supplier-test');
    }

    public function testGetContainerNoSuchFile()
    {
        $this->_mockSettingsFileReader->shouldReceive('isSystemCacheEnabled')->once()->andReturn(true);
        $this->_mockSettingsFileReader->shouldReceive('getPathToSystemCacheDirectory')->once()->andReturn('abc');

        $this->_completeUncachedTest();
    }

    public function testCacheSuccess()
    {
        $this->_mockSettingsFileReader->shouldReceive('isSystemCacheEnabled')->once()->andReturn(true);

        $mockCacheDir = sys_get_temp_dir() . '/tubepress-container-supplier-test';
        $file = $mockCacheDir . '/TubePress-' . TUBEPRESS_VERSION . '-ServiceContainer.php';
        $success = mkdir($mockCacheDir, 0755, true);
        $this->assertTrue($success);
        $text = $this->_getDumpedEmptyIconicContainerBuilder();
        file_put_contents($file, $text);

        $this->_mockSettingsFileReader->shouldReceive('getPathToSystemCacheDirectory')->once()->andReturn(dirname($mockCacheDir));

        $result = $this->_sut->getServiceContainer();

        $this->_verifyResult($result);
    }

    public function testCacheDisabled()
    {
        $this->_mockSettingsFileReader->shouldReceive('isSystemCacheEnabled')->once()->andReturn(false);

        $this->_completeUncachedTest();
    }

    private function _getDumpedEmptyIconicContainerBuilder()
    {
        return <<<XYZ
<?php

/**
 * TubePressServiceContainer
 *
 * This class has been auto-generated
 * by the Symfony Dependency Injection Component.
 */
class TubePressServiceContainer extends ehough_iconic_Container
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct(new ehough_iconic_parameterbag_ParameterBag(array('classMap' => array('x' => 'b'))));
    }
}

XYZ;

    }

    private function _completeUncachedTest()
    {
        $mockIconicContainer  = $this->mock('ehough_iconic_ContainerInterface');
        $mockUncachedProvider = $this->mock('tubepress_internal_boot_helper_uncached_UncachedContainerSupplier');

        $mockUncachedProvider->shouldReceive('getNewIconicContainer')->once()->andReturn($mockIconicContainer);

        $this->_sut->___setUncachedContainerSupplier($mockUncachedProvider);

        $mockIconicContainer->shouldReceive('set')->once()->with('tubepress_api_ioc_ContainerInterface', ehough_mockery_Mockery::type('tubepress_api_ioc_ContainerInterface'));
        $mockIconicContainer->shouldReceive('set')->once()->with('ehough_iconic_ContainerInterface', $mockIconicContainer);
        $mockIconicContainer->shouldReceive('set')->once()->with('tubepress_internal_logger_BootLogger', $this->_mockLogger);
        $mockIconicContainer->shouldReceive('set')->once()->with('tubepress_api_boot_BootSettingsInterface', $this->_mockSettingsFileReader);

        $result = $this->_sut->getServiceContainer();

        $this->_verifyResult($result);
    }

    private function _verifyResult($container)
    {
        $this->assertInstanceOf('tubepress_api_ioc_ContainerInterface', $container);
    }
}