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
 * @covers tubepress_logger_ioc_LoggerExtension
 */
class tubepress_test_logger_ioc_LoggerExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_logger_ioc_LoggerExtension
     */
    protected function buildSut()
    {
        return  new tubepress_logger_ioc_LoggerExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            tubepress_platform_api_log_LoggerInterface::_,
            'tubepress_logger_impl_HtmlLogger'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
         ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_http_RequestParametersInterface::_));
    }

    protected function getExpectedExternalServicesMap()
    {
        $requestParams = $this->mock(tubepress_lib_api_http_RequestParametersInterface::_);
        $requestParams->shouldReceive('hasParam')->once()->with('tubepress_debug')->andReturn(true);
        $requestParams->shouldReceive('getParamValue')->once()->with('tubepress_debug')->andReturn('true');

        $context = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $context->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::DEBUG_ON)->andReturn(true);

        return array(
            tubepress_app_api_options_ContextInterface::_        => $context,
            tubepress_lib_api_http_RequestParametersInterface::_ => $requestParams,
        );
    }
}
