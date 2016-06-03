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

class tubepress_search_ioc_SearchExtension implements tubepress_spi_ioc_ContainerExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerListeners($containerBuilder);
        $this->_registerOptions($containerBuilder);
        $this->_registerOptionsUi($containerBuilder);
        $this->_registerTemplatePathProvider($containerBuilder);
    }

    private function _registerListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_search_impl_listeners_SearchListener',
            'tubepress_search_impl_listeners_SearchListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
         ->addTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_spi_media_MediaProviderInterface::__,
            'method' => 'setMediaProviders', ))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::HTML_GENERATION,
            'priority' => 100000,
            'method'   => 'onHtmlGenerationSearchInput', ))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::HTML_GENERATION,
            'priority' => 96000,
            'method'   => 'onHtmlGenerationSearchOutput', ))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_api_options_Names::SEARCH_PROVIDER,
            'priority' => 100000,
            'method'   => 'onAcceptableValues', ));

        $containerBuilder->register(
            'tubepress_search_impl_listeners_SearchInputTemplateListener',
            'tubepress_search_impl_listeners_SearchInputTemplateListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::TEMPLATE_PRE_RENDER . '.search/input',
            'priority' => 100000,
            'method'   => 'onSearchInputTemplatePreRender', ));
    }

    private function _registerTemplatePathProvider(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_api_template_BasePathProvider__search',
            'tubepress_api_template_BasePathProvider'
        )->addArgument(array(
            TUBEPRESS_ROOT . '/src/add-ons/search/templates',
        ))->addTag('tubepress_spi_template_PathProviderInterface');
    }

    private function _registerOptions(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_api_options_Reference__search',
            'tubepress_api_options_Reference'
        )->addTag(tubepress_api_options_ReferenceInterface::_)
         ->addArgument(array(

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
        ))->addArgument(array());

        $toValidate = array(
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_ZERO_OR_MORE_WORDCHARS => array(
                tubepress_api_options_Names::SEARCH_ONLY_USER,
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
            'multiSourceText' => array(
                tubepress_api_options_Names::SEARCH_ONLY_USER,
            ),
        );

        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {

                $serviceId = 'search_field_' . $id;

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
            tubepress_api_options_ui_CategoryNames::FEED => array(
                tubepress_api_options_Names::SEARCH_ONLY_USER,
            ),
        );

        $containerBuilder->register(
            'tubepress_api_options_ui_BaseFieldProvider__search',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->addArgument('field-provider-search')
         ->addArgument('Search')
         ->addArgument(false)
         ->addArgument(false)
         ->addArgument(array())
         ->addArgument($fieldReferences)
         ->addArgument($fieldMap)
         ->addTag('tubepress_spi_options_ui_FieldProviderInterface');
    }
}
