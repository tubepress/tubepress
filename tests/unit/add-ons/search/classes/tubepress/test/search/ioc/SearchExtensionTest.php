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
 * @covers tubepress_search_ioc_SearchExtension
 */
class tubepress_test_search_ioc_SearchExtensionTest extends tubepress_api_test_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_search_ioc_SearchExtension
     */
    protected function buildSut()
    {
        return  new tubepress_search_ioc_SearchExtension();
    }

    protected function prepareForLoad()
    {
        $this->_registerListeners();
        $this->_registerOptions();
        $this->_registerOptionsUi();
        $this->_registerTemplatePathProvider();
    }

    private function _registerListeners()
    {
        $this->expectRegistration(
            'tubepress_search_impl_listeners_SearchListener',
            'tubepress_search_impl_listeners_SearchListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_spi_media_MediaProviderInterface::__,
                'method' => 'setMediaProviders'))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    =>  tubepress_api_event_Events::HTML_GENERATION,
                'priority' => 100000,
                'method'   => 'onHtmlGenerationSearchInput'))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    =>  tubepress_api_event_Events::HTML_GENERATION,
                'priority' => 96000,
                'method'   => 'onHtmlGenerationSearchOutput'))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    =>  tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_api_options_Names::SEARCH_PROVIDER,
                'priority' => 100000,
                'method'   => 'onAcceptableValues'));

        $this->expectRegistration(
            'tubepress_search_impl_listeners_SearchInputTemplateListener',
            'tubepress_search_impl_listeners_SearchInputTemplateListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    =>  tubepress_api_event_Events::TEMPLATE_PRE_RENDER . '.search/input',
                'priority' => 100000,
                'method'   => 'onSearchInputTemplatePreRender'));
    }

    private function _registerTemplatePathProvider()
    {
        $this->expectRegistration(
            'tubepress_api_template_BasePathProvider__search',
            'tubepress_api_template_BasePathProvider'
        )->withArgument(array(
            TUBEPRESS_ROOT . '/src/add-ons/search/templates',
        ))->withTag('tubepress_spi_template_PathProviderInterface');
    }

    private function _registerOptions()
    {
        $this->expectRegistration(
            'tubepress_api_options_Reference__search',
            'tubepress_api_options_Reference'
        )->withTag(tubepress_api_options_ReferenceInterface::_)
            ->withArgument(array(

                tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                    tubepress_api_options_Names::SEARCH_ONLY_USER    => null,
                    tubepress_api_options_Names::SEARCH_PROVIDER     => 'youtube',
                    tubepress_api_options_Names::SEARCH_RESULTS_ONLY => false,
                    tubepress_api_options_Names::SEARCH_RESULTS_URL  => null,
                ),

                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                    tubepress_api_options_Names::SEARCH_ONLY_USER => 'Restrict search results to videos from author', //>(translatable)<

                ),

                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                    tubepress_api_options_Names::SEARCH_ONLY_USER => 'A YouTube or Vimeo user name. Only applies to search-based galleries.',      //>(translatable)<

                ),
            ))->withArgument(array());

        $toValidate = array(
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_ZERO_OR_MORE_WORDCHARS => array(
                tubepress_api_options_Names::SEARCH_ONLY_USER
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

    private function _registerOptionsUi()
    {
        $fieldReferences = array();
        $fieldMap = array(
            'multiSourceText' => array(
                tubepress_api_options_Names::SEARCH_ONLY_USER
            ),
        );

        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {

                $serviceId = 'search_field_' . $id;

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

        $fieldMap = array(
            tubepress_api_options_ui_CategoryNames::FEED => array(
                tubepress_api_options_Names::SEARCH_ONLY_USER,
            ),
        );

        $this->expectRegistration(
            'tubepress_api_options_ui_BaseFieldProvider__search',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->withArgument('field-provider-search')
            ->withArgument('Search')
            ->withArgument(false)
            ->withArgument(false)
            ->withArgument(array())
            ->withArgument($fieldReferences)
            ->withArgument($fieldMap)
            ->withTag('tubepress_spi_options_ui_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $fieldBuilder = $this->mock(tubepress_api_options_ui_FieldBuilderInterface::_);
        $mockField    = $this->mock('tubepress_api_options_ui_FieldInterface');
        $fieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        return array(
            tubepress_api_log_LoggerInterface::_ => tubepress_api_log_LoggerInterface::_,
            tubepress_api_options_ui_FieldBuilderInterface::_ => $fieldBuilder,
            tubepress_api_options_ContextInterface::_ => tubepress_api_options_ContextInterface::_,
            tubepress_api_template_TemplatingInterface::_ => tubepress_api_template_TemplatingInterface::_,
            tubepress_api_http_RequestParametersInterface::_ => tubepress_api_http_RequestParametersInterface::_,
            tubepress_api_url_UrlFactoryInterface::_ => tubepress_api_url_UrlFactoryInterface::_,
            tubepress_api_options_ReferenceInterface::_ => tubepress_api_options_ReferenceInterface::_,
            tubepress_api_translation_TranslatorInterface::_ => tubepress_api_translation_TranslatorInterface::_,
        );
    }
}
