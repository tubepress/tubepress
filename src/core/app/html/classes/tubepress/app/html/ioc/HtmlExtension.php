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
 *
 */
class tubepress_app_html_ioc_HtmlExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
{

    /**
     * Called during construction of the TubePress service container. If an add-on intends to add
     * services to the container, it should do so here. The incoming `tubepress_platform_api_ioc_ContainerBuilderInterface`
     * will be completely empty, and after this method is executed will be merged into the primary service container.
     *
     * @param tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder An empty `tubepress_platform_api_ioc_ContainerBuilderInterface` instance.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function load(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_app_html_impl_listeners_BaseUrlSetter',
            'tubepress_app_html_impl_listeners_BaseUrlSetter'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_environment_api_EnvironmentInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_util_api_UrlUtilsInterface::_))
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_html_api_Constants::EVENT_GLOBAL_JS_CONFIG,
            'method'   => 'onGlobalJsConfig',
            'priority' => 10000
        ));

        $containerBuilder->register(
            'tubepress_app_html_impl_listeners_ExceptionListener',
            'tubepress_app_html_impl_listeners_ExceptionListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_html_api_Constants::EVENT_EXCEPTION_CAUGHT,
            'method'   => 'onException',
            'priority' => 10000,
        ));

        $containerBuilder->register(
            'tubepress_app_html_impl_listeners_GlobalJsConfigListener',
            'tubepress_app_html_impl_listeners_GlobalJsConfigListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_html_api_Constants::EVENT_SCRIPTS_PRE,
            'method'   => 'onPreScriptsHtml',
             'priority' => 10000
        ));

        $containerBuilder->register(

            tubepress_app_html_api_HtmlGeneratorInterface::_,
            'tubepress_app_html_impl_HtmlGenerator'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_shortcode_api_ParserInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_theme_api_ThemeLibraryInterface::_));

        $containerBuilder->setParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_html', array(

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

        $containerBuilder->setParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_VALIDATION . '_html', array(

            'priority' => 30000,
            'map'      => array(
                'oneOrMoreWordChars' => array(
                    tubepress_app_html_api_Constants::OPTION_GALLERY_ID
                )
            )
        ));
    }
}