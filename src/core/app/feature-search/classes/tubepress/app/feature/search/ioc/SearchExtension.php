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
class tubepress_app_feature_search_ioc_SearchExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
{

    /**
     * Called during construction of the TubePress service container. If an add-on intends to add
     * services to the container, it should do so here. The incoming `tubepress_platform_api_ioc_ContainerBuilderInterface`
     * will be completely empty, and after this method is executed will be merged into the primary service container.
     *
     * @param tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder An empty `tubepress_platform_api_ioc_ContainerBuilderInterface` instance.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function load(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_app_feature_search_impl_listeners_html_SearchInputListener',
            'tubepress_app_feature_search_impl_listeners_html_SearchInputListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_template_api_TemplateFactoryInterface::_))
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_html_api_Constants::EVENT_PRIMARY_HTML,
            'method'   => 'onHtmlGeneration',
            'priority' => 10000
        ));

        $containerBuilder->register(

            'tubepress_app_feature_search_impl_listeners_html_SearchOutputListener',
            'tubepress_app_feature_search_impl_listeners_html_SearchOutputListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_RequestParametersInterface::_))
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
            'tag' => tubepress_app_media_provider_api_MediaProviderInterface::_,
            'method' => 'setMediaProviders'
        ))->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_html_api_Constants::EVENT_PRIMARY_HTML,
            'method'   => 'onHtmlGeneration',
            'priority' => 9000
        ));

        $containerBuilder->register(

            'tubepress_app_feature_search_impl_listeners_options_AcceptableValues',
            'tubepress_app_feature_search_impl_listeners_options_AcceptableValues'
        )->addTag(tubepress_lib_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_app_media_provider_api_MediaProviderInterface::_,
            'method' => 'setMediaProviders'
        ))->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_options_api_Constants::EVENT_OPTION_GET_ACCEPTABLE_VALUES . '.' . tubepress_app_feature_search_api_Constants::OPTION_SEARCH_PROVIDER,
            'method'   => 'onAcceptableValues',
            'priority' => 30000
        ));

        $containerBuilder->register(

            'tubepress_app_feature_search_impl_listeners_template_SearchInputCoreVariables',
            'tubepress_app_feature_search_impl_listeners_template_SearchInputCoreVariables'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_translation_api_TranslatorInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_url_api_UrlFactoryInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_RequestParametersInterface::_))
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_feature_search_api_Constants::EVENT_TEMPLATE_SEARCH_INPUT,
            'method'   => 'onSearchInputTemplate',
            'priority' => 10000
        ));

        $containerBuilder->setParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_search', array(

            'defaultValues' => array(
                tubepress_app_feature_search_api_Constants::OPTION_SEARCH_ONLY_USER    => null,
                tubepress_app_feature_search_api_Constants::OPTION_SEARCH_PROVIDER     => null,
                tubepress_app_feature_search_api_Constants::OPTION_SEARCH_RESULTS_ONLY => false,
                tubepress_app_feature_search_api_Constants::OPTION_SEARCH_RESULTS_URL  => null,
            ),

            'labels' => array(
                tubepress_app_feature_search_api_Constants::OPTION_SEARCH_ONLY_USER => 'Restrict search results to videos from author', //>(translatable)<
            ),

            'descriptions' => array(
                tubepress_app_feature_search_api_Constants::OPTION_SEARCH_ONLY_USER => 'A YouTube or Vimeo user name. Only applies to search-based galleries.',      //>(translatable)<
            )
        ));

        $containerBuilder->setParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_VALIDATION . '_search', array(

            'priority' => 30000,
            'map'      => array(
                'zeroOrMoreWordChars' => array(
                    tubepress_app_feature_search_api_Constants::OPTION_SEARCH_ONLY_USER
                )
            )
        ));
    }
}