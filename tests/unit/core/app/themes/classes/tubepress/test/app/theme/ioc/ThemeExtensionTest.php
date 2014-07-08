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
 * @covers tubepress_app_theme_ioc_ThemeExtension
 */
class tubepress_test_app_theme_ioc_ThemeExtensionTest extends tubepress_test_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_app_theme_ioc_ThemeExtension
     */
    protected function buildSut()
    {
        return  new tubepress_app_theme_ioc_ThemeExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            tubepress_app_theme_api_ThemeLibraryInterface::_,
            'tubepress_app_theme_impl_ThemeLibrary'
        )->withArgument('%themes%')
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_environment_api_EnvironmentInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_url_api_UrlFactoryInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_LangUtilsInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_));

        $this->expectRegistration(
            'tubepress_app_theme_impl_ThemeRegistry',
            'tubepress_app_theme_impl_ThemeRegistry'
        )->withArgument(new tubepress_platform_api_ioc_Reference('tubepress_platform_impl_log_BootLogger'))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_boot_BootSettingsInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference('ehough_finder_FinderFactoryInterface'))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_contrib_api_ContributableValidatorInterface::_))
            ->withTag(tubepress_platform_api_contrib_RegistryInterface::_, array('type' => 'tubepress_app_theme_api_ThemeInterface'));

        $this->expectRegistration(
            'tubepress_app_theme_impl_listeners_options_LegacyThemeListener',
            'tubepress_app_theme_impl_listeners_options_LegacyThemeListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_theme_api_ThemeLibraryInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_options_api_Constants::EVENT_OPTION_SET . '.' . tubepress_app_theme_api_Constants::OPTION_THEME,
                'method'   => 'onPreValidationSet',
                'priority' => 300000
            ));

        $this->expectRegistration(
            'tubepress_app_theme_impl_listeners_options_AcceptableValues',
            'tubepress_app_theme_impl_listeners_options_AcceptableValues'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_theme_api_ThemeLibraryInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_options_api_Constants::EVENT_OPTION_GET_ACCEPTABLE_VALUES . '.' . tubepress_app_theme_api_Constants::OPTION_THEME,
                'method'   => 'onAcceptableValues',
                'priority' => 30000
            ));

        $this->expectParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_theme', array(

            'defaultValues' => array(
                tubepress_app_theme_api_Constants::OPTION_THEME => 'tubepress/default',
            ),

            'labels' => array(
                tubepress_app_theme_api_Constants::OPTION_THEME => 'Theme',  //>(translatable)<
            )
        ));

        $this->expectRegistration(
            'theme_field',
            'tubepress_app_options_ui_api_FieldInterface'
        )->withFactoryService(tubepress_app_options_ui_api_FieldBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_app_theme_api_Constants::OPTION_THEME)
            ->withArgument('theme');

        $this->expectRegistration(
            'theme_category',
            'tubepress_app_options_ui_api_ElementInterface'
        )->withFactoryService(tubepress_app_options_ui_api_ElementBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_app_theme_api_Constants::OPTIONS_UI_CATEGORY_THEMES)
            ->withArgument('Theme');

        $fieldMap = array(
            tubepress_app_theme_api_Constants::OPTIONS_UI_CATEGORY_THEMES => array(
                tubepress_app_theme_api_Constants::OPTION_THEME
            )
        );

        $this->expectRegistration(

            'tubepress_app_theme_impl_options_ui_FieldProvider',
            'tubepress_app_theme_impl_options_ui_FieldProvider'
        )->withArgument(array(new tubepress_platform_api_ioc_Reference('theme_category')))
            ->withArgument(array(new tubepress_platform_api_ioc_Reference('theme_field')))
            ->withArgument($fieldMap)
            ->withTag('tubepress_app_options_ui_api_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $logger = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $bootLogger = $this->mock('tubepress_platform_impl_log_BootLogger');
        $logger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $bootLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);

        $mockField = $this->mock('tubepress_app_options_ui_api_FieldInterface');
        $fieldBuilder = $this->mock(tubepress_app_options_ui_api_FieldBuilderInterface::_);
        $fieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        $mockCategory = $this->mock('tubepress_app_options_ui_api_ElementInterface');
        $elementBuilder = $this->mock(tubepress_app_options_ui_api_ElementBuilderInterface::_);
        $elementBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockCategory);

        return array(
            tubepress_app_options_api_ContextInterface::_ => tubepress_app_options_api_ContextInterface::_,
            tubepress_app_environment_api_EnvironmentInterface::_ => tubepress_app_environment_api_EnvironmentInterface::_,
            tubepress_lib_url_api_UrlFactoryInterface::_ => tubepress_lib_url_api_UrlFactoryInterface::_,
            tubepress_platform_api_util_LangUtilsInterface::_ => tubepress_platform_api_util_LangUtilsInterface::_,
            tubepress_platform_api_log_LoggerInterface::_ => $logger,
            tubepress_platform_api_boot_BootSettingsInterface::_ => tubepress_platform_api_boot_BootSettingsInterface::_,
            'ehough_finder_FinderFactoryInterface' => 'ehough_finder_FinderFactoryInterface',
            tubepress_app_contrib_api_ContributableValidatorInterface::_ => tubepress_app_contrib_api_ContributableValidatorInterface::_,
            'tubepress_platform_impl_log_BootLogger' => $bootLogger,
            tubepress_app_options_ui_api_ElementBuilderInterface::_ => $elementBuilder,
            tubepress_app_options_ui_api_FieldBuilderInterface::_ => $fieldBuilder
        );
    }

    protected function getExpectedParameterMap()
    {
        return array('themes' => array('boo'));
    }
}
