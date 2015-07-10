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
        $this->_expectUtils();
    }

    protected function getExpectedExternalServicesMap()
    {
        $logger = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);

        return array(

            tubepress_platform_api_log_LoggerInterface::_ => $logger,
            tubepress_lib_api_event_EventDispatcherInterface::_ => tubepress_lib_api_event_EventDispatcherInterface::_,
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
