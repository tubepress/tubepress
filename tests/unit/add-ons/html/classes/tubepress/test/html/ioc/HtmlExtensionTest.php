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
 * @covers tubepress_html_ioc_HtmlExtension
 */
class tubepress_test_html_ioc_HtmlExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_html_ioc_HtmlExtension
     */
    protected function buildSut()
    {
        return  new tubepress_html_ioc_HtmlExtension();
    }

    protected function prepareForLoad()
    {
        $this->_registerListeners();

        $this->expectRegistration(
            'tubepress_html_impl_CssAndJsGenerationHelper',
            'tubepress_html_impl_CssAndJsGenerationHelper'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_contrib_RegistryInterface::_ . '.' . tubepress_app_api_theme_ThemeInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference('tubepress_theme_impl_CurrentThemeService'))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_environment_EnvironmentInterface::_))
            ->withArgument(tubepress_app_api_event_Events::HTML_STYLESHEETS)
            ->withArgument(tubepress_app_api_event_Events::HTML_SCRIPTS)
            ->withArgument('cssjs/styles')
            ->withArgument('cssjs/scripts');

        $this->expectRegistration(
            tubepress_app_api_html_HtmlGeneratorInterface::_,
            'tubepress_html_impl_HtmlGenerator'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference('tubepress_html_impl_CssAndJsGenerationHelper'))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_environment_EnvironmentInterface::_))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::HTML_SCRIPTS,
                'priority' => 100000,
                'method'   => 'onScripts',
            ));
    }

    private function _registerListeners()
    {
        $this->expectRegistration(
            'tubepress_html_impl_listeners_ExceptionLogger',
            'tubepress_html_impl_listeners_ExceptionLogger'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    =>   tubepress_app_api_event_Events::HTML_EXCEPTION_CAUGHT,
                'priority' => 100000,
                'method'   => 'onException'
            ));
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(
            tubepress_lib_api_event_EventDispatcherInterface::_   => tubepress_lib_api_event_EventDispatcherInterface::_,
            tubepress_lib_api_template_TemplatingInterface::_     => tubepress_lib_api_template_TemplatingInterface::_,
            tubepress_app_api_environment_EnvironmentInterface::_ => tubepress_app_api_environment_EnvironmentInterface::_,
            'tubepress_theme_impl_CurrentThemeService'            => 'tubepress_theme_impl_CurrentThemeService',
            tubepress_platform_api_contrib_RegistryInterface::_ . '.' . tubepress_app_api_theme_ThemeInterface::_ => tubepress_platform_api_contrib_RegistryInterface::_,
            tubepress_platform_api_log_LoggerInterface::_         => tubepress_platform_api_log_LoggerInterface::_,
        );
    }
}
