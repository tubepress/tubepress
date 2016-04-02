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

class tubepress_cache_html_ioc_HtmlCacheExtension implements tubepress_spi_ioc_ContainerExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerOptions($containerBuilder);
        $this->_registerOptionsUiFieldProvider($containerBuilder);
    }

    private function _registerOptions(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_api_options_Reference__htmlcache',
            'tubepress_api_options_Reference'
        )->addTag(tubepress_api_options_ReferenceInterface::_)
         ->addArgument(array(

            tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                tubepress_api_options_Names::CACHE_HTML_CLEANING_FACTOR  => 100,
                tubepress_api_options_Names::CACHE_HTML_CLEANING_KEY     => 'tubepress_clear_html_cache',
                tubepress_api_options_Names::CACHE_HTML_DIRECTORY        => null,
                tubepress_api_options_Names::CACHE_HTML_ENABLED          => false,
                tubepress_api_options_Names::CACHE_HTML_LIFETIME_SECONDS => 21600, //six hours
            ),

            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                tubepress_api_options_Names::CACHE_HTML_CLEANING_FACTOR  => 'Cache cleaning factor',           //>(translatable)<
                tubepress_api_options_Names::CACHE_HTML_CLEANING_KEY     => 'Cache cleaning key',           //>(translatable)<
                tubepress_api_options_Names::CACHE_HTML_DIRECTORY        => 'Cache directory',                 //>(translatable)<
                tubepress_api_options_Names::CACHE_HTML_ENABLED          => 'Enable HTML cache',               //>(translatable)<
                tubepress_api_options_Names::CACHE_HTML_LIFETIME_SECONDS => 'Cache expiration time (seconds)', //>(translatable)<tubepress_api_options_Names::CACHE_HTML_CLEANING_KEY
            ),

            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                tubepress_api_options_Names::CACHE_HTML_CLEANING_FACTOR  => 'If you enter X, the entire cache will be cleaned every 1/X cache writes. Enter 0 to disable cache cleaning.', //>(translatable)<
                tubepress_api_options_Names::CACHE_HTML_CLEANING_KEY     => 'The name of the HTTP request parameter that, when set to <code>true</code>, can remotely flush the cache. For instance, if you enter <code>foobar</code>, then adding <code>?foobar=true</code> to the end of a URL would remotely trigger a clear of the cache.', //>(translatable)<
                tubepress_api_options_Names::CACHE_HTML_DIRECTORY        => 'Leave blank to attempt to use your system\'s temp directory. Otherwise enter the absolute path of a writable directory.', //>(translatable)<
                tubepress_api_options_Names::CACHE_HTML_ENABLED          => 'Store TubePress\'s HTML output in a cache file to significantly improve performance at the slight expense of freshness.', //>(translatable)<
                tubepress_api_options_Names::CACHE_HTML_LIFETIME_SECONDS => sprintf('Cache entries will be considered stale after the specified number of seconds. Default is %s (%s).', 21600, "six hours"),  //>(translatable)<
            ),
        ))->addArgument(array(

            tubepress_api_options_Reference::PROPERTY_PRO_ONLY => array(
                tubepress_api_options_Names::CACHE_HTML_CLEANING_FACTOR,
                tubepress_api_options_Names::CACHE_HTML_CLEANING_KEY,
                tubepress_api_options_Names::CACHE_HTML_DIRECTORY,
                tubepress_api_options_Names::CACHE_HTML_ENABLED,
                tubepress_api_options_Names::CACHE_HTML_LIFETIME_SECONDS,
            ),
        ));

        $toValidate = array(
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_INTEGER_POSITIVE => array(
                tubepress_api_options_Names::CACHE_HTML_LIFETIME_SECONDS,
            ),
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_INTEGER_NONNEGATIVE => array(
                tubepress_api_options_Names::CACHE_HTML_CLEANING_FACTOR,
            ),
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS_OR_HYPHEN => array(
                tubepress_api_options_Names::CACHE_HTML_CLEANING_KEY,
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

    private function _registerOptionsUiFieldProvider(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $fieldReferences = array();
        $fieldMap        = array(
            'boolean' => array(
                tubepress_api_options_Names::CACHE_HTML_ENABLED,
            ),
            'text' => array(
                tubepress_api_options_Names::CACHE_HTML_DIRECTORY,
                tubepress_api_options_Names::CACHE_HTML_LIFETIME_SECONDS,
                tubepress_api_options_Names::CACHE_HTML_CLEANING_FACTOR,
                tubepress_api_options_Names::CACHE_HTML_CLEANING_KEY,
            ),
        );
        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {

                $serviceId = 'htmlcache_field_' . $id;

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

        $fieldMap = array(
            tubepress_api_options_ui_CategoryNames::CACHE => array(
                tubepress_api_options_Names::CACHE_HTML_ENABLED,
                tubepress_api_options_Names::CACHE_HTML_DIRECTORY,
                tubepress_api_options_Names::CACHE_HTML_LIFETIME_SECONDS,
                tubepress_api_options_Names::CACHE_HTML_CLEANING_FACTOR,
                tubepress_api_options_Names::CACHE_HTML_CLEANING_KEY,
            ),
        );

        $containerBuilder->register(
            'tubepress_api_options_ui_BaseFieldProvider__cache_html',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->addArgument('field-provider-htmlcache')
         ->addArgument('HTML Cache')                                        //>(translatable)<
         ->addArgument(false)
         ->addArgument(true)
         ->addArgument(array())
         ->addArgument($fieldReferences)
         ->addArgument($fieldMap)
         ->addTag('tubepress_spi_options_ui_FieldProviderInterface');
    }
}
