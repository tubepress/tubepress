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
 * @covers tubepress_impl_boot_PrimaryBootstrapper<extended>
 */
class tubepress_test_impl_boot_PrimaryBootstrapperTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_boot_PrimaryBootstrapper
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockBootHelperSettingsFileReader;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainerSupplier;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockClassLoader;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemporaryLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockCachedContainer;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_boot_PrimaryBootstrapper();

        $this->_mockBootHelperSettingsFileReader = $this->mock(tubepress_api_boot_BootSettingsInterface::_);
        $this->_mockContainerSupplier            = $this->mock('tubepress_impl_boot_helper_ContainerSupplier');
        $this->_mockClassLoader                  = $this->mock('ehough_pulsar_ComposerClassLoader');
        $this->_mockTemporaryLogger              = $this->mock('tubepress_impl_log_BootLogger');
        $this->_mockCachedContainer              = $this->mock('tubepress_api_ioc_ContainerInterface');

        $this->_sut->___setContainerSupplier($this->_mockContainerSupplier);
        $this->_sut->___setSettingsFileReader($this->_mockBootHelperSettingsFileReader);
        $this->_sut->___setClassLoader($this->_mockClassLoader);
        $this->_sut->___setTemporaryLogger($this->_mockTemporaryLogger);

        $_GET['tubepress_debug'] = 'true';

        $this->_mockClassLoader->shouldReceive('register')->once();
        $this->_mockTemporaryLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
    }

    public function testBootNoErrorClEnabled()
    {
        $this->_mockContainerSupplier->shouldReceive('getServiceContainer')->once()->andReturn($this->_mockCachedContainer);
        $this->_mockCachedContainer->shouldReceive('set')->once()->with(tubepress_api_boot_BootSettingsInterface::_, $this->_mockBootHelperSettingsFileReader);
        $this->_mockCachedContainer->shouldReceive('set')->once()->with('tubepress_impl_log_BootLogger', $this->_mockTemporaryLogger);
        $this->_mockCachedContainer->shouldReceive('getParameter')->once()->with('classMap')->andReturn(array('foo'));
        $this->_mockBootHelperSettingsFileReader->shouldReceive('isClassLoaderEnabled')->twice()->andReturn(true);

        //addToClassMap() is final :(
        //$this->_mockClassLoader->shouldReceive('addToClassMap')->once()->with(array('foo'));

        $this->_mockTemporaryLogger->shouldReceive('debug')->atLeast(1);
        $result = $this->_sut->getServiceContainer();

        $this->assertSame($this->_mockCachedContainer, $result);

        $result = $this->_sut->getServiceContainer();

        $this->assertSame($this->_mockCachedContainer, $result);
    }
}