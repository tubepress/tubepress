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
class tubepress_core_theme_ioc_ThemeExtension implements tubepress_api_ioc_ContainerExtensionInterface
{

    /**
     * Called during construction of the TubePress service container. If an add-on intends to add
     * services to the container, it should do so here. The incoming `tubepress_api_ioc_ContainerBuilderInterface`
     * will be completely empty, and after this method is executed will be merged into the primary service container.
     *
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder An empty `tubepress_api_ioc_ContainerBuilderInterface` instance.
     *
     * @return void
     *
     * @api
     * @since 3.2.0
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            tubepress_core_theme_api_ThemeLibraryInterface::_,
            'tubepress_core_theme_impl_ThemeLibrary'
        )->addArgument('%themes%')
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_environment_api_EnvironmentInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_url_api_UrlFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_));

        $containerBuilder->register(
            'tubepress_core_theme_impl_ThemeRegistry',
            'tubepress_core_theme_impl_ThemeRegistry'
        )->addArgument(new tubepress_api_ioc_Reference('tubepress_impl_log_BootLogger'))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_boot_BootSettingsInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('ehough_finder_FinderFactoryInterface'))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_contrib_api_ContributableValidatorInterface::_))
         ->addTag(tubepress_api_contrib_RegistryInterface::_, array('type' => 'tubepress_core_theme_api_ThemeInterface'));

        $containerBuilder->register(
            'tubepress_core_theme_impl_listeners_options_LegacyThemeListener',
            'tubepress_core_theme_impl_listeners_options_LegacyThemeListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_theme_api_ThemeLibraryInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_options_api_Constants::EVENT_OPTION_SET . '.' . tubepress_core_theme_api_Constants::OPTION_THEME,
            'method'   => 'onPreValidationSet',
            'priority' => 300000
        ));

        $containerBuilder->register(
            'tubepress_core_theme_impl_listeners_options_AcceptableValues',
            'tubepress_core_theme_impl_listeners_options_AcceptableValues'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_theme_api_ThemeLibraryInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_options_api_Constants::EVENT_OPTION_GET_ACCEPTABLE_VALUES . '.' . tubepress_core_theme_api_Constants::OPTION_THEME,
            'method'   => 'onAcceptableValue',
            'priority' => 30000
        ));

        $containerBuilder->setParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_theme', array(

            'defaultValues' => array(
                tubepress_core_theme_api_Constants::OPTION_THEME => 'tubepress/default',
            ),

            'labels' => array(
                tubepress_core_theme_api_Constants::OPTION_THEME => 'Theme',  //>(translatable)<
            )
        ));
    }
}