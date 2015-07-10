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
class tubepress_theme_ioc_ThemeExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
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
        $this->_registerSingletonServices($containerBuilder);
        $this->_registerListeners($containerBuilder);
        $this->_registerOptions($containerBuilder);
        $this->_registerOptionsUi($containerBuilder);
    }

    private function _registerSingletonServices(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_platform_impl_boot_helper_uncached_Serializer',
            'tubepress_platform_impl_boot_helper_uncached_Serializer'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_boot_BootSettingsInterface::_));

        $parallelServices = array(
            array('',       '',       tubepress_app_api_options_Names::THEME),
            array('.admin', 'admin-', tubepress_app_api_options_Names::THEME_ADMIN),
        );

        foreach ($parallelServices as $serviceInfo) {

            $serviceSuffix  = $serviceInfo[0];
            $artifactPrefix = $serviceInfo[1];
            $optionName     = $serviceInfo[2];

            $containerBuilder->register(
                tubepress_platform_api_contrib_RegistryInterface::_ . '.' . tubepress_app_api_theme_ThemeInterface::_ . $serviceSuffix,
                'tubepress_platform_impl_boot_helper_uncached_contrib_SerializedRegistry'
            )->addArgument(sprintf('%%%s%%', tubepress_platform_impl_boot_PrimaryBootstrapper::CONTAINER_PARAM_BOOT_ARTIFACTS))
             ->addArgument($artifactPrefix . 'themes')
             ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
             ->addArgument(new tubepress_platform_api_ioc_Reference('tubepress_platform_impl_boot_helper_uncached_Serializer'));

            $containerBuilder->register(
                'tubepress_theme_impl_CurrentThemeService' . $serviceSuffix,
                'tubepress_theme_impl_CurrentThemeService'
            )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
             ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_contrib_RegistryInterface::_ . '.' . tubepress_app_api_theme_ThemeInterface::_ . $serviceSuffix))
             ->addArgument('tubepress/' . $artifactPrefix . 'default')
             ->addArgument($optionName);
        }
    }

    private function _registerListeners(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_theme_impl_listeners_LegacyThemeListener',
            'tubepress_theme_impl_listeners_LegacyThemeListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_contrib_RegistryInterface::_ . '.' . tubepress_app_api_theme_ThemeInterface::_))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
           'event'    => tubepress_app_api_event_Events::OPTION_SET . '.' . tubepress_app_api_options_Names::THEME,
           'priority' => 100000,
           'method'   => 'onPreValidationSet'
        ));

        $containerBuilder->register(
            'tubepress_theme_impl_listeners_AcceptableValuesListener',
            'tubepress_theme_impl_listeners_AcceptableValuesListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_contrib_RegistryInterface::_ . '.' . tubepress_app_api_theme_ThemeInterface::_))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
           'event'    => tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::THEME,
           'priority' => 100000,
           'method'   => 'onAcceptableValues'
        ));

        $containerBuilder->register(
            'tubepress_theme_impl_listeners_AcceptableValuesListener.admin',
            'tubepress_theme_impl_listeners_AcceptableValuesListener'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_contrib_RegistryInterface::_ . '.' . tubepress_app_api_theme_ThemeInterface::_ . '.admin'))
         ->addTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::THEME_ADMIN,
            'priority' => 100000,
            'method'   => 'onAcceptableValues'
        ));
    }

    private function _registerOptions(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_app_api_options_Reference__theme',
            'tubepress_app_api_options_Reference'
        )->addTag(tubepress_app_api_options_ReferenceInterface::_)
         ->addArgument(array(

            tubepress_app_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                tubepress_app_api_options_Names::THEME       => 'tubepress/default',
                tubepress_app_api_options_Names::THEME_ADMIN => 'tubepress/admin-default',
            ),
            tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                tubepress_app_api_options_Names::THEME => 'Theme',  //>(translatable)<
            ),
        ));
    }

    private function _registerOptionsUi(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'theme_field',
            'tubepress_app_api_options_ui_FieldInterface'
        )->setFactoryService(tubepress_app_api_options_ui_FieldBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_app_api_options_Names::THEME)
         ->addArgument('theme');

        $fieldReferences = array(new tubepress_platform_api_ioc_Reference('theme_field'));

        $containerBuilder->register(
            'theme_category',
            'tubepress_app_impl_options_ui_BaseElement'
        )->addArgument(tubepress_app_api_options_ui_CategoryNames::THEME)
         ->addArgument('Theme'); //>(translatable)<

        $categoryReferences = array(new tubepress_platform_api_ioc_Reference('theme_category'));

        $fieldMap = array(
            tubepress_app_api_options_ui_CategoryNames::THEME => array(
                tubepress_app_api_options_Names::THEME
            ),
        );

        $containerBuilder->register(
            'tubepress_api_options_ui_BaseFieldProvider__theme',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->addArgument('field-provider-theme')
         ->addArgument('Theme')
         ->addArgument(false)
         ->addArgument(false)
         ->addArgument($categoryReferences)
         ->addArgument($fieldReferences)
         ->addArgument($fieldMap);
    }
}