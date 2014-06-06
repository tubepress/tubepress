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
class tubepress_core_cache_ioc_CacheExtension implements tubepress_api_ioc_ContainerExtensionInterface
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
     * @since 4.0.0
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        /**
         * Long, guaranteed unique service IDs (since they're anonymous)
         */
        $actualPoolServiceId = 'tubepress_core_cache_ioc_CacheExtension__registerCacheService_actualPoolServiceId';
        $builderServiceId    = 'tubepress_core_cache_ioc_CacheExtension__registerCacheService_builderServiceId';

        /**
         * First register the default cache builder.
         */
        $containerBuilder->register(

            $builderServiceId,
            'tubepress_core_cache_impl_stash_FilesystemCacheBuilder'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('ehough_filesystem_FilesystemInterface'));

        $actualPoolDefinition = new tubepress_impl_ioc_Definition('ehough_stash_interfaces_PoolInterface');
        $actualPoolDefinition->setFactoryService($builderServiceId);
        $actualPoolDefinition->setFactoryMethod('buildCache');
        $containerBuilder->setDefinition($actualPoolServiceId, $actualPoolDefinition);

        $containerBuilder->register(

            'ehough_stash_interfaces_PoolInterface',
            'tubepress_core_cache_impl_stash_PoolDecorator'

        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference($actualPoolServiceId));

        $containerBuilder->register(

            'tubepress_core_cache_impl_listeners_http_ApiCacheListener',
            'tubepress_core_cache_impl_listeners_http_ApiCacheListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('ehough_stash_interfaces_PoolInterface'))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_http_api_Constants::EVENT_HTTP_REQUEST,
            'method'   => 'onRequest',
            'priority' => 10000
        ))->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_http_api_Constants::EVENT_HTTP_RESPONSE,
            'method'   => 'onResponse',
            'priority' => 10000
        ));

        $containerBuilder->setParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_cache', array(

            'defaultValues' => array(

                tubepress_core_cache_api_Constants::CLEANING_FACTOR  => 20,
                tubepress_core_cache_api_Constants::DIRECTORY        => null,
                tubepress_core_cache_api_Constants::ENABLED          => true,
                tubepress_core_cache_api_Constants::LIFETIME_SECONDS => 3600,
            ),

            'labels' => array(

                tubepress_core_cache_api_Constants::CLEANING_FACTOR  => 'Cache cleaning factor',        //>(translatable)<
                tubepress_core_cache_api_Constants::DIRECTORY        => 'Cache directory',           //>(translatable)<
                tubepress_core_cache_api_Constants::ENABLED          => 'Enable API cache',                //>(translatable)<
                tubepress_core_cache_api_Constants::LIFETIME_SECONDS => 'Cache expiration time (seconds)', //>(translatable)<
            ),

            'descriptions' => array(

                tubepress_core_cache_api_Constants::CLEANING_FACTOR  => 'If you enter X, the entire cache will be cleaned every 1/X cache writes. Enter 0 to disable cache cleaning.', //>(translatable)<
                tubepress_core_cache_api_Constants::DIRECTORY        => 'Leave blank to attempt to use your system\'s temp directory. Otherwise enter the absolute path of a writeable directory.', //>(translatable)<
                tubepress_core_cache_api_Constants::ENABLED          => 'Store API responses in a cache file to significantly reduce load times for your galleries at the slight expense of freshness.', //>(translatable)<
                tubepress_core_cache_api_Constants::LIFETIME_SECONDS => 'Cache entries will be considered stale after the specified number of seconds. Default is 3600 (one hour).',   //>(translatable)<
            )
        ));

        $containerBuilder->setParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_VALIDATION . '_cache', array(

            'priority' => 3000,
            'map' => array(
                'positiveInteger' => array(
                    tubepress_core_cache_api_Constants::LIFETIME_SECONDS,
                ),
                'nonNegativeInteger' => array(
                    tubepress_core_cache_api_Constants::CLEANING_FACTOR,
                )
            )
        ));
    }
}