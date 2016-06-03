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

class tubepress_http_ioc_HttpExtension implements tubepress_spi_ioc_ContainerExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerClient($containerBuilder);
        $this->_registerMiscServices($containerBuilder);
        $this->_registerListeners($containerBuilder);
    }

    private function _registerClient(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'puzzle_event_Emitter',
            'puzzle_event_Emitter'
        );

        $containerBuilder->register(
            'puzzle.httpClient',
            'puzzle_Client'
        )->addArgument(array('emitter' => new tubepress_api_ioc_Reference('puzzle_event_Emitter')));

        $containerBuilder->register(
            tubepress_api_http_HttpClientInterface::_,
            'tubepress_http_impl_puzzle_PuzzleHttpClient'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('puzzle.httpClient'))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_));
    }

    private function _registerMiscServices(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            tubepress_api_http_ResponseCodeInterface::_,
            'tubepress_http_impl_ResponseCode'
        );

        $containerBuilder->register(
            tubepress_api_http_AjaxInterface::_,
            'tubepress_http_impl_PrimaryAjaxHandler'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_ResponseCodeInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_));

        $containerBuilder->register(
         tubepress_api_http_RequestParametersInterface::_,
         'tubepress_http_impl_RequestParameters'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_));
    }

    private function _registerListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_http_impl_listeners_UserAgentListener',
            'tubepress_http_impl_listeners_UserAgentListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_http_Events::EVENT_HTTP_REQUEST,
            'priority' => 100000,
            'method'   => 'onRequest',
        ));
    }
}
