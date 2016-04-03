<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_html_ioc_HtmlExtension
 */
class tubepress_test_html_ioc_HtmlExtensionTest extends tubepress_api_test_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_html_ioc_HtmlExtension
     */
    protected function buildSut()
    {
        return  new tubepress_html_ioc_HtmlExtension();
    }

    protected function prepareForLoad()
    {
        $this->_registerListeners();
        $this->_registerPathProvider();
        $this->_registerOptions();
        $this->_registerOptionsUi();

        $this->expectRegistration(
            'tubepress_html_impl_CssAndJsGenerationHelper',
            'tubepress_html_impl_CssAndJsGenerationHelper'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_contrib_RegistryInterface::_ . '.' . tubepress_api_theme_ThemeInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_theme_impl_CurrentThemeService'))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
            ->withArgument(tubepress_api_event_Events::HTML_STYLESHEETS)
            ->withArgument(tubepress_api_event_Events::HTML_SCRIPTS)
            ->withArgument('cssjs/styles')
            ->withArgument('cssjs/scripts');

        $this->expectRegistration(
            tubepress_api_html_HtmlGeneratorInterface::_,
            'tubepress_html_impl_HtmlGenerator'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_html_impl_CssAndJsGenerationHelper'))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::HTML_SCRIPTS,
                'priority' => 100000,
                'method'   => 'onScripts',
            ));
    }

    private function _registerListeners()
    {
        $this->expectRegistration(
            'tubepress_html_impl_listeners_HtmlListener',
            'tubepress_html_impl_listeners_HtmlListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::HTML_GLOBAL_JS_CONFIG,
                'priority' => 100000,
                'method'   => 'onGlobalJsConfig',
            ))->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::HTML_EXCEPTION_CAUGHT,
                'priority' => 100000,
                'method'   => 'onException',
            ))->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::TEMPLATE_POST_RENDER . '.cssjs/styles',
                'priority' => 100000,
                'method'   => 'onPostStylesTemplateRender', ))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::TEMPLATE_POST_RENDER . '.cssjs/scripts',
                'priority' => 100000,
                'method'   => 'onPostScriptsTemplateRender', ));
    }

    private function _registerPathProvider()
    {
        $this->expectRegistration(
            'tubepress_api_template_BasePathProvider__html',
            'tubepress_api_template_BasePathProvider'
        )->withArgument(array(
            TUBEPRESS_ROOT . '/src/add-ons/html/templates',
        ))->withTag('tubepress_spi_template_PathProviderInterface');
    }

    private function _registerOptions()
    {
        $this->expectRegistration(
            'tubepress_api_options_Reference__html',
            'tubepress_api_options_Reference'
        )->withTag(tubepress_api_options_ReferenceInterface::_)
            ->withArgument(array(

                tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(

                    tubepress_api_options_Names::HTML_GALLERY_ID => null,
                    tubepress_api_options_Names::HTML_HTTPS      => false,
                    tubepress_api_options_Names::HTML_OUTPUT     => null,
                    tubepress_api_options_Names::HTTP_METHOD     => 'GET',
                ),

                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(

                    tubepress_api_options_Names::HTML_HTTPS  => 'Enable HTTPS',
                    tubepress_api_options_Names::HTTP_METHOD => 'HTTP method',
                ),

                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(

                    tubepress_api_options_Names::HTML_HTTPS  => 'Serve thumbnails and embedded video player over a secure connection.',
                    tubepress_api_options_Names::HTTP_METHOD => 'Defines the HTTP method used in most TubePress Ajax operations',
                ),
            ))->withArgument(array(

                tubepress_api_options_Reference::PROPERTY_NO_PERSIST => array(
                    tubepress_api_options_Names::HTML_GALLERY_ID,
                    tubepress_api_options_Names::HTML_OUTPUT,
                ),

                tubepress_api_options_Reference::PROPERTY_PRO_ONLY => array(
                    tubepress_api_options_Names::HTML_HTTPS,
                ),
            ));

        $toValidate = array(
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS => array(
                tubepress_api_options_Names::HTML_GALLERY_ID,
            ),
        );

        foreach ($toValidate as $type => $optionNames) {
            foreach ($optionNames as $optionName) {
                $this->expectRegistration(
                    'regex_validator.' . $optionName,
                    'tubepress_api_options_listeners_RegexValidatingListener'
                )->withArgument($type)
                    ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
                    ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
                    ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                        'event'    => tubepress_api_event_Events::OPTION_SET . ".$optionName",
                        'priority' => 100000,
                        'method'   => 'onOption',
                    ));
            }
        }

        $fixedValuesMap = array(
            tubepress_api_options_Names::HTTP_METHOD => array(
                'GET'  => 'GET',
                'POST' => 'POST',
            ),
        );
        foreach ($fixedValuesMap as $optionName => $valuesMap) {
            $this->expectRegistration(
                'fixed_values.' . $optionName,
                'tubepress_api_options_listeners_FixedValuesListener'
            )->withArgument($valuesMap)
                ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                    'priority' => 100000,
                    'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . ".$optionName",
                    'method'   => 'onAcceptableValues',
                ));
        }
    }

    private function _registerOptionsUi()
    {
        $fieldReferences = array();
        $fieldMap        = array(
            'boolean' => array(
                tubepress_api_options_Names::HTML_HTTPS,
            ),
            'dropdown' => array(
                tubepress_api_options_Names::HTTP_METHOD,
            ),
        );

        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {

                $serviceId = 'html_field_' . $id;

                $this->expectRegistration(
                    $serviceId,
                    'tubepress_api_options_ui_FieldInterface'
                )->withFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
                    ->withFactoryMethod('newInstance')
                    ->withArgument($id)
                    ->withArgument($type);

                $fieldReferences[] = new tubepress_api_ioc_Reference($serviceId);
            }
        }

        $fieldMap = array(
            tubepress_api_options_ui_CategoryNames::ADVANCED => array(
                tubepress_api_options_Names::HTML_HTTPS,
                tubepress_api_options_Names::HTTP_METHOD,
            ),
        );

        $this->expectRegistration(
            'tubepress_api_options_ui_BaseFieldProvider__html',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->withArgument('field-provider-html')
            ->withArgument('HTML')
            ->withArgument(false)
            ->withArgument(false)
            ->withArgument(array())
            ->withArgument($fieldReferences)
            ->withArgument($fieldMap)
            ->withTag('tubepress_spi_options_ui_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $fieldBuilder = $this->mock(tubepress_api_options_ui_FieldBuilderInterface::_);
        $mockField    = $this->mock('tubepress_api_options_ui_FieldInterface');
        $fieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        return array(
            tubepress_api_event_EventDispatcherInterface::_                                      => tubepress_api_event_EventDispatcherInterface::_,
            tubepress_api_template_TemplatingInterface::_                                        => tubepress_api_template_TemplatingInterface::_,
            tubepress_api_environment_EnvironmentInterface::_                                    => tubepress_api_environment_EnvironmentInterface::_,
            'tubepress_theme_impl_CurrentThemeService'                                           => 'tubepress_theme_impl_CurrentThemeService',
            tubepress_api_contrib_RegistryInterface::_ . '.' . tubepress_api_theme_ThemeInterface::_ => tubepress_api_contrib_RegistryInterface::_,
            tubepress_api_log_LoggerInterface::_                                                 => tubepress_api_log_LoggerInterface::_,
            tubepress_api_http_RequestParametersInterface::_                                     => tubepress_api_http_RequestParametersInterface::_,
            tubepress_api_options_ReferenceInterface::_                                          => tubepress_api_options_ReferenceInterface::_,
            tubepress_api_translation_TranslatorInterface::_                                     => tubepress_api_translation_TranslatorInterface::_,
            tubepress_api_options_ui_FieldBuilderInterface::_                                    => $fieldBuilder,
        );
    }
}
