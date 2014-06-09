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
class tubepress_core_options_ui_ioc_OptionsUiExtension implements tubepress_api_ioc_ContainerExtensionInterface
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
     * @since 4.0.0
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_core_options_ui_impl_listeners_CoreCategorySorter',
            'tubepress_core_options_ui_impl_listeners_CoreCategorySorter'
        )->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_options_ui_api_Constants::EVENT_OPTIONS_UI_PAGE_TEMPLATE,
            'method'   => 'onOptionsPageTemplate',
            'priority' => 80000
        ));

        $containerBuilder->register(
            tubepress_core_options_ui_api_FieldBuilderInterface::_,
            'tubepress_core_options_ui_impl_FieldBuilder'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_PersistenceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_template_api_TemplateFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ReferenceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_AcceptableValuesInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_theme_api_ThemeLibraryInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_core_media_provider_api_MediaProviderInterface::_,
            'method' => 'setMediaProviders'
        ));

        $containerBuilder->register(
            tubepress_core_options_ui_api_ElementBuilderInterface::_,
            'tubepress_core_options_ui_impl_ElementBuilder'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_));

        $containerBuilder->setParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_options_ui', array(

            'defaultValues' => array(
                tubepress_core_options_ui_api_Constants::OPTION_DISABLED_OPTIONS_PAGE_PARTICIPANTS => null,
            ),

            'labels' => array(
                tubepress_core_options_ui_api_Constants::OPTION_DISABLED_OPTIONS_PAGE_PARTICIPANTS => 'Only show options applicable to...', //>(translatable)<
            )
        ));

        $containerBuilder->register(
            'tubepress_core_options_ui_impl_fields_ParticipantFilterField',
            'tubepress_core_options_ui_impl_fields_ParticipantFilterField'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_PersistenceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_template_api_TemplateFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ReferenceInterface::_));
        $fieldReferences = array(new tubepress_api_ioc_Reference('tubepress_core_options_ui_impl_fields_ParticipantFilterField'));

        $containerBuilder->register(
            'tubepress_core_options_ui_api_ElementInterface_advanced_category',
            'tubepress_core_options_ui_api_ElementInterface'
        )->setFactoryService(tubepress_core_options_ui_api_ElementBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument(tubepress_core_options_ui_api_Constants::OPTIONS_UI_CATEGORY_ADVANCED)
         ->addArgument('Advanced');  //>(translatable)<
        $categoryReferences = array(new tubepress_api_ioc_Reference('tubepress_core_options_ui_api_ElementInterface_advanced_category'));

        $containerBuilder->register(
            'tubepress_core_options_ui_impl_FieldProvider',
            'tubepress_core_options_ui_impl_FieldProvider'
        )->addArgument($categoryReferences)
         ->addArgument($fieldReferences)
         ->addTag('tubepress_core_options_ui_api_FieldProviderInterface');
    }
}