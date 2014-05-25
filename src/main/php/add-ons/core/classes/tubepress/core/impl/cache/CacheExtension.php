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
class tubepress_core_impl_cache_CacheExtension implements tubepress_api_ioc_ContainerExtensionInterface
{

    /**
     * Called during construction of the TubePress service container. If an add-on intends to add
     * services to the container, it should do so here. The incoming `tubepress_api_ioc_ContainerBuilderInterface`
     * will be completely empty, and after this method is executed will be merged into the primary service container.
     *
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder An empty `tubepress_api_ioc_ContainerBuilderInterface` instance.
     *
     * @return void
     *
     * @api
     * @since 3.2.0
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        /**
         * Long, guaranteed unique service IDs (since they're anonymous)
         */
        $actualPoolServiceId = 'tubepress_core_impl_cache_CacheExtension__registerCacheService_actualPoolServiceId';
        $builderServiceId    = 'tubepress_core_impl_cache_CacheExtension__registerCacheService_builderServiceId';

        /**
         * First register the default cache builder.
         */
        $containerBuilder->register(

            $builderServiceId,
            'tubepress_core_impl_cache_stash_FilesystemCacheBuilder'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('ehough_filesystem_FilesystemInterface'));

        $actualPoolDefinition = new tubepress_impl_ioc_Definition('ehough_stash_interfaces_PoolInterface');
        $actualPoolDefinition->setFactoryService($builderServiceId);
        $actualPoolDefinition->setFactoryMethod('buildCache');
        $containerBuilder->setDefinition($actualPoolServiceId, $actualPoolDefinition);

        $containerBuilder->register(

            'ehough_stash_interfaces_PoolInterface',
            'tubepress_core_impl_cache_stash_PoolDecorator'

        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference($actualPoolServiceId));
    }
}