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
 * @covers tubepress_core_html_ioc_HtmlExtension
 */
class tubepress_test_core_html_ioc_HtmlExtensionTest extends tubepress_test_core_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_core_html_ioc_HtmlExtension
     */
    protected function buildSut()
    {
        return  new tubepress_core_html_ioc_HtmlExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            'tubepress_core_html_impl_listeners_BaseUrlSetter',
            'tubepress_core_html_impl_listeners_BaseUrlSetter'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_environment_api_EnvironmentInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_html_api_Constants::EVENT_GLOBAL_JS_CONFIG,
                'method'   => 'onJsConfig',
                'priority' => 10000
            ));

        $this->expectRegistration(
            'tubepress_core_html_impl_listeners_GlobalJsConfig',
            'tubepress_core_html_impl_listeners_GlobalJsConfig'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_html_api_Constants::EVENT_SCRIPTS_PRE,
                'method'   => 'onPreScriptsHtml',
                'priority' => 10000
            ));

        $this->expectRegistration(
            tubepress_core_html_api_HtmlGeneratorInterface::_,
            'tubepress_core_html_impl_HtmlGenerator'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_shortcode_api_ParserInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_theme_api_ThemeLibraryInterface::_));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE, array(

            'defaultValues' => array(

                tubepress_core_html_api_Constants::OPTION_ENABLE_JS_API => true,
                tubepress_core_html_api_Constants::OPTION_GALLERY_ID    => null,
                tubepress_core_html_api_Constants::OPTION_HTTPS         => false,
                tubepress_core_html_api_Constants::OPTION_OUTPUT        => null,
            ),

            'labels' => array(

                tubepress_core_html_api_Constants::OPTION_ENABLE_JS_API => 'Enable JavaScript API', //>(translatable)<
                tubepress_core_html_api_Constants::OPTION_HTTPS         => 'Enable HTTPS',       //>(translatable)<
            ),

            'descriptions' => array(

                tubepress_core_html_api_Constants::OPTION_ENABLE_JS_API => 'Allow TubePress to communicate with the embedded video player via JavaScript. This incurs a very small performance overhead, but is required for some features.', //>(translatable)<
                tubepress_core_html_api_Constants::OPTION_HTTPS         => 'Serve thumbnails and embedded video player over a secure connection.',  //>(translatable)<
            ),

            'proOptions' => array(

                tubepress_core_html_api_Constants::OPTION_HTTPS
            ),

            'noPersist' => array(

                tubepress_core_html_api_Constants::OPTION_GALLERY_ID,
                tubepress_core_html_api_Constants::OPTION_OUTPUT,
            )
        ));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_VALIDATION . '_html', array(

            'priority' => 30000,
            'map'      => array(
                'oneOrMoreWordChars' => array(
                    tubepress_core_html_api_Constants::OPTION_GALLERY_ID
                )
            )
        ));
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(

            tubepress_core_event_api_EventDispatcherInterface::_ => tubepress_core_event_api_EventDispatcherInterface::_,
            tubepress_core_shortcode_api_ParserInterface::_ => tubepress_core_shortcode_api_ParserInterface::_,
            tubepress_core_theme_api_ThemeLibraryInterface::_ => tubepress_core_theme_api_ThemeLibraryInterface::_,
            tubepress_core_environment_api_EnvironmentInterface::_ => tubepress_core_environment_api_EnvironmentInterface::_
        );
    }
}
