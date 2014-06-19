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
        $containerBuilder->register(

            'tubepress_core_cache_impl_stash_FilesystemCacheBuilder',
            'tubepress_core_cache_impl_stash_FilesystemCacheBuilder'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('ehough_filesystem_FilesystemInterface'));

        $actualPoolDefinition = new tubepress_impl_ioc_Definition('ehough_stash_interfaces_PoolInterface');
        $actualPoolDefinition->setFactoryService('tubepress_core_cache_impl_stash_FilesystemCacheBuilder');
        $actualPoolDefinition->setFactoryMethod('buildCache');
        $containerBuilder->setDefinition('ehough_stash_interfaces_PoolInterface', $actualPoolDefinition);

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

        $fieldIndex = 0;
        $fieldMap = array(
            'boolean' => array(
                tubepress_core_cache_api_Constants::ENABLED
            ),
            'text' => array(
                tubepress_core_cache_api_Constants::DIRECTORY,
                tubepress_core_cache_api_Constants::LIFETIME_SECONDS,
                tubepress_core_cache_api_Constants::CLEANING_FACTOR
            )
        );
        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {
                $containerBuilder->register(
                    'cache_field_' . $fieldIndex++,
                    'tubepress_core_options_ui_api_FieldInterface'
                )->setFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
                 ->setFactoryMethod('newInstance')
                 ->addArgument($id)
                 ->addArgument($type);
            }
        }

        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; $x++) {
            $fieldReferences[] = new tubepress_api_ioc_Reference('cache_field_' . $x);
        }

        $containerBuilder->register(
            'cache_category',
            'tubepress_core_options_ui_api_ElementInterface'
        )->setFactoryService(tubepress_core_options_ui_api_ElementBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_core_cache_api_Constants::OPTIONS_UI_CATEGORY_CACHE)
         ->addArgument('Cache');    //>(translatable)<

        $fieldMap = array(
            tubepress_core_cache_api_Constants::OPTIONS_UI_CATEGORY_CACHE => array(
                tubepress_core_cache_api_Constants::ENABLED,
                tubepress_core_cache_api_Constants::DIRECTORY,
                tubepress_core_cache_api_Constants::LIFETIME_SECONDS,
                tubepress_core_cache_api_Constants::CLEANING_FACTOR,
            )
        );

        $containerBuilder->register(

            'tubepress_core_cache_impl_options_ui_FieldProvider',
            'tubepress_core_cache_impl_options_ui_FieldProvider'
        )->addArgument(array(new tubepress_api_ioc_Reference('cache_category')))
         ->addArgument($fieldReferences)
         ->addArgument($fieldMap)
         ->addTag('tubepress_core_options_ui_api_FieldProviderInterface');
    }
}