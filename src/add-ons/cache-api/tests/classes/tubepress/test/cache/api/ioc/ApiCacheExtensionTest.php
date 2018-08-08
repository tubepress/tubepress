<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
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
class tubepress_test_cache_api_ioc_ApiCacheExtensionTest extends tubepress_api_test_ioc_AbstractContainerExtensionTest
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
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_boot_BootSettingsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_));

        $this->expectRegistration(
            'api_cache_pool',
            'Stash\Pool'
        )->withMethodCall('setDriver', array(new tubepress_api_ioc_Reference('api_cache_driver')));

        $this->expectRegistration(
            'api_cache_driver',
            'Stash\Interfaces\DriverInterface'
        )->withFactoryService('tubepress_cache_api_impl_stash_FilesystemCacheBuilder')
            ->withFactoryMethod('buildFilesystemDriver');
    }

    private function _expectListener()
    {
        $this->expectRegistration(
            'tubepress_cache_api_impl_listeners_ApiCacheListener',
            'tubepress_cache_api_impl_listeners_ApiCacheListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('api_cache_pool'))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_http_Events::EVENT_HTTP_REQUEST,
                'priority' => 100000,
                'method'   => 'onRequest',
                ))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_http_Events::EVENT_HTTP_RESPONSE,
                'priority' => 100000,
                'method'   => 'onResponse',
            ));
    }

    private function _expectOptions()
    {
        $this->expectRegistration(
            'tubepress_api_options_Reference__cache_api',
            'tubepress_api_options_Reference'
        )->withTag(tubepress_api_options_ReferenceInterface::_)
            ->withArgument(array(

                tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                    tubepress_api_options_Names::CACHE_CLEANING_FACTOR  => 20,
                    tubepress_api_options_Names::CACHE_DIRECTORY        => null,
                    tubepress_api_options_Names::CACHE_ENABLED          => true,
                    tubepress_api_options_Names::CACHE_LIFETIME_SECONDS => 21600, //six hours
                ),
                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                    tubepress_api_options_Names::CACHE_CLEANING_FACTOR  => 'Cache cleaning factor',
                    tubepress_api_options_Names::CACHE_DIRECTORY        => 'Cache directory',
                    tubepress_api_options_Names::CACHE_ENABLED          => 'Enable API cache',
                    tubepress_api_options_Names::CACHE_LIFETIME_SECONDS => 'Cache expiration time (seconds)',
                ),
                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                    tubepress_api_options_Names::CACHE_CLEANING_FACTOR  => 'If you enter X, the entire cache will be cleaned every 1/X cache writes. Enter 0 to disable cache cleaning.',
                    tubepress_api_options_Names::CACHE_DIRECTORY        => 'Leave blank to attempt to use your system\'s temp directory. Otherwise enter the absolute path of a writable directory.',
                    tubepress_api_options_Names::CACHE_ENABLED          => 'Store API responses in a cache file to significantly reduce load times for your galleries at the slight expense of freshness.',
                    tubepress_api_options_Names::CACHE_LIFETIME_SECONDS => sprintf('Cache entries will be considered stale after the specified number of seconds. Default is %s (%s).', 21600, "six hours"),
                ),
            ));

        $toValidate = array(
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_INTEGER_POSITIVE => array(
                tubepress_api_options_Names::CACHE_LIFETIME_SECONDS,
            ),
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_INTEGER_NONNEGATIVE => array(
                tubepress_api_options_Names::CACHE_CLEANING_FACTOR,
            ),
        );

        foreach ($toValidate as $type => $optionNames) {
            foreach ($optionNames as $optionName) {
                $this->expectRegistration(
                    'regex_validator.' . $optionName,
                    'tubepress_api_options_listeners_RegexValidatingListener'
                )->withArgument($type)
                    ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
                    ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
                    ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                        'event'    => tubepress_api_event_Events::OPTION_SET . ".$optionName",
                        'priority' => 100000,
                        'method'   => 'onOption',
                    ));
            }
        }
    }

    private function _expectOptionsUi()
    {
        $fieldReferences = array();
        $fieldMap        = array(
            'boolean' => array(
                tubepress_api_options_Names::CACHE_ENABLED,
            ),
            'text' => array(
                tubepress_api_options_Names::CACHE_DIRECTORY,
                tubepress_api_options_Names::CACHE_LIFETIME_SECONDS,
                tubepress_api_options_Names::CACHE_CLEANING_FACTOR,
            ),
        );

        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {

                $serviceId = 'cache_api_field_' . $id;

                $this->expectRegistration(
                    $serviceId,
                    'tubepress_api_options_ui_FieldInterface'
                )->withFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
                    ->withFactoryMethod('newInstance')
                    ->withArgument($id)
                    ->withArgument($type);

                $fieldReferences[] = new tubepress_api_ioc_Reference($serviceId);
            }
        }

        $categoryReferences = array();
        $categories         = array(
            array(tubepress_api_options_ui_CategoryNames::CACHE, 'Cache'),
        );
        foreach ($categories as $categoryIdAndLabel) {

            $serviceId = 'cache_api_category_' . $categoryIdAndLabel[0];
            $this->expectRegistration(
                $serviceId,
                'tubepress_options_ui_impl_BaseElement'
            )->withArgument($categoryIdAndLabel[0])
                ->withArgument($categoryIdAndLabel[1]);

            $categoryReferences[] = new tubepress_api_ioc_Reference($serviceId);
        }

        $fieldMap = array(
            tubepress_api_options_ui_CategoryNames::CACHE => array(
                tubepress_api_options_Names::CACHE_ENABLED,
                tubepress_api_options_Names::CACHE_DIRECTORY,
                tubepress_api_options_Names::CACHE_LIFETIME_SECONDS,
                tubepress_api_options_Names::CACHE_CLEANING_FACTOR,
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
            ->withTag('tubepress_spi_options_ui_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $logger = $this->mock(tubepress_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $logger->shouldReceive('debug')->atLeast(1);

        $context = $this->mock(tubepress_api_options_ContextInterface::_);
        $context->shouldReceive('get')->once()->with(tubepress_api_options_Names::CACHE_DIRECTORY)->andReturn('/foobar');

        $bootSettings = $this->mock(tubepress_api_boot_BootSettingsInterface::_);
        $bootSettings->shouldReceive('getPathToSystemCacheDirectory')->once()->andReturn(sys_get_temp_dir() . '/tubepress-test-cache-api');

        $fieldBuilder = $this->mock(tubepress_api_options_ui_FieldBuilderInterface::_);
        $mockField    = $this->mock('tubepress_api_options_ui_FieldInterface');
        $fieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        return array(
            tubepress_api_options_ContextInterface::_         => $context,
            tubepress_api_boot_BootSettingsInterface::_       => $bootSettings,
            tubepress_api_log_LoggerInterface::_              => $logger,
            tubepress_api_options_ReferenceInterface::_       => tubepress_api_options_ReferenceInterface::_,
            tubepress_api_translation_TranslatorInterface::_  => tubepress_api_translation_TranslatorInterface::_,
            tubepress_api_options_ui_FieldBuilderInterface::_ => $fieldBuilder,
        );
    }

    protected function onTearDown()
    {
        if (is_dir(sys_get_temp_dir() . '/tubepress-test-cache-api')) {

            $this->recursivelyDeleteDirectory(sys_get_temp_dir() . '/tubepress-test-cache-api');
        }
    }
}
