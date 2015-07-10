<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_platform_impl_boot_PrimaryBootstrapper<extended>
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class tubepress_test_impl_boot_PrimaryBootstrapperTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_platform_impl_boot_PrimaryBootstrapper
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_bootSettings;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainerSupplier;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockBootLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockServiceContainer;

    public function onSetup()
    {
        $this->_sut = new tubepress_platform_impl_boot_PrimaryBootstrapper();

        $this->_bootSettings = $this->mock(tubepress_platform_api_boot_BootSettingsInterface::_);
        $this->_mockContainerSupplier            = $this->mock('tubepress_platform_impl_boot_helper_ContainerSupplier');
        $this->_mockBootLogger                   = $this->mock('tubepress_internal_logger_BootLogger');
        $this->_mockServiceContainer             = $this->mock('tubepress_platform_api_ioc_ContainerInterface');

        $this->_sut->___setContainerSupplier($this->_mockContainerSupplier);
        $this->_sut->___setSettingsFileReader($this->_bootSettings);
        $this->_sut->___setTemporaryLogger($this->_mockBootLogger);

        $_GET['tubepress_debug'] = 'true';

        $this->_mockBootLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $this->_mockBootLogger->shouldReceive('debug')->atLeast(1);
    }

    public function testBoot()
    {
        $fakeCacheDir = sys_get_temp_dir() . '/foo/bar/hello';
        $result       = mkdir($fakeCacheDir, 0755, true);
        $this->assertTrue($result);

        $result = touch($fakeCacheDir . '/hi.txt');
        $this->assertTrue($result);

        $this->_mockContainerSupplier->shouldReceive('getServiceContainer')->once()->andReturn($this->_mockServiceContainer);

        $mockLogger = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $mockLogger->shouldReceive('debug')->atLeast(1);
        $mockLogger->shouldReceive('onBootComplete')->once();
        $this->_mockServiceContainer->shouldReceive('get')->once()->with(tubepress_platform_api_log_LoggerInterface::_)->andReturn($mockLogger);
        $this->_mockServiceContainer->shouldReceive('hasParameter')->once()->with(tubepress_platform_impl_boot_PrimaryBootstrapper::CONTAINER_PARAM_BOOT_ARTIFACTS)->andReturn(true);
        $this->_mockServiceContainer->shouldReceive('getParameter')->once()->with(tubepress_platform_impl_boot_PrimaryBootstrapper::CONTAINER_PARAM_BOOT_ARTIFACTS)->andReturn(array('a' => 'b'));

        $this->_bootSettings->shouldReceive('isClassLoaderEnabled')->once()->andReturn(true);
        $this->_bootSettings->shouldReceive('shouldClearCache')->once()->andReturn(true);
        $this->_bootSettings->shouldReceive('getPathToSystemCacheDirectory')->once()->andReturn($fakeCacheDir);
        $this->_mockBootLogger->shouldReceive('flushTo')->once()->with($mockLogger);
        $this->_mockBootLogger->shouldReceive('onBootComplete')->once();

        $result = $this->_sut->getServiceContainer();

        $this->assertFalse(is_dir($fakeCacheDir));

        $this->assertSame($this->_mockServiceContainer, $result);

        $result = $this->_sut->getServiceContainer();

        $this->assertSame($this->_mockServiceContainer, $result);
    }

    public function testBootException()
    {
        $this->setExpectedException('RuntimeException', 'hellooo!');

        $this->_mockContainerSupplier->shouldReceive('getServiceContainer')->once()->andThrow(new RuntimeException('hellooo!'));

        $this->_mockBootLogger->shouldReceive('handleBootException')->once();
        $this->_mockBootLogger->shouldReceive('onBootComplete')->once();
        $this->_mockBootLogger->shouldReceive('error')->atLeast(1)->with(ehough_mockery_Mockery::on(array($this, '__callbackTestBootException')));

        $this->_bootSettings->shouldReceive('shouldClearCache')->once()->andReturn(false);

        $this->_sut->getServiceContainer();
    }

    public function __callbackTestBootException($arg)
    {
        $stringUtils = new tubepress_util_impl_StringUtils();
        return $stringUtils->startsWith($arg, '<tt>');
    }
}