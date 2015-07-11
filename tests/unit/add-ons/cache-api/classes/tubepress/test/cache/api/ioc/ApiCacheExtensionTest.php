<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_cache_api_ioc_ApiCacheExtension
 */
class tubepress_test_cache_api_ioc_ApiCacheExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_cache_api_ioc_ApiCacheExtension
     */
    protected function buildSut()
    {
        return  new tubepress_cache_api_ioc_ApiCacheExtension();
    }

    protected function prepareForLoad()
    {
        $this->_expectVendorServices();
        $this->_expectListener();
        $this->_expectOptions();
        $this->_expectOptionsUi();
    }

    private function _expectVendorServices()
    {
        $this->expectRegistration(

            'tubepress_cache_api_impl_stash_FilesystemCacheBuilder',
            'tubepress_cache_api_impl_stash_FilesystemCacheBuilder'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_boot_BootSettingsInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_));

        $this->expectRegistration(
            'ehough_stash_interfaces_PoolInterface',
            'ehough_stash_Pool'
        )->withMethodCall('setDriver', array(new tubepress_platform_api_ioc_Reference('ehough_stash_interfaces_DriverInterface')));

        $this->expectRegistration(
            'ehough_stash_interfaces_DriverInterface',
            'ehough_stash_interfaces_DriverInterface'
        )->withFactoryService('tubepress_cache_api_impl_stash_FilesystemCacheBuilder')
            ->withFactoryMethod('buildFilesystemDriver');
    }

    private function _expectListener()
    {
        $this->expectRegistration(
            'tubepress_cache_api_impl_listeners_ApiCacheListener',
            'tubepress_cache_api_impl_listeners_ApiCacheListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference('ehough_stash_interfaces_PoolInterface'))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_lib_api_http_Events::EVENT_HTTP_REQUEST,
                'priority' => 100000,
                'method'   => 'onRequest'))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_lib_api_http_Events::EVENT_HTTP_RESPONSE,
                'priority' => 100000,
                'method'   => 'onResponse'
            ));
    }

    private function _expectOptions()
    {
        $this->expectRegistration(
            'tubepress_app_api_options_Reference__cache_api',
            'tubepress_app_api_options_Reference'
        )->withTag(tubepress_app_api_options_ReferenceInterface::_)
            ->withArgument(array(

                tubepress_app_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                    tubepress_app_api_options_Names::CACHE_CLEANING_FACTOR  => 20,
                    tubepress_app_api_options_Names::CACHE_DIRECTORY        => null,
                    tubepress_app_api_options_Names::CACHE_ENABLED          => true,
                    tubepress_app_api_options_Names::CACHE_LIFETIME_SECONDS => 21600, //six hours
                ),
                tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                    tubepress_app_api_options_Names::CACHE_CLEANING_FACTOR  => 'Cache cleaning factor',        //>(translatable)<
                    tubepress_app_api_options_Names::CACHE_DIRECTORY        => 'Cache directory',           //>(translatable)<
                    tubepress_app_api_options_Names::CACHE_ENABLED          => 'Enable API cache',                //>(translatable)<
                    tubepress_app_api_options_Names::CACHE_LIFETIME_SECONDS => 'Cache expiration time (seconds)', //>(translatable)<
                ),
                tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                    tubepress_app_api_options_Names::CACHE_CLEANING_FACTOR  => 'If you enter X, the entire cache will be cleaned every 1/X cache writes. Enter 0 to disable cache cleaning.', //>(translatable)<
                    tubepress_app_api_options_Names::CACHE_DIRECTORY        => 'Leave blank to attempt to use your system\'s temp directory. Otherwise enter the absolute path of a writeable directory.', //>(translatable)<
                    tubepress_app_api_options_Names::CACHE_ENABLED          => 'Store API responses in a cache file to significantly reduce load times for your galleries at the slight expense of freshness.', //>(translatable)<
                    tubepress_app_api_options_Names::CACHE_LIFETIME_SECONDS => 'Cache entries will be considered stale after the specified number of seconds. Default is 21600 (six hours).',   //>(translatable)<
                ),
            ));

        $toValidate = array(
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_POSITIVE => array(
                tubepress_app_api_options_Names::CACHE_LIFETIME_SECONDS,
            ),
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_NONNEGATIVE => array(
                tubepress_app_api_options_Names::CACHE_CLEANING_FACTOR,
            ),
        );

        foreach ($toValidate as $type => $optionNames) {
            foreach ($optionNames as $optionName) {
                $this->expectRegistration(
                    'regex_validator.' . $optionName,
                    'tubepress_app_api_listeners_options_RegexValidatingListener'
                )->withArgument($type)
                    ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ReferenceInterface::_))
                    ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_translation_TranslatorInterface::_))
                    ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                        'event'    => tubepress_app_api_event_Events::OPTION_SET . ".$optionName",
                        'priority' => 100000,
                        'method'   => 'onOption',
                    ));
            }
        }
    }

    private function _expectOptionsUi()
    {
        $fieldReferences = array();
        $fieldMap = array(
            'boolean' => array(
                tubepress_app_api_options_Names::CACHE_ENABLED,
            ),
            'text' => array(
                tubepress_app_api_options_Names::CACHE_DIRECTORY,
                tubepress_app_api_options_Names::CACHE_LIFETIME_SECONDS,
                tubepress_app_api_options_Names::CACHE_CLEANING_FACTOR,
            ),
        );

        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {

                $serviceId = 'cache_api_field_' . $id;

                $this->expectRegistration(
                    $serviceId,
                    'tubepress_app_api_options_ui_FieldInterface'
                )->withFactoryService(tubepress_app_api_options_ui_FieldBuilderInterface::_)
                    ->withFactoryMethod('newInstance')
                    ->withArgument($id)
                    ->withArgument($type);

                $fieldReferences[] = new tubepress_platform_api_ioc_Reference($serviceId);
            }
        }

        $categoryReferences = array();
        $categories = array(
            array(tubepress_app_api_options_ui_CategoryNames::CACHE, 'Cache'),         //>(translatable)<,
        );
        foreach ($categories as $categoryIdAndLabel) {

            $serviceId = 'cache_api_category_' . $categoryIdAndLabel[0];
            $this->expectRegistration(
                $serviceId,
                'tubepress_options_ui_impl_BaseElement'
            )->withArgument($categoryIdAndLabel[0])
                ->withArgument($categoryIdAndLabel[1]);

            $categoryReferences[] = new tubepress_platform_api_ioc_Reference($serviceId);
        }

        $fieldMap = array(
            tubepress_app_api_options_ui_CategoryNames::CACHE => array(
                tubepress_app_api_options_Names::CACHE_ENABLED,
                tubepress_app_api_options_Names::CACHE_DIRECTORY,
                tubepress_app_api_options_Names::CACHE_LIFETIME_SECONDS,
                tubepress_app_api_options_Names::CACHE_CLEANING_FACTOR,
            ),
        );

        $this->expectRegistration(
            'tubepress_api_options_ui_BaseFieldProvider__cache_api',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->withArgument('field-provider-cache-api')
            ->withArgument('API Cache')
            ->withArgument(false)
            ->withArgument(true)
            ->withArgument($categoryReferences)
            ->withArgument($fieldReferences)
            ->withArgument($fieldMap)
            ->withTag('tubepress_app_api_options_ui_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $logger = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $logger->shouldReceive('debug')->atLeast(1);

        $context = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $context->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::CACHE_DIRECTORY)->andReturn('/foobar');

        $bootSettings = $this->mock(tubepress_platform_api_boot_BootSettingsInterface::_);
        $bootSettings->shouldReceive('getPathToSystemCacheDirectory')->once()->andReturn(sys_get_temp_dir() . '/tubepress-test-cache-api');

        $fieldBuilder = $this->mock(tubepress_app_api_options_ui_FieldBuilderInterface::_);
        $mockField    = $this->mock('tubepress_app_api_options_ui_FieldInterface');
        $fieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        return array(
            tubepress_app_api_options_ContextInterface::_         => $context,
            tubepress_platform_api_boot_BootSettingsInterface::_  => $bootSettings,
            tubepress_platform_api_log_LoggerInterface::_         => $logger,
            tubepress_app_api_options_ReferenceInterface::_       => tubepress_app_api_options_ReferenceInterface::_,
            tubepress_lib_api_translation_TranslatorInterface::_  => tubepress_lib_api_translation_TranslatorInterface::_,
            tubepress_app_api_options_ui_FieldBuilderInterface::_ => $fieldBuilder,
        );
    }

    protected function onTearDown()
    {
        if (is_dir(sys_get_temp_dir() . '/tubepress-test-cache-api')) {

            $this->recursivelyDeleteDirectory(sys_get_temp_dir() . '/tubepress-test-cache-api');
        }
    }
}
