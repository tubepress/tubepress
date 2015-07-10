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
 *
 */
class tubepress_html_ioc_HtmlExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
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
            'tubepress_html_impl_CssAndJsGenerationHelper',
            'tubepress_html_impl_CssAndJsGenerationHelper'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_contrib_RegistryInterface::_ . '.' . tubepress_app_api_theme_ThemeInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference('tubepress_app_impl_theme_CurrentThemeService'))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_environment_EnvironmentInterface::_))
         ->addArgument(tubepress_app_api_event_Events::HTML_STYLESHEETS)
         ->addArgument(tubepress_app_api_event_Events::HTML_SCRIPTS)
         ->addArgument('cssjs/styles')
         ->addArgument('cssjs/scripts');

        $containerBuilder->register(
            tubepress_app_api_html_HtmlGeneratorInterface::_,
            'tubepress_html_impl_HtmlGenerator'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_template_TemplatingInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference('tubepress_html_impl_CssAndJsGenerationHelper'))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_environment_EnvironmentInterface::_))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
             'event'    => tubepress_app_api_event_Events::HTML_SCRIPTS,
             'priority' => 100000,
             'method'   => 'onScripts',
        ));
    }
}