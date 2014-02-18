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
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class tubepress_test_impl_bootstrap_PrimaryBootstrapperTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_boot_PrimaryBootstrapper
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStorageManager;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockSecondaryBootstrapper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockBootHelperSettingsFileReader;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainer;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockClassLoader;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_boot_PrimaryBootstrapper();

        $this->_mockEnvironmentDetector          = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockStorageManager               = $this->createMockSingletonService(tubepress_spi_options_StorageManager::_);
        $this->_mockExecutionContext             = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockHttpRequestParameterService  = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockBootHelperSettingsFileReader = $this->createMockSingletonService(tubepress_spi_boot_SettingsFileReaderInterface::_);
        $this->_mockSecondaryBootstrapper        = ehough_mockery_Mockery::mock(tubepress_spi_boot_secondary_SecondaryBootstrapperInterface::_);
        $this->_mockContainer                    = ehough_mockery_Mockery::mock('ehough_iconic_ContainerInterface');
        $this->_mockClassLoader                  = ehough_mockery_Mockery::mock('ehough_pulsar_ComposerClassLoader');

        $this->_sut->___setSecondaryBootstrapper($this->_mockSecondaryBootstrapper);
        $this->_sut->___setSettingsFileReader($this->_mockBootHelperSettingsFileReader);
        $this->_sut->___setClassLoader($this->_mockClassLoader);

        $_GET['tubepress_debug'] = 'true';

        $this->_mockClassLoader->shouldReceive('register')->once();
    }

    public function onTearDown()
    {
        $nullHandler = new ehough_epilog_handler_NullHandler();

        ehough_epilog_LoggerFactory::setHandlerStack(array($nullHandler));

        unset($_GET['tubepress_debug']);
    }

    public function testCachedBoot()
    {
        $this->_mockBootHelperSettingsFileReader->shouldReceive('isClassLoaderEnabled')->once()->andReturn(true);
        $this->_mockSecondaryBootstrapper->shouldReceive('getServiceContainer')->once()->andReturn($this->_mockContainer);
        $this->_mockContainer->shouldReceive('getParameter')->once()->with('classMap')->andReturn(array('s'));
        $this->_mockContainer->shouldReceive('set')->once()->with('sys.settingsFileReader', $this->_mockBootHelperSettingsFileReader);

        $mockExecutionContext = ehough_mockery_Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::DEBUG_ON)->andReturn(true);
        $this->_mockContainer->shouldReceive('get')->once()->with(tubepress_spi_context_ExecutionContext::_)->andReturn($mockExecutionContext);

        $mockHttpRequestParameterService = ehough_mockery_Mockery::mock(tubepress_spi_http_HttpRequestParameterService::_);
        $mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubepress_debug')->andReturn(true);
        $mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('tubepress_debug')->andReturn('true');
        $this->_mockContainer->shouldReceive('get')->once()->with(tubepress_spi_http_HttpRequestParameterService::_)->andReturn($mockHttpRequestParameterService);

        $this->_sut->boot();

        $this->assertTrue(true);
    }

    public function testBootError()
    {
        $this->expectOutputRegex('~^\[201[4-9]-[0-1][0-9]-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}\] \[ERROR\] TubePress Bootstrapper: Caught exception while booting: whoa nelly <br />$~');

        $this->setExpectedException('RuntimeException', 'whoa nelly');

        $this->_mockSecondaryBootstrapper->shouldReceive('getServiceContainer')->once()->andThrow(new RuntimeException('whoa nelly'));

        $this->_sut->boot();

        $this->assertTrue(true);
    }
}