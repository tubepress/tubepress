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
 * @covers tubepress_cache_html_ioc_HtmlCacheExtension<extended>
 */
class tubepress_test_cache_html_ioc_HtmlCacheExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
{
    protected function buildSut()
    {
        return new tubepress_cache_html_ioc_HtmlCacheExtension();
    }

    protected function prepareForLoad()
    {
        $this->_expectOptions();
        $this->_expectOptionsUi();
    }

    private function _expectOptions()
    {
        $this->expectRegistration(
            'tubepress_app_api_options_Reference__htmlcache',
            'tubepress_app_api_options_Reference'
        )->withTag(tubepress_app_api_options_ReferenceInterface::_)
            ->withArgument(array(

                tubepress_app_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                    tubepress_app_api_options_Names::CACHE_HTML_CLEANING_FACTOR  => 100,
                    tubepress_app_api_options_Names::CACHE_HTML_CLEANING_KEY     => 'tubepress_clear_html_cache',
                    tubepress_app_api_options_Names::CACHE_HTML_DIRECTORY        => null,
                    tubepress_app_api_options_Names::CACHE_HTML_ENABLED          => false,
                    tubepress_app_api_options_Names::CACHE_HTML_LIFETIME_SECONDS => 21600, //six hours
                ),

                tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                    tubepress_app_api_options_Names::CACHE_HTML_CLEANING_FACTOR  => 'Cache cleaning factor',           //>(translatable)<
                    tubepress_app_api_options_Names::CACHE_HTML_CLEANING_KEY     => 'Cache cleaning key',           //>(translatable)<
                    tubepress_app_api_options_Names::CACHE_HTML_DIRECTORY        => 'Cache directory',                 //>(translatable)<
                    tubepress_app_api_options_Names::CACHE_HTML_ENABLED          => 'Enable HTML cache',               //>(translatable)<
                    tubepress_app_api_options_Names::CACHE_HTML_LIFETIME_SECONDS => 'Cache expiration time (seconds)', //>(translatable)<
                ),

                tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                    tubepress_app_api_options_Names::CACHE_HTML_CLEANING_FACTOR  => 'If you enter X, the entire cache will be cleaned every 1/X cache writes. Enter 0 to disable cache cleaning.', //>(translatable)<
                    tubepress_app_api_options_Names::CACHE_HTML_CLEANING_KEY     => 'The name of the HTTP request parameter that, when set to <code>true</code>, can remotely flush the cache. For instance, if you enter <code>foobar</code>, then adding <code>?foobar=true</code> to the end of a URL would remotely trigger a clear of the cache.', //>(translatable)<
                    tubepress_app_api_options_Names::CACHE_HTML_DIRECTORY        => 'Leave blank to attempt to use your system\'s temp directory. Otherwise enter the absolute path of a writeable directory.', //>(translatable)<
                    tubepress_app_api_options_Names::CACHE_HTML_ENABLED          => 'Store TubePress\'s HTML output in a cache file to significantly improve performance at the slight expense of freshness.', //>(translatable)<
                    tubepress_app_api_options_Names::CACHE_HTML_LIFETIME_SECONDS => 'Cache entries will be considered stale after the specified number of seconds. Default is 21600 (six hours).',   //>(translatable)<
                ),
            ))->withArgument(array(

                tubepress_app_api_options_Reference::PROPERTY_PRO_ONLY => array(
                    tubepress_app_api_options_Names::CACHE_HTML_CLEANING_FACTOR,
                    tubepress_app_api_options_Names::CACHE_HTML_CLEANING_KEY,
                    tubepress_app_api_options_Names::CACHE_HTML_DIRECTORY,
                    tubepress_app_api_options_Names::CACHE_HTML_ENABLED,
                    tubepress_app_api_options_Names::CACHE_HTML_LIFETIME_SECONDS,
                ),
            ));

        $toValidate = array(
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_POSITIVE => array(
                tubepress_app_api_options_Names::CACHE_HTML_LIFETIME_SECONDS,
            ),
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_NONNEGATIVE => array(
                tubepress_app_api_options_Names::CACHE_HTML_CLEANING_FACTOR,
            ),
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS_OR_HYPHEN => array(
                tubepress_app_api_options_Names::CACHE_HTML_CLEANING_KEY
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
                tubepress_app_api_options_Names::CACHE_HTML_ENABLED,
            ),
            'text' => array(
                tubepress_app_api_options_Names::CACHE_HTML_DIRECTORY,
                tubepress_app_api_options_Names::CACHE_HTML_LIFETIME_SECONDS,
                tubepress_app_api_options_Names::CACHE_HTML_CLEANING_FACTOR,
                tubepress_app_api_options_Names::CACHE_HTML_CLEANING_KEY,
            ),
        );
        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {

                $serviceId = 'htmlcache_field_' . $id;

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

        $fieldMap = array(
            tubepress_app_api_options_ui_CategoryNames::CACHE => array(
                tubepress_app_api_options_Names::CACHE_HTML_ENABLED,
                tubepress_app_api_options_Names::CACHE_HTML_DIRECTORY,
                tubepress_app_api_options_Names::CACHE_HTML_LIFETIME_SECONDS,
                tubepress_app_api_options_Names::CACHE_HTML_CLEANING_FACTOR,
                tubepress_app_api_options_Names::CACHE_HTML_CLEANING_KEY,
            ),
        );

        $this->expectRegistration(
            'tubepress_cache_html_impl_options_ui_FieldProvider',
            'tubepress_cache_html_impl_options_ui_FieldProvider'
        )->withArgument($fieldReferences)
            ->withArgument($fieldMap)
            ->withTag('tubepress_app_api_options_ui_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $textFieldOptionNames = array(

            tubepress_app_api_options_Names::CACHE_HTML_DIRECTORY,
            tubepress_app_api_options_Names::CACHE_HTML_LIFETIME_SECONDS,
            tubepress_app_api_options_Names::CACHE_HTML_CLEANING_FACTOR,
            tubepress_app_api_options_Names::CACHE_HTML_CLEANING_KEY,
        );

        $mockFieldBuilder = $this->mock(tubepress_app_api_options_ui_FieldBuilderInterface::_);

        foreach ($textFieldOptionNames as $textOptionName) {

            $textField = $this->mock('tubepress_app_api_options_ui_FieldInterface');
            $mockFieldBuilder->shouldReceive('newInstance')->once()->with($textOptionName, 'text')->andReturn($textField);
        }

        $booleanField = $this->mock('tubepress_app_api_options_ui_FieldInterface');
        $mockFieldBuilder->shouldReceive('newInstance')->once()->with(tubepress_app_api_options_Names::CACHE_HTML_ENABLED, 'boolean')->andReturn($booleanField);

        return array(
            tubepress_app_api_options_ReferenceInterface::_ => tubepress_app_api_options_ReferenceInterface::_,
            tubepress_app_api_options_ui_FieldBuilderInterface::_ => $mockFieldBuilder,
            tubepress_lib_api_translation_TranslatorInterface::_ => tubepress_lib_api_translation_TranslatorInterface::_
        );
    }
}