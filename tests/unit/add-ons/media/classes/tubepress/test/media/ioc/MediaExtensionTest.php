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
 * @covers tubepress_media_ioc_MediaExtension
 */
class tubepress_test_media_ioc_MediaExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_media_ioc_MediaExtension
     */
    protected function buildSut()
    {
        return  new tubepress_media_ioc_MediaExtension();
    }

    protected function prepareForLoad()
    {
       $this->expectRegistration(
            tubepress_app_api_media_AttributeFormatterInterface::_,
            'tubepress_media_impl_AttributeFormatter'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_util_TimeUtilsInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_translation_TranslatorInterface::_));

       $this->expectRegistration(
            tubepress_app_api_media_CollectorInterface::_,
            'tubepress_media_impl_Collector'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_environment_EnvironmentInterface::_));

       $this->expectRegistration(
            tubepress_app_api_media_HttpCollectorInterface::_,
            'tubepress_media_impl_HttpCollector'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_HttpClientInterface::_));
    }

    protected function getExpectedExternalServicesMap()
    {
        $logger = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);

        return array(

            tubepress_platform_api_log_LoggerInterface::_         => $logger,
            tubepress_lib_api_event_EventDispatcherInterface::_   => tubepress_lib_api_event_EventDispatcherInterface::_,
            tubepress_lib_api_http_HttpClientInterface::_         => tubepress_lib_api_http_HttpClientInterface::_,
            tubepress_app_api_options_ContextInterface::_         => tubepress_app_api_options_ContextInterface::_,
            tubepress_lib_api_util_TimeUtilsInterface::_          => tubepress_lib_api_util_TimeUtilsInterface::_,
            tubepress_lib_api_translation_TranslatorInterface::_  => tubepress_lib_api_translation_TranslatorInterface::_,
            tubepress_app_api_environment_EnvironmentInterface::_ => tubepress_app_api_environment_EnvironmentInterface::_,
        );
    }
}
