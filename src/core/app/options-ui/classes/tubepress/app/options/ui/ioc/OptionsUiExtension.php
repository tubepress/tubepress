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
class tubepress_app_options_ui_ioc_OptionsUiExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
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
            'tubepress_app_options_ui_impl_listeners_CoreCategorySorter',
            'tubepress_app_options_ui_impl_listeners_CoreCategorySorter'
        )->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_options_ui_api_Constants::EVENT_OPTIONS_UI_PAGE_TEMPLATE,
            'method'   => 'onOptionsPageTemplate',
            'priority' => 80000
        ));

        $containerBuilder->register(
            tubepress_app_options_ui_api_FieldBuilderInterface::_,
            'tubepress_app_options_ui_impl_FieldBuilder'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_translation_api_TranslatorInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_PersistenceInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_RequestParametersInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_template_api_TemplateFactoryInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ReferenceInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_LangUtilsInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_AcceptableValuesInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_theme_api_ThemeLibraryInterface::_))
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_app_media_provider_api_MediaProviderInterface::_,
            'method' => 'setMediaProviders'
        ));

        $containerBuilder->register(
            tubepress_app_options_ui_api_ElementBuilderInterface::_,
            'tubepress_app_options_ui_impl_ElementBuilder'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_translation_api_TranslatorInterface::_));

        $containerBuilder->setParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_options_ui', array(

            'defaultValues' => array(
                tubepress_app_options_ui_api_Constants::OPTION_DISABLED_FIELD_PROVIDERS => null,
            ),

            'labels' => array(
                tubepress_app_options_ui_api_Constants::OPTION_DISABLED_FIELD_PROVIDERS => 'Only show options applicable to...', //>(translatable)<
            )
        ));

        $containerBuilder->register(
            'tubepress_app_options_ui_impl_fields_FieldProviderFilterField',
            'tubepress_app_options_ui_impl_fields_FieldProviderFilterField'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_translation_api_TranslatorInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_PersistenceInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_RequestParametersInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_template_api_TemplateFactoryInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ReferenceInterface::_));
        $fieldReferences = array(new tubepress_platform_api_ioc_Reference('tubepress_app_options_ui_impl_fields_FieldProviderFilterField'));

        $containerBuilder->register(
            'tubepress_app_options_ui_api_ElementInterface_advanced_category',
            'tubepress_app_options_ui_api_ElementInterface'
        )->setFactoryService(tubepress_app_options_ui_api_ElementBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_app_options_ui_api_Constants::OPTIONS_UI_CATEGORY_ADVANCED)
         ->addArgument('Advanced');  //>(translatable)<
        $categoryReferences = array(new tubepress_platform_api_ioc_Reference('tubepress_app_options_ui_api_ElementInterface_advanced_category'));

        $containerBuilder->register(
            'tubepress_app_options_ui_impl_FieldProvider',
            'tubepress_app_options_ui_impl_FieldProvider'
        )->addArgument($categoryReferences)
         ->addArgument($fieldReferences)
         ->addTag('tubepress_app_options_ui_api_FieldProviderInterface');
    }
}