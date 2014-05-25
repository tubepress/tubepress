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
 * @covers tubepress_core_impl_util_UtilsExtension
 */
class tubepress_test_core_impl_util_UtilsExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_core_impl_util_UtilsExtension
     */
    protected function buildSut()
    {
        return  new tubepress_core_impl_util_UtilsExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            tubepress_core_api_util_UrlUtilsInterface::_,
            'tubepress_core_impl_util_UrlUtils'
        );

        $this->expectRegistration(

            tubepress_core_api_util_TimeUtilsInterface::_,
            'tubepress_core_impl_util_TimeUtils'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_));
    }

    protected function getExpectedServiceContructionMap()
    {
        return array(

            tubepress_core_api_util_UrlUtilsInterface::_ => 'tubepress_core_impl_util_UrlUtils',
            tubepress_core_api_util_TimeUtilsInterface::_ => 'tubepress_core_impl_util_TimeUtils'
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(

            tubepress_api_util_StringUtilsInterface::_ => tubepress_api_util_StringUtilsInterface::_
        );
    }
}
