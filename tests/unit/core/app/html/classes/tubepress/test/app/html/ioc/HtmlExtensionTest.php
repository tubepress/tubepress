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
 * @covers tubepress_app_html_ioc_HtmlExtension
 */
class tubepress_test_app_html_ioc_HtmlExtensionTest extends tubepress_test_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_app_html_ioc_HtmlExtension
     */
    protected function buildSut()
    {
        return  new tubepress_app_html_ioc_HtmlExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            'tubepress_app_html_impl_listeners_BaseUrlSetter',
            'tubepress_app_html_impl_listeners_BaseUrlSetter'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_environment_api_EnvironmentInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_util_api_UrlUtilsInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_html_api_Constants::EVENT_GLOBAL_JS_CONFIG,
                'method'   => 'onGlobalJsConfig',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_app_html_impl_listeners_ExceptionListener',
            'tubepress_app_html_impl_listeners_ExceptionListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_html_api_Constants::EVENT_EXCEPTION_CAUGHT,
                'method'   => 'onException',
                'priority' => 10000,
            ));

        $this->expectRegistration(
            'tubepress_app_html_impl_listeners_GlobalJsConfigListener',
            'tubepress_app_html_impl_listeners_GlobalJsConfigListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_html_api_Constants::EVENT_SCRIPTS_PRE,
                'method'   => 'onPreScriptsHtml',
                'priority' => 10000
            ));

        $this->expectRegistration(

            tubepress_app_html_api_HtmlGeneratorInterface::_,
            'tubepress_app_html_impl_HtmlGenerator'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_shortcode_api_ParserInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_theme_api_ThemeLibraryInterface::_));

        $this->expectParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_html', array(

            'defaultValues' => array(

                tubepress_app_html_api_Constants::OPTION_GALLERY_ID  => null,
                tubepress_app_html_api_Constants::OPTION_HTTPS       => false,
                tubepress_app_html_api_Constants::OPTION_OUTPUT      => null,
            ),

            'labels' => array(
                tubepress_app_html_api_Constants::OPTION_HTTPS => 'Enable HTTPS',       //>(translatable)<
            ),

            'descriptions' => array(
                tubepress_app_html_api_Constants::OPTION_HTTPS => 'Serve thumbnails and embedded video player over a secure connection.',  //>(translatable)<
            ),

            'proOptionNames' => array(
                tubepress_app_html_api_Constants::OPTION_HTTPS
            ),

            'doNotPersistNames' => array(
                tubepress_app_html_api_Constants::OPTION_GALLERY_ID,
                tubepress_app_html_api_Constants::OPTION_OUTPUT,
            )
        ));

        $this->expectParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_VALIDATION . '_html', array(

            'priority' => 30000,
            'map'      => array(
                'oneOrMoreWordChars' => array(
                    tubepress_app_html_api_Constants::OPTION_GALLERY_ID
                )
            )
        ));
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(

            tubepress_lib_event_api_EventDispatcherInterface::_ => tubepress_lib_event_api_EventDispatcherInterface::_,
            tubepress_app_shortcode_api_ParserInterface::_ => tubepress_app_shortcode_api_ParserInterface::_,
            tubepress_app_theme_api_ThemeLibraryInterface::_ => tubepress_app_theme_api_ThemeLibraryInterface::_,
            tubepress_app_environment_api_EnvironmentInterface::_ => tubepress_app_environment_api_EnvironmentInterface::_,
            tubepress_platform_api_log_LoggerInterface::_ => tubepress_platform_api_log_LoggerInterface::_,
            tubepress_lib_util_api_UrlUtilsInterface::_ => tubepress_lib_util_api_UrlUtilsInterface::_
        );
    }
}
