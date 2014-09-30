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
 * @covers tubepress_lib_ioc_LibExtension
 */
class tubepress_test_lib_ioc_LibExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
{

    /**
     * @return tubepress_lib_ioc_LibExtension
     */
    protected function buildSut()
    {
        return  new tubepress_lib_ioc_LibExtension();
    }

    protected function prepareForLoad()
    {
        $this->_expectEventDispatcher();
        $this->_expectHttpClient();
        $this->_expectUrlFactory();
        $this->_expectUtils();
    }
    
    private function _expectEventDispatcher()
    {
        $this->expectRegistration(

            'ehough_tickertape_ContainerAwareEventDispatcher',
            'ehough_tickertape_ContainerAwareEventDispatcher'

        )->withArgument(new tubepress_platform_api_ioc_Reference('ehough_iconic_ContainerInterface'));

        $this->expectRegistration(

            tubepress_lib_api_event_EventDispatcherInterface::_,
            'tubepress_lib_impl_event_tickertape_EventDispatcher'

        )->withArgument(new tubepress_platform_api_ioc_Reference('ehough_tickertape_ContainerAwareEventDispatcher'));
    }

    protected function getExpectedExternalServicesMap()
    {
        $logger = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);

        return array(

            'ehough_iconic_ContainerInterface'            => 'ehough_iconic_ContainerInterface',
            tubepress_platform_api_log_LoggerInterface::_ => $logger,
        );
    }

    private function _expectHttpClient()
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

            $emitterDef->withMethodCall('attach', array(new tubepress_platform_api_ioc_Reference('puzzle_subscriber_Chunked')));
        }

        $this->expectRegistration(
            tubepress_lib_api_http_oauth_v1_ClientInterface::_,
            'tubepress_lib_impl_http_oauth_v1_Client'
        );

        $this->expectRegistration(
            'puzzle.httpClient',
            'puzzle_Client'
        )->withArgument(array('emitter' => new tubepress_platform_api_ioc_Reference('puzzle_event_Emitter')));

        $this->expectRegistration(
            tubepress_lib_api_http_HttpClientInterface::_,
            'tubepress_lib_impl_http_puzzle_PuzzleHttpClient'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference('puzzle.httpClient'));

        $this->expectRegistration(
            tubepress_lib_api_http_ResponseCodeInterface::_,
            'tubepress_lib_impl_http_ResponseCode'
        );
    }

    private function _expectUrlFactory()
    {
        $this->expectRegistration(

            tubepress_platform_api_url_UrlFactoryInterface::_,
            'tubepress_platform_impl_url_puzzle_UrlFactory'
        );
    }

    private function _expectUtils()
    {
        $this->expectRegistration(
            tubepress_lib_api_util_TimeUtilsInterface::_,
            'tubepress_lib_impl_util_TimeUtils'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_StringUtilsInterface::_));

        $this->expectRegistration(
            tubepress_platform_api_util_LangUtilsInterface::_,
            'tubepress_platform_impl_util_LangUtils'
        );

        $this->expectRegistration(
            tubepress_platform_api_util_StringUtilsInterface::_,
            'tubepress_platform_impl_util_StringUtils'
        );
    }
}
