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
 * @covers tubepress_app_apicache_ioc_ApiCacheExtension
 */
class tubepress_test_app_apicache_ioc_CacheExtensionTest extends tubepress_test_ioc_AbstractIocContainerExtensionTest
{
    /**
     * @return tubepress_app_apicache_ioc_ApiCacheExtension
     */
    protected function buildSut()
    {
        return new tubepress_app_apicache_ioc_ApiCacheExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            'tubepress_app_apicache_impl_listeners_http_ApiCacheListener',
            'tubepress_app_apicache_impl_listeners_http_ApiCacheListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference('ehough_stash_interfaces_PoolInterface'))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_lib_http_api_Constants::EVENT_HTTP_REQUEST,
                'method'   => 'onRequest',
                'priority' => 10000
            ))->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_lib_http_api_Constants::EVENT_HTTP_RESPONSE,
                'method'   => 'onResponse',
                'priority' => 10000
            ));

        $this->expectParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_cache', array(

            'defaultValues' => array(

                tubepress_app_apicache_api_Constants::OPTION_CLEANING_FACTOR  => 20,
                tubepress_app_apicache_api_Constants::OPTION_DIRECTORY        => null,
                tubepress_app_apicache_api_Constants::OPTION_ENABLED          => true,
                tubepress_app_apicache_api_Constants::OPTION_LIFETIME_SECONDS => 3600,
            ),

            'labels' => array(

                tubepress_app_apicache_api_Constants::OPTION_CLEANING_FACTOR  => 'Cache cleaning factor',        //>(translatable)<
                tubepress_app_apicache_api_Constants::OPTION_DIRECTORY        => 'Cache directory',           //>(translatable)<
                tubepress_app_apicache_api_Constants::OPTION_ENABLED          => 'Enable API cache',                //>(translatable)<
                tubepress_app_apicache_api_Constants::OPTION_LIFETIME_SECONDS => 'Cache expiration time (seconds)', //>(translatable)<
            ),

            'descriptions' => array(

                tubepress_app_apicache_api_Constants::OPTION_CLEANING_FACTOR  => 'If you enter X, the entire cache will be cleaned every 1/X cache writes. Enter 0 to disable cache cleaning.', //>(translatable)<
                tubepress_app_apicache_api_Constants::OPTION_DIRECTORY        => 'Leave blank to attempt to use your system\'s temp directory. Otherwise enter the absolute path of a writeable directory.', //>(translatable)<
                tubepress_app_apicache_api_Constants::OPTION_ENABLED          => 'Store API responses in a cache file to significantly reduce load times for your galleries at the slight expense of freshness.', //>(translatable)<
                tubepress_app_apicache_api_Constants::OPTION_LIFETIME_SECONDS => 'Cache entries will be considered stale after the specified number of seconds. Default is 3600 (one hour).',   //>(translatable)<
            )
        ));

        $this->expectParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_VALIDATION . '_cache', array(

            'priority' => 3000,
            'map' => array(
                'positiveInteger' => array(
                    tubepress_app_apicache_api_Constants::OPTION_LIFETIME_SECONDS,
                ),
                'nonNegativeInteger' => array(
                    tubepress_app_apicache_api_Constants::OPTION_CLEANING_FACTOR,
                )
            )
        ));

        $fieldIndex = 0;
        $fieldMap = array(
            'boolean' => array(
                tubepress_app_apicache_api_Constants::OPTION_ENABLED
            ),
            'text' => array(
                tubepress_app_apicache_api_Constants::OPTION_DIRECTORY,
                tubepress_app_apicache_api_Constants::OPTION_LIFETIME_SECONDS,
                tubepress_app_apicache_api_Constants::OPTION_CLEANING_FACTOR
            )
        );
        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {
                $this->expectRegistration(
                    'cache_field_' . $fieldIndex++,
                    'tubepress_app_options_ui_api_FieldInterface'
                )->withFactoryService(tubepress_app_options_ui_api_FieldBuilderInterface::_)
                    ->withFactoryMethod('newInstance')
                    ->withArgument($id)
                    ->withArgument($type);
            }
        }

        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; $x++) {
            $fieldReferences[] = new tubepress_platform_api_ioc_Reference('cache_field_' . $x);
        }

        $this->expectRegistration(
            'cache_category',
            'tubepress_app_options_ui_api_ElementInterface'
        )->withFactoryService(tubepress_app_options_ui_api_ElementBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_app_apicache_api_Constants::OPTIONS_UI_CATEGORY_CACHE)
            ->withArgument('Cache');    //>(translatable)<

        $fieldMap = array(
            tubepress_app_apicache_api_Constants::OPTIONS_UI_CATEGORY_CACHE => array(
                tubepress_app_apicache_api_Constants::OPTION_ENABLED,
                tubepress_app_apicache_api_Constants::OPTION_DIRECTORY,
                tubepress_app_apicache_api_Constants::OPTION_LIFETIME_SECONDS,
                tubepress_app_apicache_api_Constants::OPTION_CLEANING_FACTOR,
            )
        );

        $this->expectRegistration(

            'tubepress_app_apicache_impl_options_ui_FieldProvider',
            'tubepress_app_apicache_impl_options_ui_FieldProvider'
        )->withArgument(array(new tubepress_platform_api_ioc_Reference('cache_category')))
            ->withArgument($fieldReferences)
            ->withArgument($fieldMap)
            ->withTag('tubepress_app_options_ui_api_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {

        $mockField = $this->mock('tubepress_app_options_ui_api_FieldInterface');
        $fieldBuilder = $this->mock(tubepress_app_options_ui_api_FieldBuilderInterface::_);
        $fieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        $mockCategory = $this->mock('tubepress_app_options_ui_api_ElementInterface');
        $elementBuilder = $this->mock(tubepress_app_options_ui_api_ElementBuilderInterface::_);
        $elementBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockCategory);

        return array(

            tubepress_app_options_api_ContextInterface::_ => tubepress_app_options_api_ContextInterface::_,
            'ehough_filesystem_FilesystemInterface'        => 'ehough_filesystem_FilesystemInterface',
            tubepress_platform_api_log_LoggerInterface::_           => tubepress_platform_api_log_LoggerInterface::_,
            tubepress_app_options_ui_api_FieldBuilderInterface::_ => $fieldBuilder,
            tubepress_app_options_ui_api_ElementBuilderInterface::_ => $elementBuilder,
            'ehough_stash_interfaces_PoolInterface' => 'ehough_stash_interfaces_PoolInterface'
        );
    }
}