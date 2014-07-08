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
 * @covers tubepress_app_environment_ioc_EnvironmentExtension
 */
class tubepress_test_app_environment_ioc_EnvironmentExtensionTest extends tubepress_test_ioc_AbstractIocContainerExtensionTest
{
    /**
     * @return tubepress_app_environment_ioc_EnvironmentExtension
     */
    protected function buildSut()
    {
        return new tubepress_app_environment_ioc_EnvironmentExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            tubepress_app_environment_api_EnvironmentInterface::_,
            'tubepress_app_environment_impl_Environment'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_url_api_UrlFactoryInterface::_))
         ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_boot_BootSettingsInterface::_));
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(

            tubepress_lib_url_api_UrlFactoryInterface::_ => tubepress_lib_url_api_UrlFactoryInterface::_,
            tubepress_platform_api_boot_BootSettingsInterface::_ => tubepress_platform_api_boot_BootSettingsInterface::_
        );
    }
}