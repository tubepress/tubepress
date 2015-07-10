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
 * @covers tubepress_util_ioc_UtilExtension
 */
class tubepress_test_util_ioc_UtilExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_util_ioc_UtilExtension
     */
    protected function buildSut()
    {
        return  new tubepress_util_ioc_UtilExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            tubepress_lib_api_util_TimeUtilsInterface::_,
            'tubepress_util_impl_TimeUtils'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_StringUtilsInterface::_));

        $this->expectRegistration(
            tubepress_platform_api_util_LangUtilsInterface::_,
            'tubepress_util_impl_LangUtils'
        );

        $this->expectRegistration(
            tubepress_platform_api_util_StringUtilsInterface::_,
            'tubepress_util_impl_StringUtils'
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        return array();
    }
}
