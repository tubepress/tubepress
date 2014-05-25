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
class tubepress_core_impl_http_HttpExtension implements tubepress_api_ioc_ContainerExtensionInterface
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

            tubepress_core_api_http_AjaxCommandInterface::_,
            'tubepress_core_impl_http_PrimaryAjaxHandler'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_ResponseCodeInterface::_));

        $containerBuilder->register(

            tubepress_core_api_http_RequestParametersInterface::_,
            'tubepress_core_impl_http_RequestParameters'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_));

        $containerBuilder->register(

            tubepress_core_api_http_ResponseCodeInterface::_,
            'tubepress_core_impl_http_ResponseCode'
        );

        $containerBuilder->register(

            'tubepress_core_impl_http_PlayerAjaxCommand',
            'tubepress_core_impl_http_PlayerAjaxCommand'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_player_PlayerHtmlInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_collector_CollectorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_ResponseCodeInterface::_));

        $containerBuilder->register(

            'puzzle.httpClient',
            'puzzle_Client'
        );

        $containerBuilder->register(

            tubepress_core_api_http_HttpClientInterface::_,
            'tubepress_core_impl_http_puzzle_PuzzleHttpClient'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('puzzle.httpClient'));

        $containerBuilder->register(

            tubepress_core_api_http_oauth_v1_ClientInterface::_,
            'tubepress_core_impl_http_oauth_v1_Client'
        );
    }
}
