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
 * @covers tubepress_app_vendor_ioc_VendorsExtension<extended>
 */
class tubepress_test_app_vendor_ioc_VendorsExtensionTest extends tubepress_test_ioc_AbstractIocContainerExtensionTest
{
    /**
     * @return tubepress_platform_api_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_app_vendor_ioc_VendorsExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            'tubepress_app_vendor_impl_stash_FilesystemCacheBuilder',
            'tubepress_app_vendor_impl_stash_FilesystemCacheBuilder'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference('ehough_filesystem_FilesystemInterface'));

        $this->expectRegistration(

            'ehough_stash_interfaces_PoolInterface',
            'ehough_stash_Pool'
        )->withMethodCall('setDriver', array(new tubepress_platform_api_ioc_Reference('ehough_stash_interfaces_DriverInterface')));

        $this->expectRegistration(
            'ehough_stash_interfaces_DriverInterface',
            'ehough_stash_interfaces_DriverInterface'
        )->withFactoryService('tubepress_app_vendor_impl_stash_FilesystemCacheBuilder')
            ->withFactoryMethod('buildFilesystemDriver');

        $this->expectRegistration(

            'ehough_filesystem_FilesystemInterface',
            'ehough_filesystem_Filesystem'
        );

        $this->expectRegistration(

            'ehough_finder_FinderFactoryInterface',
            'ehough_finder_FinderFactory'
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        $context = $this->mock(tubepress_app_options_api_ContextInterface::_);
        $context->shouldReceive('get')->once()->with(tubepress_app_apicache_api_Constants::OPTION_DIRECTORY)->andReturn(sys_get_temp_dir());

        return array(

            'service_container' => 'tubepress_platform_api_ioc_ContainerInterface',
            tubepress_app_options_api_ContextInterface::_ => $context
        );
    }
}