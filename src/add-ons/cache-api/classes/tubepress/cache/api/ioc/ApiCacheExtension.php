<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_cache_api_ioc_ApiCacheExtension implements tubepress_spi_ioc_ContainerExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerVendorServices($containerBuilder);
        $this->_registerListener($containerBuilder);
        $this->_registerOptions($containerBuilder);
        $this->_registerOptionsUi($containerBuilder);
    }

    private function _registerVendorServices(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_cache_api_impl_stash_FilesystemCacheBuilder',
            'tubepress_cache_api_impl_stash_FilesystemCacheBuilder'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_boot_BootSettingsInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_));

        $containerBuilder->register(
            'api_cache_pool',
            'Stash\Pool'
        )->addMethodCall('setDriver', array(new tubepress_api_ioc_Reference('api_cache_driver')));

        $containerBuilder->register(
            'api_cache_driver',
            'Stash\Interfaces\DriverInterface'
        )->setFactoryService('tubepress_cache_api_impl_stash_FilesystemCacheBuilder')
         ->setFactoryMethod('buildFilesystemDriver');
    }

    private function _registerListener(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_cache_api_impl_listeners_ApiCacheListener',
            'tubepress_cache_api_impl_listeners_ApiCacheListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('api_cache_pool'))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
             'event'    => tubepress_api_http_Events::EVENT_HTTP_REQUEST,
             'priority' => 100000,
             'method'   => 'onRequest',
             ))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
             'event'    => tubepress_api_http_Events::EVENT_HTTP_RESPONSE,
             'priority' => 100000,
             'method'   => 'onResponse',
         ));
    }

    private function _registerOptions(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_api_options_Reference__cache_api',
            'tubepress_api_options_Reference'
        )->addTag(tubepress_api_options_ReferenceInterface::_)
         ->addArgument(array(

             tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                 tubepress_api_options_Names::CACHE_CLEANING_FACTOR  => 20,
                 tubepress_api_options_Names::CACHE_DIRECTORY        => null,
                 tubepress_api_options_Names::CACHE_ENABLED          => true,
                 tubepress_api_options_Names::CACHE_LIFETIME_SECONDS => 21600, //six hours
             ),
             tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                 tubepress_api_options_Names::CACHE_CLEANING_FACTOR  => 'Cache cleaning factor',           //>(translatable)<
                 tubepress_api_options_Names::CACHE_DIRECTORY        => 'Cache directory',                 //>(translatable)<
                 tubepress_api_options_Names::CACHE_ENABLED          => 'Enable API cache',                //>(translatable)<
                 tubepress_api_options_Names::CACHE_LIFETIME_SECONDS => 'Cache expiration time (seconds)', //>(translatable)<
             ),
             tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                 tubepress_api_options_Names::CACHE_CLEANING_FACTOR  => 'If you enter X, the entire cache will be cleaned every 1/X cache writes. Enter 0 to disable cache cleaning.',                   //>(translatable)<
                 tubepress_api_options_Names::CACHE_DIRECTORY        => 'Leave blank to attempt to use your system\'s temp directory. Otherwise enter the absolute path of a writable directory.',       //>(translatable)<
                 tubepress_api_options_Names::CACHE_ENABLED          => 'Store API responses in a cache file to significantly reduce load times for your galleries at the slight expense of freshness.', //>(translatable)<
                 tubepress_api_options_Names::CACHE_LIFETIME_SECONDS => sprintf('Cache entries will be considered stale after the specified number of seconds. Default is %s (%s).', 21600, "six hours"), //>(translatable)<
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
                $containerBuilder->register(
                    'regex_validator.' . $optionName,
                    'tubepress_api_options_listeners_RegexValidatingListener'
                )->addArgument($type)
                 ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
                 ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
                 ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                     'event'    => tubepress_api_event_Events::OPTION_SET . ".$optionName",
                     'priority' => 100000,
                     'method'   => 'onOption',
                ));
            }
        }
    }

    private function _registerOptionsUi(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
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

                $containerBuilder->register(
                    $serviceId,
                    'tubepress_api_options_ui_FieldInterface'
                )->setFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
                 ->setFactoryMethod('newInstance')
                 ->addArgument($id)
                 ->addArgument($type);

                $fieldReferences[] = new tubepress_api_ioc_Reference($serviceId);
            }
        }

        $categoryReferences = array();
        $categories         = array(
            array(tubepress_api_options_ui_CategoryNames::CACHE, 'Cache'),         //>(translatable)<,
        );
        foreach ($categories as $categoryIdAndLabel) {

            $serviceId = 'cache_api_category_' . $categoryIdAndLabel[0];
            $containerBuilder->register(
                $serviceId,
                'tubepress_options_ui_impl_BaseElement'
            )->addArgument($categoryIdAndLabel[0])
             ->addArgument($categoryIdAndLabel[1]);

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

        $containerBuilder->register(
            'tubepress_api_options_ui_BaseFieldProvider__cache_api',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->addArgument('field-provider-cache-api')
         ->addArgument('API Cache')
         ->addArgument(false)
         ->addArgument(true)
         ->addArgument($categoryReferences)
         ->addArgument($fieldReferences)
         ->addArgument($fieldMap)
         ->addTag('tubepress_spi_options_ui_FieldProviderInterface');
    }
}
