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
class tubepress_core_http_ioc_HttpExtension implements tubepress_api_ioc_ContainerExtensionInterface
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
     * @since 4.0.0
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            tubepress_core_http_api_oauth_v1_ClientInterface::_,
            'tubepress_core_http_impl_oauth_v1_Client'
        );

        $containerBuilder->register(
            'puzzle.httpClient',
            'puzzle_Client'
        );

        $containerBuilder->register(
            tubepress_core_http_api_HttpClientInterface::_,
            'tubepress_core_http_impl_puzzle_PuzzleHttpClient'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('puzzle.httpClient'));

        $containerBuilder->register(
            'tubepress_core_http_impl_PlayerAjaxCommand',
            'tubepress_core_http_impl_PlayerAjaxCommand'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_player_api_PlayerHtmlInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_media_provider_api_CollectorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_ResponseCodeInterface::_))
         ->addTag(tubepress_core_http_api_AjaxCommandInterface::_);

        $containerBuilder->register(
            tubepress_core_http_api_AjaxCommandInterface::_,
            'tubepress_core_http_impl_PrimaryAjaxHandler'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_ResponseCodeInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
           'tag'    => tubepress_core_http_api_AjaxCommandInterface::_,
           'method' => 'setAjaxCommands',
        ));

        $containerBuilder->register(
            tubepress_core_http_api_RequestParametersInterface::_,
            'tubepress_core_http_impl_RequestParameters'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_));

        $containerBuilder->register(
            tubepress_core_http_api_ResponseCodeInterface::_,
            'tubepress_core_http_impl_ResponseCode'
        );

        $containerBuilder->setParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_http', array(

            'defaultValues' => array(
                tubepress_core_http_api_Constants::OPTION_HTTP_METHOD => 'GET',
            ),

            'labels' => array(
                tubepress_core_http_api_Constants::OPTION_HTTP_METHOD => 'HTTP method',        //>(translatable)<
            ),

            'descriptions' => array(
                tubepress_core_http_api_Constants::OPTION_HTTP_METHOD => 'Defines the HTTP method used in most TubePress Ajax operations',  //>(translatable)<
            )
        ));

        $containerBuilder->setParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_ACCEPTABLE_VALUES, array(

            'optionName' => tubepress_core_http_api_Constants::OPTION_HTTP_METHOD,
            'priority'   => 30000,
            'values'     => array(
                'GET'  => 'GET',
                'POST' => 'POST'
            )
        ));
    }
}
