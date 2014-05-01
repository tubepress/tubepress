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
    private $_mockBootHelperSettingsFileReader;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockSecondaryBootstrapper;

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
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockCachedContainer;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_boot_PrimaryBootstrapper();

        $this->_mockBootHelperSettingsFileReader = ehough_mockery_Mockery::mock(tubepress_spi_boot_SettingsFileReaderInterface::_);
        $this->_mockSecondaryBootstrapper        = ehough_mockery_Mockery::mock(tubepress_spi_boot_secondary_SecondaryBootstrapperInterface::_);
        $this->_mockClassLoader                  = ehough_mockery_Mockery::mock('ehough_pulsar_ComposerClassLoader');
        $this->_mockTemporaryLogger              = ehough_mockery_Mockery::mock('tubepress_impl_log_MemoryBufferLogger');
        $this->_mockExecutionContext             = ehough_mockery_Mockery::mock(tubepress_api_options_ContextInterface::_);
        $this->_mockCachedContainer              = ehough_mockery_Mockery::mock('tubepress_api_ioc_ContainerInterface');

        $this->_sut->___setSecondaryBootstrapper($this->_mockSecondaryBootstrapper);
        $this->_sut->___setSettingsFileReader($this->_mockBootHelperSettingsFileReader);
        $this->_sut->___setClassLoader($this->_mockClassLoader);
        $this->_sut->___setTemporaryLogger($this->_mockTemporaryLogger);

        $_GET['tubepress_debug'] = 'true';

        $this->_mockClassLoader->shouldReceive('register')->once();
    }

    public function testBoot()
    {
        $this->_mockSecondaryBootstrapper->shouldReceive('getServiceContainer')->once()->with(
            $this->_mockBootHelperSettingsFileReader,
            $this->_mockClassLoader
        )->andReturn($this->_mockCachedContainer);

        $this->_mockBootHelperSettingsFileReader->shouldReceive('isClassLoaderEnabled')->once()->andReturn(true);

        $this->_mockCachedContainer->shouldReceive('getParameter')->once()->with('classMap')->andReturn(array('s'));
        $this->_mockCachedContainer->shouldReceive('set')->once()->with('tubepress.settingsFileReader', $this->_mockBootHelperSettingsFileReader);
        $this->_mockCachedContainer->shouldReceive('get')->once()->with(tubepress_api_options_ContextInterface::_)->andReturn($this->_mockExecutionContext);
        $this->_mockCachedContainer->shouldReceive('set')->once()->with(tubepress_api_log_LoggerInterface::_, ehough_mockery_Mockery::type('tubepress_impl_log_HtmlLogger'));

        $this->_mockTemporaryLogger->shouldReceive('isEnabled')->andReturn(true);
        $this->_mockTemporaryLogger->shouldReceive('flushTo')->once()->with(ehough_mockery_Mockery::type('tubepress_impl_log_HtmlLogger'));
        $this->_mockTemporaryLogger->shouldReceive('disable')->once();
        $this->_mockTemporaryLogger->shouldReceive('debug')->once()->with(ehough_mockery_Mockery::on(function ($arg) {

            return tubepress_impl_util_StringUtils::startsWith($arg, 'Boot completed in ');
        }));

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::DEBUG_ON)->andReturn(true);

        $result = $this->_sut->boot();

        $this->assertSame($this->_mockCachedContainer, $result);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage foobar
     */
    public function testBootError()
    {
        $this->_mockSecondaryBootstrapper->shouldReceive('getServiceContainer')->once()->with(
            $this->_mockBootHelperSettingsFileReader,
            $this->_mockClassLoader
        )->andThrow(new RuntimeException('foobar'));

        $this->_mockTemporaryLogger->shouldReceive('isEnabled')->twice()->andReturn(true);
        $this->_mockTemporaryLogger->shouldReceive('error')->once()->with('Caught exception while booting: foobar');
        $this->_mockTemporaryLogger->shouldReceive('printBuffer')->once();

        $this->_sut->boot();
    }
}