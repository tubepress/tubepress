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
 * @covers tubepress_core_cache_ioc_CacheExtension
 */
class tubepress_test_core_cache_ioc_CacheExtensionTest extends tubepress_test_core_ioc_AbstractIocContainerExtensionTest
{
    /**
     * @return tubepress_core_cache_ioc_CacheExtension
     */
    protected function buildSut()
    {
        return new tubepress_core_cache_ioc_CacheExtension();
    }

    protected function prepareForLoad()
    {
        /**
         * Long, guaranteed unique service IDs (since they're anonymous)
         */
        $actualPoolServiceId = 'tubepress_core_cache_ioc_CacheExtension__registerCacheService_actualPoolServiceId';
        $builderServiceId    = 'tubepress_core_cache_ioc_CacheExtension__registerCacheService_builderServiceId';

        /**
         * First register the default cache builder.
         */
        $this->expectRegistration(

            $builderServiceId,
            'tubepress_core_cache_impl_stash_FilesystemCacheBuilder'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference('ehough_filesystem_FilesystemInterface'));

        $actualPoolDefinition = new tubepress_impl_ioc_Definition('ehough_stash_interfaces_PoolInterface');
        $actualPoolDefinition->setFactoryService($builderServiceId);
        $actualPoolDefinition->setFactoryMethod('buildCache');
        $this->expectDefinition($actualPoolServiceId, $actualPoolDefinition);

        $this->expectRegistration(

            'ehough_stash_interfaces_PoolInterface',
            'tubepress_core_cache_impl_stash_PoolDecorator'

        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference($actualPoolServiceId));

        $this->expectRegistration(

            'tubepress_core_cache_impl_listeners_http_ApiCacheListener',
            'tubepress_core_cache_impl_listeners_http_ApiCacheListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('ehough_stash_interfaces_PoolInterface'))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_http_api_Constants::EVENT_HTTP_REQUEST,
                'method'   => 'onRequest',
                'priority' => 10000
            ))->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_http_api_Constants::EVENT_HTTP_RESPONSE,
                'method'   => 'onResponse',
                'priority' => 10000
            ));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_cache', array(

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

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_VALIDATION . '_cache', array(

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
                $this->expectRegistration(
                    'cache_field_' . $fieldIndex++,
                    'tubepress_core_options_ui_api_FieldInterface'
                )->withFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
                    ->withFactoryMethod('newInstance')
                    ->withArgument($id)
                    ->withArgument($type);
            }
        }

        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; $x++) {
            $fieldReferences[] = new tubepress_api_ioc_Reference('cache_field_' . $x);
        }

        $this->expectRegistration(
            'cache_category',
            'tubepress_core_options_ui_api_ElementInterface'
        )->withFactoryService(tubepress_core_options_ui_api_ElementBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_core_cache_api_Constants::OPTIONS_UI_CATEGORY_CACHE)
            ->withArgument('Cache');    //>(translatable)<

        $fieldMap = array(
            tubepress_core_cache_api_Constants::OPTIONS_UI_CATEGORY_CACHE => array(
                tubepress_core_cache_api_Constants::ENABLED,
                tubepress_core_cache_api_Constants::DIRECTORY,
                tubepress_core_cache_api_Constants::LIFETIME_SECONDS,
                tubepress_core_cache_api_Constants::CLEANING_FACTOR,
            )
        );

        $this->expectRegistration(

            'tubepress_core_cache_impl_options_ui_FieldProvider',
            'tubepress_core_cache_impl_options_ui_FieldProvider'
        )->withArgument(array(new tubepress_api_ioc_Reference('cache_category')))
            ->withArgument($fieldReferences)
            ->withArgument($fieldMap)
            ->withTag('tubepress_core_options_ui_api_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $context = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $context->shouldReceive('get')->once()->with(tubepress_core_cache_api_Constants::DIRECTORY)->andReturn(sys_get_temp_dir());

        $mockField = $this->mock('tubepress_core_options_ui_api_FieldInterface');
        $fieldBuilder = $this->mock(tubepress_core_options_ui_api_FieldBuilderInterface::_);
        $fieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        $mockCategory = $this->mock('tubepress_core_options_ui_api_ElementInterface');
        $elementBuilder = $this->mock(tubepress_core_options_ui_api_ElementBuilderInterface::_);
        $elementBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockCategory);

        return array(

            tubepress_core_options_api_ContextInterface::_ => $context,
            'ehough_filesystem_FilesystemInterface'        => 'ehough_filesystem_FilesystemInterface',
            tubepress_api_log_LoggerInterface::_           => tubepress_api_log_LoggerInterface::_,
            tubepress_core_options_ui_api_FieldBuilderInterface::_ => $fieldBuilder,
            tubepress_core_options_ui_api_ElementBuilderInterface::_ => $elementBuilder
        );
    }
}