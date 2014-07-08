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
 *
 */
class tubepress_app_vendor_ioc_VendorsExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
{
    /**
     * Called during construction of the TubePress service container. If an add-on intends to add
     * services to the container, it should do so here. The incoming `tubepress_platform_api_ioc_ContainerBuilderInterface`
     * will be completely empty, and after this method is executed will be merged into the primary service container.
     *
     * @param tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder An empty `tubepress_platform_api_ioc_ContainerBuilderInterface` instance.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function load(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'ehough_filesystem_FilesystemInterface',
            'ehough_filesystem_Filesystem'
        );

        $containerBuilder->register(
            'ehough_finder_FinderFactoryInterface',
            'ehough_finder_FinderFactory'
        );

        $containerBuilder->register(

            'tubepress_app_vendor_impl_stash_FilesystemCacheBuilder',
            'tubepress_app_vendor_impl_stash_FilesystemCacheBuilder'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->addArgument(new tubepress_platform_api_ioc_Reference('ehough_filesystem_FilesystemInterface'));

        $containerBuilder->register(

            'ehough_stash_interfaces_PoolInterface',
            'ehough_stash_Pool'
        )->addMethodCall('setDriver', array(new tubepress_platform_api_ioc_Reference('ehough_stash_interfaces_DriverInterface')));

        $containerBuilder->register(
            'ehough_stash_interfaces_DriverInterface',
            'ehough_stash_interfaces_DriverInterface'
        )->setFactoryService('tubepress_app_vendor_impl_stash_FilesystemCacheBuilder')
            ->setFactoryMethod('buildFilesystemDriver');
    }
}