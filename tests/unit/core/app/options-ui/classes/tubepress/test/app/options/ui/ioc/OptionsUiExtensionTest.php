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
 * @covers tubepress_app_options_ui_ioc_OptionsUiExtension
 */
class tubepress_test_app_options_ui_ioc_OptionsUiExtensionTest extends tubepress_test_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_platform_api_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_app_options_ui_ioc_OptionsUiExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            'tubepress_app_options_ui_impl_listeners_CoreCategorySorter',
            'tubepress_app_options_ui_impl_listeners_CoreCategorySorter'
        )->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_options_ui_api_Constants::EVENT_OPTIONS_UI_PAGE_TEMPLATE,
                'method'   => 'onOptionsPageTemplate',
                'priority' => 80000
            ));

        $this->expectRegistration(
            tubepress_app_options_ui_api_FieldBuilderInterface::_,
            'tubepress_app_options_ui_impl_FieldBuilder'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_translation_api_TranslatorInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_PersistenceInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_RequestParametersInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_template_api_TemplateFactoryInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ReferenceInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_LangUtilsInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_AcceptableValuesInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_theme_api_ThemeLibraryInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_app_media_provider_api_MediaProviderInterface::_,
                'method' => 'setMediaProviders'
            ));

        $this->expectRegistration(
            tubepress_app_options_ui_api_ElementBuilderInterface::_,
            'tubepress_app_options_ui_impl_ElementBuilder'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_translation_api_TranslatorInterface::_));

        $this->expectParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_options_ui', array(

            'defaultValues' => array(
                tubepress_app_options_ui_api_Constants::OPTION_DISABLED_FIELD_PROVIDERS => null,
            ),

            'labels' => array(
                tubepress_app_options_ui_api_Constants::OPTION_DISABLED_FIELD_PROVIDERS => 'Only show options applicable to...', //>(translatable)<
            )
        ));

        $this->expectRegistration(
            'tubepress_app_options_ui_impl_fields_FieldProviderFilterField',
            'tubepress_app_options_ui_impl_fields_FieldProviderFilterField'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_translation_api_TranslatorInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_PersistenceInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_http_api_RequestParametersInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_template_api_TemplateFactoryInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ReferenceInterface::_));
        $fieldReferences = array(new tubepress_platform_api_ioc_Reference('tubepress_app_options_ui_impl_fields_FieldProviderFilterField'));

        $this->expectRegistration(
            'tubepress_app_options_ui_api_ElementInterface_advanced_category',
            'tubepress_app_options_ui_api_ElementInterface'
        )->withFactoryService(tubepress_app_options_ui_api_ElementBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_app_options_ui_api_Constants::OPTIONS_UI_CATEGORY_ADVANCED)
            ->withArgument('Advanced');  //>(translatable)<
        $categoryReferences = array(new tubepress_platform_api_ioc_Reference('tubepress_app_options_ui_api_ElementInterface_advanced_category'));

        $this->expectRegistration(
            'tubepress_app_options_ui_impl_FieldProvider',
            'tubepress_app_options_ui_impl_FieldProvider'
        )->withArgument($categoryReferences)
            ->withArgument($fieldReferences)
            ->withTag('tubepress_app_options_ui_api_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $optionReference = $this->mock(tubepress_app_options_api_ReferenceInterface::_);
        $optionReference->shouldReceive('getUntranslatedLabel')->once()->with(tubepress_app_options_ui_api_Constants::OPTION_DISABLED_FIELD_PROVIDERS)->andReturn('SDF');
        $optionReference->shouldReceive('getUntranslatedDescription')->once()->with(tubepress_app_options_ui_api_Constants::OPTION_DISABLED_FIELD_PROVIDERS)->andReturn('SDF');

        return array(
            tubepress_lib_translation_api_TranslatorInterface::_ => tubepress_lib_translation_api_TranslatorInterface::_,
            tubepress_app_options_api_PersistenceInterface::_ => tubepress_app_options_api_PersistenceInterface::_,
            tubepress_app_http_api_RequestParametersInterface::_ => tubepress_app_http_api_RequestParametersInterface::_,
            tubepress_lib_event_api_EventDispatcherInterface::_ => tubepress_lib_event_api_EventDispatcherInterface::_,
            tubepress_lib_template_api_TemplateFactoryInterface::_ => tubepress_lib_template_api_TemplateFactoryInterface::_,
            tubepress_app_options_api_ReferenceInterface::_ => $optionReference,
            tubepress_platform_api_util_LangUtilsInterface::_ => tubepress_platform_api_util_LangUtilsInterface::_,
            tubepress_app_options_api_ContextInterface::_ => tubepress_app_options_api_ContextInterface::_,
            tubepress_app_options_api_AcceptableValuesInterface::_ => tubepress_app_options_api_AcceptableValuesInterface::_,
            tubepress_app_theme_api_ThemeLibraryInterface::_ => tubepress_app_theme_api_ThemeLibraryInterface::_
        );
    }
}