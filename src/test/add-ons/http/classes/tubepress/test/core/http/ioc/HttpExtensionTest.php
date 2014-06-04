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
 * @covers tubepress_core_http_ioc_HttpExtension
 */
class tubepress_test_core_http_impl_HttpExtensionTest extends tubepress_test_core_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_core_http_ioc_HttpExtension
     */
    protected function buildSut()
    {
        return  new tubepress_core_http_ioc_HttpExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            tubepress_core_http_api_oauth_v1_ClientInterface::_,
            'tubepress_core_http_impl_oauth_v1_Client'
        );

        $this->expectRegistration(
            'puzzle.httpClient',
            'puzzle_Client'
        );

        $this->expectRegistration(
            tubepress_core_http_api_HttpClientInterface::_,
            'tubepress_core_http_impl_puzzle_PuzzleHttpClient'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('puzzle.httpClient'));

        $this->expectRegistration(
            'tubepress_core_http_impl_PlayerAjaxCommand',
            'tubepress_core_http_impl_PlayerAjaxCommand'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_player_api_PlayerHtmlInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_provider_api_CollectorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_ResponseCodeInterface::_));

        $this->expectRegistration(
            tubepress_core_http_api_AjaxCommandInterface::_,
            'tubepress_core_http_impl_PrimaryAjaxHandler'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_ResponseCodeInterface::_));

        $this->expectRegistration(
            tubepress_core_http_api_RequestParametersInterface::_,
            'tubepress_core_http_impl_RequestParameters'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_));

        $this->expectRegistration(
            tubepress_core_http_api_ResponseCodeInterface::_,
            'tubepress_core_http_impl_ResponseCode'
        );

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_http', array(

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

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_ACCEPTABLE_VALUES, array(

            'optionName' => tubepress_core_http_api_Constants::OPTION_HTTP_METHOD,
            'priority'   => 30000,
            'values'     => array(
                'GET'  => 'GET',
                'POST' => 'POST'
            )
        ));
    }

    protected function getExpectedExternalServicesMap()
    {
        $logger = $this->mock(tubepress_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);

        return array(

            tubepress_api_log_LoggerInterface::_ => $logger,
            tubepress_core_options_api_ContextInterface::_ => tubepress_core_options_api_ContextInterface::_,
            tubepress_core_player_api_PlayerHtmlInterface::_ => tubepress_core_player_api_PlayerHtmlInterface::_,
            tubepress_core_provider_api_CollectorInterface::_ => tubepress_core_provider_api_CollectorInterface::_,
            tubepress_core_event_api_EventDispatcherInterface::_ => tubepress_core_event_api_EventDispatcherInterface::_,
        );
    }
}
