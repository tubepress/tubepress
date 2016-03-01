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
 * @covers tubepress_internal_boot_PrimaryBootstrapper<extended>
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class tubepress_test_internal_boot_PrimaryBootstrapperTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_internal_boot_PrimaryBootstrapper
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_bootSettings;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockContainerSupplier;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockBootLogger;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockServiceContainer;

    public function onSetup()
    {
        $this->_sut                   = new tubepress_internal_boot_PrimaryBootstrapper();
        $this->_bootSettings          = $this->mock(tubepress_api_boot_BootSettingsInterface::_);
        $this->_mockContainerSupplier = $this->mock('tubepress_internal_boot_helper_ContainerSupplier');
        $this->_mockBootLogger        = $this->mock('tubepress_internal_logger_BootLogger');
        $this->_mockServiceContainer  = $this->mock('tubepress_api_ioc_ContainerInterface');

        $this->_sut->___setContainerSupplier($this->_mockContainerSupplier);
        $this->_sut->___setSettingsFileReader($this->_bootSettings);
        $this->_sut->___setTemporaryLogger($this->_mockBootLogger);

        $_GET['tubepress_debug'] = 'true';

        $this->_mockBootLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $this->_mockBootLogger->shouldReceive('debug')->atLeast(1);
    }

    public function testBoot()
    {
        $fs = new \Symfony\Component\Filesystem\Filesystem();

        $fakeCacheDir = sys_get_temp_dir() . '/foo/bar/hello';

        $fs->remove($fakeCacheDir);

        $result = mkdir($fakeCacheDir, 0755, true);

        $this->assertTrue($result);

        $result = touch($fakeCacheDir . '/hi.txt');
        $this->assertTrue($result);

        $this->_mockContainerSupplier->shouldReceive('getServiceContainer')->once()->andReturn($this->_mockServiceContainer);

        $mockLogger = $this->mock(tubepress_api_log_LoggerInterface::_);
        $mockLogger->shouldReceive('debug')->atLeast(1);
        $mockLogger->shouldReceive('onBootComplete')->once();

        $this->_mockServiceContainer->shouldReceive('get')->once()->with(tubepress_api_log_LoggerInterface::_)->andReturn($mockLogger);

        $this->_bootSettings->shouldReceive('isClassLoaderEnabled')->once()->andReturn(true);
        $this->_bootSettings->shouldReceive('shouldClearCache')->once()->andReturn(true);
        $this->_bootSettings->shouldReceive('getPathToSystemCacheDirectory')->twice()->andReturn($fakeCacheDir);

        $this->_mockBootLogger->shouldReceive('flushTo')->once()->with($mockLogger);
        $this->_mockBootLogger->shouldReceive('onBootComplete')->once();

        $result = $this->_sut->getServiceContainer();

        $this->assertTrue(is_dir($fakeCacheDir));

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
        $this->_mockBootLogger->shouldReceive('error')->atLeast(1)->with(Mockery::on(array($this, '__callbackTestBootException')));

        $this->_bootSettings->shouldReceive('shouldClearCache')->once()->andReturn(false);

        $this->_sut->getServiceContainer();
    }

    public function __callbackTestBootException($arg)
    {
        $stringUtils = new tubepress_util_impl_StringUtils();
        return $stringUtils->startsWith($arg, '<code>');
    }
}