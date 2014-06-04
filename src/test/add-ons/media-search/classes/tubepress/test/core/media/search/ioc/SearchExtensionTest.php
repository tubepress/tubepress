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
 * @covers tubepress_core_media_search_ioc_SearchExtension
 */
class tubepress_test_core_media_search_ioc_SearchExtensionTest extends tubepress_test_core_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_api_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_core_media_search_ioc_SearchExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            'tubepress_core_media_search_impl_listeners_html_SearchInputListener',
            'tubepress_core_media_search_impl_listeners_html_SearchInputListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_template_api_TemplateFactoryInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_html_api_Constants::EVENT_PRIMARY_HTML,
                'method'   => 'onHtmlGeneration',
                'priority' => 10000
            ));

        $this->expectRegistration(

            'tubepress_core_media_search_impl_listeners_html_SearchOutputListener',
            'tubepress_core_media_search_impl_listeners_html_SearchOutputListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_core_media_gallery_impl_listeners_html_GalleryMaker'))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_html_api_Constants::EVENT_PRIMARY_HTML,
                'method'   => 'onHtmlGeneration',
                'priority' => 9000
            ));

        $this->expectRegistration(

            'tubepress_core_media_search_impl_listeners_options_AcceptableValues',
            'tubepress_core_media_search_impl_listeners_options_AcceptableValues'
        )->withTag(tubepress_core_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_core_provider_api_MediaProviderInterface::_,
                'method' => 'setVideoProviders'
            ))->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_options_api_Constants::EVENT_OPTION_GET_ACCEPTABLE_VALUES . '.' . tubepress_core_media_search_api_Constants::OPTION_SEARCH_PROVIDER,
                'method'   => 'onAcceptableValues',
                'priority' => 30000
            ));

        $this->expectRegistration(

            'tubepress_core_media_search_impl_listeners_template_SearchInputCoreVariables',
            'tubepress_core_media_search_impl_listeners_template_SearchInputCoreVariables'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_url_api_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_media_search_api_Constants::EVENT_TEMPLATE_SEARCH_INPUT,
                'method'   => 'onSearchInputTemplate',
                'priority' => 10000
            ));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_search', array(

            'defaultValues' => array(
                tubepress_core_media_search_api_Constants::OPTION_SEARCH_ONLY_USER    => null,
                tubepress_core_media_search_api_Constants::OPTION_SEARCH_PROVIDER     => null,
                tubepress_core_media_search_api_Constants::OPTION_SEARCH_RESULTS_ONLY => false,
                tubepress_core_media_search_api_Constants::OPTION_SEARCH_RESULTS_URL  => null,
            ),

            'labels' => array(
                tubepress_core_media_search_api_Constants::OPTION_SEARCH_ONLY_USER => 'Restrict search results to videos from author', //>(translatable)<
            ),

            'descriptions' => array(
                tubepress_core_media_search_api_Constants::OPTION_SEARCH_ONLY_USER => 'A YouTube or Vimeo user name. Only applies to search-based galleries.',      //>(translatable)<
            )
        ));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_VALIDATION . '_search', array(

            'priority' => 30000,
            'map'      => array(
                'zeroOrMoreWordChars' => array(
                    tubepress_core_media_search_api_Constants::OPTION_SEARCH_ONLY_USER
                )
            )
        ));
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(

            tubepress_core_options_api_ContextInterface::_ => tubepress_core_options_api_ContextInterface::_,
            tubepress_core_translation_api_TranslatorInterface::_ => tubepress_core_translation_api_TranslatorInterface::_,
            tubepress_core_url_api_UrlFactoryInterface::_ => tubepress_core_url_api_UrlFactoryInterface::_,
            tubepress_core_http_api_RequestParametersInterface::_ => tubepress_core_http_api_RequestParametersInterface::_,
            tubepress_api_log_LoggerInterface::_ => tubepress_api_log_LoggerInterface::_,
            tubepress_core_event_api_EventDispatcherInterface::_ => tubepress_core_event_api_EventDispatcherInterface::_,
            tubepress_core_template_api_TemplateFactoryInterface::_ => tubepress_core_template_api_TemplateFactoryInterface::_,
            'tubepress_core_media_gallery_impl_listeners_html_GalleryMaker' => 'tubepress_core_media_gallery_impl_listeners_html_GalleryMaker'
        );
    }
}