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
class tubepress_lib_http_ioc_HttpExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
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
            tubepress_lib_http_api_oauth_v1_ClientInterface::_,
            'tubepress_lib_http_impl_oauth_v1_Client'
        );

        $containerBuilder->register(
            'puzzle.httpClient',
            'puzzle_Client'
        );

        $containerBuilder->register(
            tubepress_lib_http_api_HttpClientInterface::_,
            'tubepress_lib_http_impl_puzzle_PuzzleHttpClient'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_environment_api_EnvironmentInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference('puzzle.httpClient'));

        $containerBuilder->register(
            tubepress_lib_http_api_ResponseCodeInterface::_,
            'tubepress_lib_http_impl_ResponseCode'
        );
    }
}