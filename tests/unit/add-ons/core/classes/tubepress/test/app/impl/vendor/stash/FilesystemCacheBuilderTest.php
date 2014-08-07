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
 * @covers tubepress_app_impl_vendor_stash_FilesystemCacheBuilder
 */
class tubepress_test_app_vendor_impl_stash_FilesystemCacheBuilderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_vendor_stash_FilesystemCacheBuilder
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockBootSettings;


    public function onSetup()
    {
        $this->_mockBootSettings = $this->mock(tubepress_platform_api_boot_BootSettingsInterface::_);
        $this->_mockContext      = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_sut              = new tubepress_app_impl_vendor_stash_FilesystemCacheBuilder(

            $this->_mockContext,
            $this->_mockBootSettings
        );
    }

    public function testBuildCache()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::CACHE_DIRECTORY)->andReturn('/abc');
        $this->_mockBootSettings->shouldReceive('getPathToSystemCacheDirectory')->once()->andReturn(sys_get_temp_dir());

        $result = $this->_sut->buildFilesystemDriver();

        $this->assertInstanceOf('ehough_stash_interfaces_DriverInterface', $result);
    }
}