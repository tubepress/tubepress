<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_addons_core_impl_ioc_FilesystemCacheBuilderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_ioc_FilesystemCacheBuilder
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFilesystem;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_core_impl_ioc_FilesystemCacheBuilder();

        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockFilesystem       = $this->createMockSingletonService('ehough_filesystem_FilesystemInterface');
    }

    public function testBuildCustomDir()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_DIR)->andReturn('/tmp');

        $result = $this->_sut->buildCache();

        $this->assertTrue($result instanceof ehough_stash_Pool);
        $this->assertTrue($result->getDriver() instanceof ehough_stash_driver_FileSystem);
    }

    public function testBuildDefaultSettings()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_DIR)->andReturn('');
        $this->_mockFilesystem->shouldReceive('getSystemTempDirectory')->once()->andReturn('/tmp');

        $result = $this->_sut->buildCache();

        $this->assertTrue($result instanceof ehough_stash_Pool);
        $this->assertTrue($result->getDriver() instanceof ehough_stash_driver_FileSystem);
    }
}