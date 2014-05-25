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
 * @covers tubepress_core_impl_cache_CacheExtension
 */
class tubepress_test_core_impl_cache_CacheExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{
    /**
     * @return tubepress_api_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_core_impl_cache_CacheExtension();
    }

    protected function prepareForLoad()
    {
        /**
         * Long, guaranteed unique service IDs (since they're anonymous)
         */
        $actualPoolServiceId = 'tubepress_core_impl_cache_CacheExtension__registerCacheService_actualPoolServiceId';
        $builderServiceId    = 'tubepress_core_impl_cache_CacheExtension__registerCacheService_builderServiceId';

        /**
         * First register the default cache builder.
         */
        $this->expectRegistration(

            $builderServiceId,
            'tubepress_core_impl_cache_stash_FilesystemCacheBuilder'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference('ehough_filesystem_FilesystemInterface'));

        $actualPoolDefinition = new tubepress_impl_ioc_Definition('ehough_stash_interfaces_PoolInterface');
        $actualPoolDefinition->setFactoryService($builderServiceId);
        $actualPoolDefinition->setFactoryMethod('buildCache');
        $this->expectDefinition($actualPoolServiceId, $actualPoolDefinition);

        $this->expectRegistration(

            'ehough_stash_interfaces_PoolInterface',
            'tubepress_core_impl_cache_stash_PoolDecorator'

        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference($actualPoolServiceId));
    }

    protected function getExpectedServiceContructionMap()
    {
        return array(

            'ehough_stash_interfaces_PoolInterface' => 'tubepress_core_impl_cache_stash_PoolDecorator'
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        $context = $this->mock(tubepress_core_api_options_ContextInterface::_);

        $context->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::CACHE_DIR)->andReturn(sys_get_temp_dir());

        return array(

            tubepress_core_api_options_ContextInterface::_ => $context,
            'ehough_filesystem_FilesystemInterface'        => 'ehough_filesystem_FilesystemInterface'
        );
    }
}