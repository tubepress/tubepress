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
 * @covers tubepress_lib_util_ioc_UtilsExtension
 */
class tubepress_test_lib_util_ioc_UtilsExtensionTest extends tubepress_test_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_lib_util_ioc_UtilsExtension
     */
    protected function buildSut()
    {
        return  new tubepress_lib_util_ioc_UtilsExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            tubepress_lib_util_api_UrlUtilsInterface::_,
            'tubepress_lib_util_impl_UrlUtils'
        );

        $this->expectRegistration(
            tubepress_lib_util_api_TimeUtilsInterface::_,
            'tubepress_lib_util_impl_TimeUtils'
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

    protected function getExpectedExternalServicesMap()
    {
        return array();
    }
}
