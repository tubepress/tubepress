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
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
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

    public function testGetContainerNoSuchFile()
    {
        $this->_mockSettingsFileReader->shouldReceive('isContainerCacheEnabled')->once()->andReturn(true);
        $this->_mockSettingsFileReader->shouldReceive('getPathToContainerCacheFile')->once()->andReturn('abc');

        $this->_completeUncachedTest();
    }

    public function testCacheSuccess()
    {
        $this->_mockSettingsFileReader->shouldReceive('isContainerCacheEnabled')->once()->andReturn(true);

        $tmpFile = tmpfile();
        $metaDatas = stream_get_meta_data($tmpFile);
        $tmpFilename = $metaDatas['uri'];

        fwrite($tmpFile, $this->_getDumpedEmptyIconicContainerBuilder());

        $this->_mockSettingsFileReader->shouldReceive('getPathToContainerCacheFile')->once()->andReturn($tmpFilename);
        $this->_mockSettingsFileReader->shouldReceive('isClassLoaderEnabled')->once()->andReturn(true);

        $result = $this->_sut->getServiceContainer();

        $this->_verifyResult($result);
    }

    public function testCacheDisabled()
    {
        $this->_mockSettingsFileReader->shouldReceive('isContainerCacheEnabled')->once()->andReturn(false);

        $this->_completeUncachedTest();
    }

    private function _verifyResult($container)
    {
        $this->assertInstanceOf('tubepress_api_ioc_ContainerInterface', $container);
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
        $this->_mockSettingsFileReader->shouldReceive('isClassLoaderEnabled')->once()->andReturn(true);

        $mockIconicContainer  = $this->mock('ehough_iconic_ContainerInterface');
        $mockUncachedProvider = $this->mock('tubepress_impl_boot_helper_secondary_UncachedContainerSupplier');

        $mockUncachedProvider->shouldReceive('getNewIconicContainer')->once()->andReturn($mockIconicContainer);

        $this->_sut->___setUncachedContainerSupplier($mockUncachedProvider);

        $mockIconicContainer->shouldReceive('set')->once()->with('tubepress_api_ioc_ContainerInterface', ehough_mockery_Mockery::type('tubepress_api_ioc_ContainerInterface'));
        $mockIconicContainer->shouldReceive('set')->once()->with('ehough_iconic_ContainerInterface', $mockIconicContainer);
        $mockIconicContainer->shouldReceive('set')->once()->with('tubepress_impl_log_BootLogger', $this->_mockLogger);
        $mockIconicContainer->shouldReceive('set')->once()->with('tubepress_api_boot_BootSettingsInterface', $this->_mockSettingsFileReader);
        $mockIconicContainer->shouldReceive('getParameter')->once()->with('classMap')->andReturn(array('x' => 'z'));

        $result = $this->_sut->getServiceContainer();

        $this->_verifyResult($result);
    }

}