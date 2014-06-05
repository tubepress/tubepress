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
 *
 */
class tubepress_core_html_search_ioc_SearchExtension implements tubepress_api_ioc_ContainerExtensionInterface
{

    /**
     * Called during construction of the TubePress service container. If an add-on intends to add
     * services to the container, it should do so here. The incoming `tubepress_api_ioc_ContainerBuilderInterface`
     * will be completely empty, and after this method is executed will be merged into the primary service container.
     *
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder An empty `tubepress_api_ioc_ContainerBuilderInterface` instance.
     *
     * @return void
     *
     * @api
     * @since 3.2.0
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_core_html_search_impl_listeners_html_SearchInputListener',
            'tubepress_core_html_search_impl_listeners_html_SearchInputListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_template_api_TemplateFactoryInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_html_api_Constants::EVENT_PRIMARY_HTML,
            'method'   => 'onHtmlGeneration',
            'priority' => 10000
        ));

        $containerBuilder->register(

            'tubepress_core_html_search_impl_listeners_html_SearchOutputListener',
            'tubepress_core_html_search_impl_listeners_html_SearchOutputListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_core_html_gallery_impl_listeners_html_GalleryMaker'))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_html_api_Constants::EVENT_PRIMARY_HTML,
            'method'   => 'onHtmlGeneration',
            'priority' => 9000
        ));

        $containerBuilder->register(

            'tubepress_core_html_search_impl_listeners_options_AcceptableValues',
            'tubepress_core_html_search_impl_listeners_options_AcceptableValues'
        )->addTag(tubepress_core_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_core_media_provider_api_MediaProviderInterface::_,
            'method' => 'setVideoProviders'
        ))->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_options_api_Constants::EVENT_OPTION_GET_ACCEPTABLE_VALUES . '.' . tubepress_core_html_search_api_Constants::OPTION_SEARCH_PROVIDER,
            'method'   => 'onAcceptableValues',
            'priority' => 30000
        ));

        $containerBuilder->register(

            'tubepress_core_html_search_impl_listeners_template_SearchInputCoreVariables',
            'tubepress_core_html_search_impl_listeners_template_SearchInputCoreVariables'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_url_api_UrlFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_html_search_api_Constants::EVENT_TEMPLATE_SEARCH_INPUT,
            'method'   => 'onSearchInputTemplate',
            'priority' => 10000
        ));

        $containerBuilder->setParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_search', array(

            'defaultValues' => array(
                tubepress_core_html_search_api_Constants::OPTION_SEARCH_ONLY_USER    => null,
                tubepress_core_html_search_api_Constants::OPTION_SEARCH_PROVIDER     => null,
                tubepress_core_html_search_api_Constants::OPTION_SEARCH_RESULTS_ONLY => false,
                tubepress_core_html_search_api_Constants::OPTION_SEARCH_RESULTS_URL  => null,
            ),

            'labels' => array(
                tubepress_core_html_search_api_Constants::OPTION_SEARCH_ONLY_USER => 'Restrict search results to videos from author', //>(translatable)<
            ),

            'descriptions' => array(
                tubepress_core_html_search_api_Constants::OPTION_SEARCH_ONLY_USER => 'A YouTube or Vimeo user name. Only applies to search-based galleries.',      //>(translatable)<
            )
        ));

        $containerBuilder->setParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_VALIDATION . '_search', array(

            'priority' => 30000,
            'map'      => array(
                'zeroOrMoreWordChars' => array(
                    tubepress_core_html_search_api_Constants::OPTION_SEARCH_ONLY_USER
                )
            )
        ));
    }
}