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
 * @covers tubepress_options_ui_ioc_OptionsUiExtension
 */
class tubepress_test_options_ui_ioc_OptionsUiExtensionTest extends tubepress_api_test_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_options_ui_ioc_OptionsUiExtension
     */
    protected function buildSut()
    {
        return  new tubepress_options_ui_ioc_OptionsUiExtension();
    }

    protected function prepareForLoad()
    {
        $this->_registerOptionsUiSingletons();
        $this->_registerOptions();
        $this->_registerOptionsUi();
        $this->_registerListeners();
        $this->_registerPathProvider();
    }

    protected function getExpectedExternalServicesMap()
    {
        $logger = $this->mock(tubepress_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);

        $reference = $this->mock(tubepress_api_options_ReferenceInterface::_);
        $reference->shouldReceive('optionExists')->atLeast(1)->andReturn(true);
        $reference->shouldReceive('getUntranslatedLabel')->atLeast(1)->andReturnUsing(function ($optionName) {

            return "<<$optionName-label>>";
        });
        $reference->shouldReceive('getUntranslatedDescription')->atLeast(1)->andReturnUsing(function ($optionName) {

            return "<<$optionName-desc>>";
        });

        return array(
            tubepress_api_options_PersistenceInterface::_            => tubepress_api_options_PersistenceInterface::_,
            tubepress_api_http_RequestParametersInterface::_         => tubepress_api_http_RequestParametersInterface::_,
            tubepress_api_template_TemplatingInterface::_ . '.admin' => tubepress_api_template_TemplatingInterface::_,
            tubepress_api_options_ReferenceInterface::_              => $reference,
            tubepress_api_util_LangUtilsInterface::_            => tubepress_api_util_LangUtilsInterface::_,
            tubepress_api_options_AcceptableValuesInterface::_       => tubepress_api_options_AcceptableValuesInterface::_,
            tubepress_api_contrib_RegistryInterface::_ . '.' . tubepress_api_theme_ThemeInterface::_ => tubepress_api_contrib_RegistryInterface::_,
            'tubepress_html_impl_CssAndJsGenerationHelper.admin'         => 'tubepress_html_impl_CssAndJsGenerationHelper',
            tubepress_api_event_EventDispatcherInterface::_          => tubepress_api_event_EventDispatcherInterface::_,
            tubepress_api_contrib_RegistryInterface::_ . '.' . tubepress_api_theme_ThemeInterface::_ . '.admin' => tubepress_api_contrib_RegistryInterface::_,
            'tubepress_theme_impl_CurrentThemeService.admin'             => 'tubepress_theme_impl_CurrentThemeService',
            tubepress_api_environment_EnvironmentInterface::_        => tubepress_api_environment_EnvironmentInterface::_,
            tubepress_api_log_LoggerInterface::_                => $logger,
            tubepress_api_util_StringUtilsInterface::_          => tubepress_api_util_StringUtilsInterface::_,
            tubepress_api_translation_TranslatorInterface::_         => tubepress_api_translation_TranslatorInterface::_,
        );
    }
    
    private function _registerPathProvider()
    {
        $this->expectRegistration(
            'tubepress_api_template_BasePathProvider__options_ui',
            'tubepress_api_template_BasePathProvider'
        )->withArgument(array(
            TUBEPRESS_ROOT . '/src/add-ons/options-ui/templates'
        ))->withTag('tubepress_spi_template_PathProviderInterface.admin');
    }
    
    private function _registerListeners()
    {
        $this->expectRegistration(
            'tubepress_options_ui_impl_listeners_BootstrapIe8Listener',
            'tubepress_options_ui_impl_listeners_BootstrapIe8Listener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::HTML_SCRIPTS_ADMIN,
                'priority' => 100000,
                'method'   => 'onAdminScripts',
            ));

        $this->expectRegistration(
            'tubepress_options_ui_impl_listeners_OptionsPageTemplateListener',
            'tubepress_options_ui_impl_listeners_OptionsPageTemplateListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
                'tag'    => 'tubepress_spi_options_ui_FieldProviderInterface',
                'method' => 'setFieldProviders'))
            ->withTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
                'tag'    => 'tubepress_spi_media_MediaProviderInterface',
                'method' => 'setMediaProviders'))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::TEMPLATE_PRE_RENDER . '.options-ui/form',
                'priority' => 100000,
                'method'   => 'onOptionsGuiTemplate',
            ));
    }
    
    private function _registerOptionsUiSingletons()
    {
        $this->expectRegistration(
            tubepress_api_options_ui_FieldBuilderInterface::_,
            'tubepress_options_ui_impl_FieldBuilder'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_ . '.admin'))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_AcceptableValuesInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_contrib_RegistryInterface::_ . '.' . tubepress_api_theme_ThemeInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_spi_media_MediaProviderInterface::__,
                'method' => 'setMediaProviders'
            ));

        $this->expectRegistration(
            'tubepress_html_impl_CssAndJsGenerationHelper.admin',
            'tubepress_html_impl_CssAndJsGenerationHelper'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_contrib_RegistryInterface::_ . '.' . tubepress_api_theme_ThemeInterface::_ . '.admin'))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_ . '.admin'))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_theme_impl_CurrentThemeService.admin'))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
            ->withArgument(tubepress_api_event_Events::HTML_STYLESHEETS_ADMIN)
            ->withArgument(tubepress_api_event_Events::HTML_SCRIPTS_ADMIN)
            ->withArgument('options-ui/cssjs/styles')
            ->withArgument('options-ui/cssjs/scripts');

        $this->expectRegistration(
            tubepress_api_options_ui_FormInterface::_,
            'tubepress_options_ui_impl_Form'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_ . '.admin'))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_html_impl_CssAndJsGenerationHelper.admin'))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
                'tag'    => 'tubepress_spi_options_ui_FieldProviderInterface',
                'method' => 'setFieldProviders',
            ));
    }

    private function _registerOptionsUi()
    {
        $categoryReferences = array();
        $categories = array(
            array(tubepress_api_options_ui_CategoryNames::ADVANCED, 'Advanced'),      //>(translatable)<
        );
        foreach ($categories as $categoryIdAndLabel) {

            $serviceId = 'options_ui_category_' . $categoryIdAndLabel[0];
            $this->expectRegistration(
                $serviceId,
                'tubepress_options_ui_impl_BaseElement'
            )->withArgument($categoryIdAndLabel[0])
                ->withArgument($categoryIdAndLabel[1]);

            $categoryReferences[] = new tubepress_api_ioc_Reference($serviceId);
        }

        $this->expectRegistration(
            'tubepress_api_options_ui_BaseFieldProvider__options_ui',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->withArgument('field-provider-options-ui')
            ->withArgument('Options UI')
            ->withArgument(false)
            ->withArgument(false)
            ->withArgument($categoryReferences)
            ->withArgument(array())
            ->withArgument(array())
            ->withTag('tubepress_spi_options_ui_FieldProviderInterface');
    }

    private function _registerOptions()
    {
        $this->expectRegistration(
            'tubepress_api_options_Reference__options_ui',
            'tubepress_api_options_Reference'
        )->withTag(tubepress_api_options_ReferenceInterface::_)
            ->withArgument(array(

                tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                    tubepress_api_options_Names::OPTIONS_UI_DISABLED_FIELD_PROVIDERS => null
                ),

                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                    tubepress_api_options_Names::OPTIONS_UI_DISABLED_FIELD_PROVIDERS => 'Only show options applicable to...', //>(translatable)<
                ),

            ))->withArgument(array());
    }
}
