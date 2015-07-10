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
 * @covers tubepress_shortcode_ioc_ShortcodeExtension
 */
class tubepress_test_shortcode_ioc_ShortcodeExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_shortcode_ioc_ShortcodeExtension
     */
    protected function buildSut()
    {
        return  new tubepress_shortcode_ioc_ShortcodeExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            tubepress_app_api_shortcode_ParserInterface::_,
            'tubepress_shortcode_impl_Parser'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
         ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
         ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
         ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_StringUtilsInterface::_));
    }

    protected function getExpectedExternalServicesMap()
    {
        $logger = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->once()->andReturn(true);

        return array(
            tubepress_platform_api_log_LoggerInterface::_       => $logger,
            tubepress_app_api_options_ContextInterface::_       => tubepress_app_api_options_ContextInterface::_,
            tubepress_lib_api_event_EventDispatcherInterface::_ => tubepress_lib_api_event_EventDispatcherInterface::_,
            tubepress_platform_api_util_StringUtilsInterface::_ => tubepress_platform_api_util_StringUtilsInterface::_
        );
    }
}