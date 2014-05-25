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
 * @covers tubepress_core_impl_theme_ThemeExtension
 */
class tubepress_test_core_impl_theme_ThemeExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_core_impl_theme_ThemeExtension
     */
    protected function buildSut()
    {
        return  new tubepress_core_impl_theme_ThemeExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            tubepress_core_api_theme_ThemeLibraryInterface::_,
            'tubepress_core_impl_theme_ThemeLibrary'
        )->withArgument('%themes%')
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_environment_EnvironmentInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_url_UrlFactoryInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_));

        $this->expectRegistration(

            'tubepress_core_impl_theme_ThemeRegistry',
            'tubepress_core_impl_theme_ThemeRegistry'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_boot_BootSettingsInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference('ehough_finder_FinderFactoryInterface'))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_contrib_ContributableValidatorInterface::_))
         ->withTag(tubepress_api_contrib_RegistryInterface::_, array('type' => 'tubepress_core_api_theme_ThemeInterface'));
    }

    protected function getExpectedServiceContructionMap()
    {
        return array(

            tubepress_core_api_theme_ThemeLibraryInterface::_ => 'tubepress_core_impl_theme_ThemeLibrary',
            'tubepress_core_impl_theme_ThemeRegistry' => 'tubepress_core_impl_theme_ThemeRegistry'
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        $logger = $this->mock(tubepress_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);

        return array(
            tubepress_core_api_options_ContextInterface::_ => tubepress_core_api_options_ContextInterface::_,
            tubepress_core_api_environment_EnvironmentInterface::_ => tubepress_core_api_environment_EnvironmentInterface::_,
            tubepress_core_api_url_UrlFactoryInterface::_ => tubepress_core_api_url_UrlFactoryInterface::_,
            tubepress_api_util_LangUtilsInterface::_ => tubepress_api_util_LangUtilsInterface::_,
            tubepress_api_log_LoggerInterface::_ => $logger,
            tubepress_api_boot_BootSettingsInterface::_ => tubepress_api_boot_BootSettingsInterface::_,
            'ehough_finder_FinderFactoryInterface' => 'ehough_finder_FinderFactoryInterface',
            tubepress_core_api_contrib_ContributableValidatorInterface::_ => tubepress_core_api_contrib_ContributableValidatorInterface::_
        );
    }

    protected function getExpectedParameterMap()
    {
        return array('themes' => array('boo'));
    }
}
