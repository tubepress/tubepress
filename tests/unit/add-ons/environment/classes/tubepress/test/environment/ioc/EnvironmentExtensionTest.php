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
 * @covers tubepress_environment_ioc_EnvironmentExtension
 */
class tubepress_test_environment_ioc_EnvironmentExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_array_ioc_ArrayExtension
     */
    protected function buildSut()
    {
        return  new tubepress_environment_ioc_EnvironmentExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            tubepress_app_api_environment_EnvironmentInterface::_,
            'tubepress_environment_impl_Environment'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_url_UrlFactoryInterface::_))
         ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_boot_BootSettingsInterface::_));
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(
            tubepress_platform_api_url_UrlFactoryInterface::_    => tubepress_platform_api_url_UrlFactoryInterface::_,
            tubepress_platform_api_boot_BootSettingsInterface::_ => tubepress_platform_api_boot_BootSettingsInterface::_,
        );
    }
}
