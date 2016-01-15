<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_http_ioc_HttpExtension
 */
class tubepress_test_http_ioc_HttpExtensionTest extends tubepress_api_test_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_array_ioc_ArrayExtension
     */
    protected function buildSut()
    {
        return  new tubepress_http_ioc_HttpExtension();
    }

    protected function prepareForLoad()
    {
        $this->_expectClient();
        $this->_expectMiscServices();
        $this->_expectListeners();
    }

    private function _expectClient()
    {
        $emitterDef = $this->expectRegistration(
            'puzzle_event_Emitter',
            'puzzle_event_Emitter'
        );

        if (version_compare(PHP_VERSION, '5.3.0') < 0) {

            $this->expectRegistration(
                'puzzle_subscriber_Chunked',
                'puzzle_subscriber_Chunked'
            );

            $emitterDef->withMethodCall('attach', array(new tubepress_api_ioc_Reference('puzzle_subscriber_Chunked')));
        }

        $this->expectRegistration(
            'puzzle.httpClient',
            'puzzle_Client'
        )->withArgument(array('emitter' => new tubepress_api_ioc_Reference('puzzle_event_Emitter')));

        $this->expectRegistration(
            tubepress_api_http_HttpClientInterface::_,
            'tubepress_http_impl_puzzle_PuzzleHttpClient'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('puzzle.httpClient'))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_));
    }

    private function _expectMiscServices()
    {
        $this->expectRegistration(
            tubepress_api_http_ResponseCodeInterface::_,
            'tubepress_http_impl_ResponseCode'
        );

        $this->expectRegistration(
            tubepress_api_http_AjaxInterface::_,
            'tubepress_http_impl_PrimaryAjaxHandler'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_ResponseCodeInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_));

        $this->expectRegistration(
            tubepress_api_http_RequestParametersInterface::_,
            'tubepress_http_impl_RequestParameters'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_));
    }

    private function _expectListeners()
    {
        $this->expectRegistration(
            'tubepress_http_impl_listeners_UserAgentListener',
            'tubepress_http_impl_listeners_UserAgentListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_http_Events::EVENT_HTTP_REQUEST,
                'priority' => 100000,
                'method'   => 'onRequest'
            ));
    }

    protected function getExpectedExternalServicesMap()
    {
        $logger = $this->mock(tubepress_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);

        return array(
            tubepress_api_event_EventDispatcherInterface::_ => tubepress_api_event_EventDispatcherInterface::_,
            tubepress_api_log_LoggerInterface::_       => $logger,
            tubepress_api_template_TemplatingInterface::_ => tubepress_api_template_TemplatingInterface::_,
            tubepress_api_environment_EnvironmentInterface::_ => tubepress_api_environment_EnvironmentInterface::_
        );
    }
}
