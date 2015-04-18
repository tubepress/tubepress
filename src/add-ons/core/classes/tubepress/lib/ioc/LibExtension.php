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
 *
 */
class tubepress_lib_ioc_LibExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
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
        $this->_registerArrayReader($containerBuilder);
        $this->_registerEventDispatcher($containerBuilder);
        $this->_registerHttpClient($containerBuilder);
        $this->_registerUrlFactory($containerBuilder);
        $this->_registerUtils($containerBuilder);
    }

    private function _registerArrayReader(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_lib_api_array_ArrayReaderInterface::_,
            'tubepress_lib_impl_array_ArrayReader'
        );
    }

    private function _registerEventDispatcher(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'ehough_tickertape_ContainerAwareEventDispatcher',
            'ehough_tickertape_ContainerAwareEventDispatcher'
        )->addArgument(new tubepress_platform_api_ioc_Reference('ehough_iconic_ContainerInterface'));

        $containerBuilder->register(
            tubepress_lib_api_event_EventDispatcherInterface::_,
            'tubepress_lib_impl_event_tickertape_EventDispatcher'
        )->addArgument(new tubepress_platform_api_ioc_Reference('ehough_tickertape_ContainerAwareEventDispatcher'));
    }

    private function _registerHttpClient(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $emitterDef = $containerBuilder->register(
            'puzzle_event_Emitter',
            'puzzle_event_Emitter'
        );

        if (version_compare(PHP_VERSION, '5.3.0') < 0) {

            $containerBuilder->register(
                'puzzle_subscriber_Chunked',
                'puzzle_subscriber_Chunked'
            );

            $emitterDef->addMethodCall('attach', array(new tubepress_platform_api_ioc_Reference('puzzle_subscriber_Chunked')));
        }

        $containerBuilder->register(
            tubepress_lib_api_http_oauth_v1_ClientInterface::_,
            'tubepress_lib_impl_http_oauth_v1_Client'
        );

        $containerBuilder->register(
            'puzzle.httpClient',
            'puzzle_Client'
        )->addArgument(array('emitter' => new tubepress_platform_api_ioc_Reference('puzzle_event_Emitter')));

        $containerBuilder->register(
            tubepress_lib_api_http_HttpClientInterface::_,
            'tubepress_lib_impl_http_puzzle_PuzzleHttpClient'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference('puzzle.httpClient'))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_));

        $containerBuilder->register(
            tubepress_lib_api_http_ResponseCodeInterface::_,
            'tubepress_lib_impl_http_ResponseCode'
        );
    }

    private function _registerUrlFactory(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            tubepress_platform_api_url_UrlFactoryInterface::_,
            'tubepress_platform_impl_url_puzzle_UrlFactory'
        );
    }

    private function _registerUtils(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            tubepress_lib_api_util_TimeUtilsInterface::_,
            'tubepress_lib_impl_util_TimeUtils'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_StringUtilsInterface::_));

        $containerBuilder->register(
            tubepress_platform_api_util_LangUtilsInterface::_,
            'tubepress_platform_impl_util_LangUtils'
        );

        $containerBuilder->register(
            tubepress_platform_api_util_StringUtilsInterface::_,
            'tubepress_platform_impl_util_StringUtils'
        );
    }
}