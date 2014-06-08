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
 * @covers tubepress_core_options_ui_ioc_OptionsUiExtension
 */
class tubepress_test_core_options_ui_ioc_OptionsUiExtensionTest extends tubepress_test_core_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_api_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_core_options_ui_ioc_OptionsUiExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            'tubepress_core_options_ui_impl_listeners_CoreCategorySorter',
            'tubepress_core_options_ui_impl_listeners_CoreCategorySorter'
        )->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_options_ui_api_Constants::EVENT_OPTIONS_UI_PAGE_TEMPLATE,
                'method'   => 'onOptionsPageTemplate',
                'priority' => 80000
            ));

        $this->expectRegistration(
            tubepress_core_options_ui_api_FieldBuilderInterface::_,
            'tubepress_core_options_ui_impl_FieldBuilder'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_PersistenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_template_api_TemplateFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ReferenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_AcceptableValuesInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_theme_api_ThemeLibraryInterface::_));

        $this->expectRegistration(
            tubepress_core_options_ui_api_ElementBuilderInterface::_,
            'tubepress_core_options_ui_impl_ElementBuilder'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_options_ui', array(

            'defaultValues' => array(
                tubepress_core_options_ui_api_Constants::OPTION_DISABLED_OPTIONS_PAGE_PARTICIPANTS => null,
            ),

            'labels' => array(
                tubepress_core_options_ui_api_Constants::OPTION_DISABLED_OPTIONS_PAGE_PARTICIPANTS => 'Only show options applicable to...', //>(translatable)<
            )
        ));

        $this->expectRegistration(
            'tubepress_core_options_ui_impl_fields_ParticipantFilterField',
            'tubepress_core_options_ui_impl_fields_ParticipantFilterField'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_PersistenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_template_api_TemplateFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ReferenceInterface::_));
        $fieldReferences = array(new tubepress_api_ioc_Reference('tubepress_core_options_ui_impl_fields_ParticipantFilterField'));

        $this->expectRegistration(
            'tubepress_core_options_ui_api_ElementInterface_advanced_category',
            'tubepress_core_options_ui_api_ElementInterface'
        )->withFactoryService(tubepress_core_options_ui_api_ElementBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_core_options_ui_api_Constants::OPTIONS_UI_CATEGORY_ADVANCED)
            ->withArgument('Advanced');
        $categoryReferences = array(new tubepress_api_ioc_Reference('tubepress_core_options_ui_api_ElementInterface_advanced_category'));

        $this->expectRegistration(
            'tubepress_core_options_ui_impl_FieldProvider',
            'tubepress_core_options_ui_impl_FieldProvider'
        )->withArgument($categoryReferences)
            ->withArgument($fieldReferences)
            ->withTag('tubepress_core_options_ui_api_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $optionReference = $this->mock(tubepress_core_options_api_ReferenceInterface::_);
        $optionReference->shouldReceive('getUntranslatedLabel')->once()->with(tubepress_core_options_ui_api_Constants::OPTION_DISABLED_OPTIONS_PAGE_PARTICIPANTS)->andReturn('SDF');
        $optionReference->shouldReceive('getUntranslatedDescription')->once()->with(tubepress_core_options_ui_api_Constants::OPTION_DISABLED_OPTIONS_PAGE_PARTICIPANTS)->andReturn('SDF');

        return array(
            tubepress_core_translation_api_TranslatorInterface::_ => tubepress_core_translation_api_TranslatorInterface::_,
            tubepress_core_options_api_PersistenceInterface::_ => tubepress_core_options_api_PersistenceInterface::_,
            tubepress_core_http_api_RequestParametersInterface::_ => tubepress_core_http_api_RequestParametersInterface::_,
            tubepress_core_event_api_EventDispatcherInterface::_ => tubepress_core_event_api_EventDispatcherInterface::_,
            tubepress_core_template_api_TemplateFactoryInterface::_ => tubepress_core_template_api_TemplateFactoryInterface::_,
            tubepress_core_options_api_ReferenceInterface::_ => $optionReference,
            tubepress_api_util_LangUtilsInterface::_ => tubepress_api_util_LangUtilsInterface::_,
            tubepress_core_options_api_ContextInterface::_ => tubepress_core_options_api_ContextInterface::_,
            tubepress_core_options_api_AcceptableValuesInterface::_ => tubepress_core_options_api_AcceptableValuesInterface::_,
            tubepress_core_theme_api_ThemeLibraryInterface::_ => tubepress_core_theme_api_ThemeLibraryInterface::_
        );
    }
}