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
 * @covers tubepress_core_impl_collector_CollectorExtension
 */
class tubepress_test_core_impl_collector_CollectorExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{
    /**
     * @return tubepress_core_impl_collector_CollectorExtension
     */
    protected function buildSut()
    {
        return new tubepress_core_impl_collector_CollectorExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            tubepress_core_api_collector_CollectorInterface::_,
            'tubepress_core_impl_collector_Collector'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_http_RequestParametersInterface::_));
    }

    protected function getExpectedServiceContructionMap()
    {
        return array(

            tubepress_core_api_collector_CollectorInterface::_ => 'tubepress_core_impl_collector_Collector',
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        $logger = $this->mock(tubepress_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->once()->andReturn(true);

        return array(

            tubepress_api_log_LoggerInterface::_                  => $logger,
            tubepress_core_api_options_ContextInterface::_        => tubepress_core_api_options_ContextInterface::_,
            tubepress_core_api_event_EventDispatcherInterface::_  => tubepress_core_api_event_EventDispatcherInterface::_,
            tubepress_core_api_http_RequestParametersInterface::_ => tubepress_core_api_http_RequestParametersInterface::_,
        );
    }
}