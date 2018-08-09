<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_cache_api_impl_stash_FilesystemCacheBuilder
 */
class tubepress_test_cache_api_impl_stash_FilesystemCacheBuilderTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_cache_api_impl_stash_FilesystemCacheBuilder
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockContext;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockBootSettings;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockLogger;

    public function onSetup()
    {
        $this->_mockBootSettings = $this->mock(tubepress_api_boot_BootSettingsInterface::_);
        $this->_mockContext      = $this->mock(tubepress_api_options_ContextInterface::_);
        $this->_mockLogger       = $this->mock(tubepress_api_log_LoggerInterface::_);

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_cache_api_impl_stash_FilesystemCacheBuilder(

            $this->_mockContext,
            $this->_mockBootSettings,
            $this->_mockLogger
        );
    }

    public function testBuildCache()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::CACHE_DIRECTORY)->andReturn('/abc');
        $this->_mockBootSettings->shouldReceive('getPathToSystemCacheDirectory')->once()->andReturn(sys_get_temp_dir());

        $result = $this->_sut->buildFilesystemDriver();

        $this->assertInstanceOf('Stash\Interfaces\DriverInterface', $result);
    }
}
