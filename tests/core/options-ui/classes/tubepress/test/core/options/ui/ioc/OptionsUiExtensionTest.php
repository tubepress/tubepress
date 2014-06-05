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

            tubepress_core_options_ui_api_FieldBuilderInterface::_,
            'tubepress_core_options_ui_impl_FieldBuilder'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_PersistenceInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_http_api_RequestParametersInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_template_api_TemplateFactoryInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ReferenceInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_));

        $this->expectRegistration(

            tubepress_core_options_ui_api_ElementBuilderInterface::_,
            'tubepress_core_options_ui_impl_ElementBuilder'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE, array(

            'defaultValues' => array(

                tubepress_core_options_ui_api_Constants::OPTION_DISABLED_OPTIONS_PAGE_PARTICIPANTS => null,
            ),

            'labels' => array(

                tubepress_core_options_ui_api_Constants::OPTION_DISABLED_OPTIONS_PAGE_PARTICIPANTS => 'Only show options applicable to...', //>(translatable)<
            )
        ));
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(
            tubepress_core_translation_api_TranslatorInterface::_ => tubepress_core_translation_api_TranslatorInterface::_,
            tubepress_core_options_api_PersistenceInterface::_ => tubepress_core_options_api_PersistenceInterface::_,
            tubepress_core_http_api_RequestParametersInterface::_ => tubepress_core_http_api_RequestParametersInterface::_,
            tubepress_core_event_api_EventDispatcherInterface::_ => tubepress_core_event_api_EventDispatcherInterface::_,
            tubepress_core_template_api_TemplateFactoryInterface::_ => tubepress_core_template_api_TemplateFactoryInterface::_,
            tubepress_core_options_api_ReferenceInterface::_ => tubepress_core_options_api_ReferenceInterface::_,
            tubepress_api_util_LangUtilsInterface::_ => tubepress_api_util_LangUtilsInterface::_,
            tubepress_core_options_api_ContextInterface::_ => tubepress_core_options_api_ContextInterface::_,
        );
    }
}