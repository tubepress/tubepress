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
 * @covers tubepress_core_impl_http_HttpExtension
 */
class tubepress_test_core_impl_http_HttpExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_core_impl_http_HttpExtension
     */
    protected function buildSut()
    {
        return  new tubepress_core_impl_http_HttpExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            tubepress_core_api_http_AjaxCommandInterface::_,
            'tubepress_core_impl_http_PrimaryAjaxHandler'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_ResponseCodeInterface::_));

        $this->expectRegistration(

            tubepress_core_api_http_RequestParametersInterface::_,
            'tubepress_core_impl_http_RequestParameters'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_));

        $this->expectRegistration(

            tubepress_core_api_http_ResponseCodeInterface::_,
            'tubepress_core_impl_http_ResponseCode'
        );

        $this->expectRegistration(

            'tubepress_core_impl_http_PlayerAjaxCommand',
            'tubepress_core_impl_http_PlayerAjaxCommand'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_player_PlayerHtmlInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_collector_CollectorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_ResponseCodeInterface::_));

        $this->expectRegistration(

            'puzzle.httpClient',
            'puzzle_Client'
        );

        $this->expectRegistration(

            tubepress_core_api_http_HttpClientInterface::_,
            'tubepress_core_impl_http_puzzle_PuzzleHttpClient'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference('puzzle.httpClient'));

        $this->expectRegistration(

            tubepress_core_api_http_oauth_v1_ClientInterface::_,
            'tubepress_core_impl_http_oauth_v1_Client'
        );
    }

    protected function getExpectedServiceContructionMap()
    {
        return array(

            tubepress_core_api_http_AjaxCommandInterface::_ => 'tubepress_core_impl_http_PrimaryAjaxHandler',
            tubepress_core_api_http_RequestParametersInterface::_ => 'tubepress_core_impl_http_RequestParameters',
            tubepress_core_api_http_ResponseCodeInterface::_ => 'tubepress_core_impl_http_ResponseCode',
            'tubepress_core_impl_http_PlayerAjaxCommand' => 'tubepress_core_impl_http_PlayerAjaxCommand',
            tubepress_core_api_http_HttpClientInterface::_ => tubepress_core_api_http_HttpClientInterface::_,
            tubepress_core_api_http_oauth_v1_ClientInterface::_ => 'tubepress_core_impl_http_oauth_v1_Client'
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        $logger = $this->mock(tubepress_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);

        return array(

            tubepress_api_log_LoggerInterface::_ => $logger,
            tubepress_core_api_options_ContextInterface::_ => tubepress_core_api_options_ContextInterface::_,
            tubepress_core_api_player_PlayerHtmlInterface::_ => tubepress_core_api_player_PlayerHtmlInterface::_,
            tubepress_core_api_collector_CollectorInterface::_ => tubepress_core_api_collector_CollectorInterface::_,
            tubepress_core_api_event_EventDispatcherInterface::_ => tubepress_core_api_event_EventDispatcherInterface::_,
        );
    }
}
