<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_jwplayer5_ioc_JwPlayerExtension<extended>
 */
class tubepress_test_jwplayer5_ioc_JwPlayerExtensionTest extends tubepress_api_test_ioc_AbstractContainerExtensionTest
{
    protected function buildSut()
    {
        return new tubepress_jwplayer5_ioc_JwPlayerExtension();
    }

    protected function prepareForLoad()
    {
        $this->_expectEmbeddedProvider();
        $this->_expectListeners();
        $this->_expectOptions();
        $this->_expectOptionsUi();
    }

    private function _expectEmbeddedProvider()
    {
        $this->expectRegistration(

            'tubepress_jwplayer5_impl_embedded_JwPlayer5EmbeddedProvider',
            'tubepress_jwplayer5_impl_embedded_JwPlayer5EmbeddedProvider'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
            ->withTag('tubepress_spi_embedded_EmbeddedProviderInterface')
            ->withTag('tubepress_spi_template_PathProviderInterface');
    }

    private function _expectListeners()
    {
        $colors = array(
            tubepress_jwplayer5_api_OptionNames::COLOR_BACK,
            tubepress_jwplayer5_api_OptionNames::COLOR_FRONT,
            tubepress_jwplayer5_api_OptionNames::COLOR_LIGHT,
            tubepress_jwplayer5_api_OptionNames::COLOR_SCREEN,
        );

        foreach ($colors as $optionName) {

            $this->expectRegistration(
                'tubepress_api_options_listeners_RegexValidatingListener.' . $optionName,
                'tubepress_api_options_listeners_RegexValidatingListener'
            )->withArgument(tubepress_api_options_listeners_RegexValidatingListener::TYPE_STRING_HEXCOLOR)
                ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
                ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
                ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                    'event' => tubepress_api_event_Events::OPTION_SET . ".$optionName",
                    'priority' => 98000,
                    'method'   => 'onOption',
                ));

            $this->expectRegistration(
                'value_trimmer.' . $optionName,
                'tubepress_api_options_listeners_TrimmingListener'
            )->withArgument('#')
                ->withMethodCall('setModeToLtrim', array())
                ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                    'priority' => 100000,
                    'method'   => 'onOption',
                    'event'    => tubepress_api_event_Events::OPTION_SET . ".$optionName"
                ));
        }
    }

    private function _expectOptions()
    {
        $this->expectRegistration(
            'tubepress_api_options_Reference__jwplayer5',
            'tubepress_api_options_Reference'
        )->withArgument(array(

                tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                    tubepress_jwplayer5_api_OptionNames::COLOR_BACK   => 'FFFFFF',
                    tubepress_jwplayer5_api_OptionNames::COLOR_FRONT  => '000000',
                    tubepress_jwplayer5_api_OptionNames::COLOR_LIGHT  => '000000',
                    tubepress_jwplayer5_api_OptionNames::COLOR_SCREEN => '000000',
                ),
                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                    tubepress_jwplayer5_api_OptionNames::COLOR_BACK   => 'Background color',//>(translatable)<
                    tubepress_jwplayer5_api_OptionNames::COLOR_FRONT  => 'Front color',     //>(translatable)<
                    tubepress_jwplayer5_api_OptionNames::COLOR_LIGHT  => 'Light color',     //>(translatable)<
                    tubepress_jwplayer5_api_OptionNames::COLOR_SCREEN => 'Screen color',    //>(translatable)<
                ),
                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                    tubepress_jwplayer5_api_OptionNames::COLOR_BACK   => sprintf('Default is %s.', "FFFFFF"),   //>(translatable)<
                    tubepress_jwplayer5_api_OptionNames::COLOR_FRONT  => sprintf('Default is %s.', "000000"),   //>(translatable)<
                    tubepress_jwplayer5_api_OptionNames::COLOR_LIGHT  => sprintf('Default is %s.', "000000"),   //>(translatable)<
                    tubepress_jwplayer5_api_OptionNames::COLOR_SCREEN => sprintf('Default is %s.', "000000"),   //>(translatable)<
                )
            ))->withTag(tubepress_api_options_ReferenceInterface::_);
    }

    private function _expectOptionsUi()
    {

        $colors = array(

            tubepress_jwplayer5_api_OptionNames::COLOR_BACK,
            tubepress_jwplayer5_api_OptionNames::COLOR_FRONT,
            tubepress_jwplayer5_api_OptionNames::COLOR_LIGHT,
            tubepress_jwplayer5_api_OptionNames::COLOR_SCREEN,
        );

        $fieldIndex = 0;
        foreach ($colors as $color) {

            $this->expectRegistration(

                'jwplayer_field_' . $fieldIndex++,
                'tubepress_api_options_ui_FieldInterface'
            )->withFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
                ->withFactoryMethod('newInstance')
                ->withArgument($color)
                ->withArgument('spectrum');
        }

        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; $x++) {
            $fieldReferences[] = new tubepress_api_ioc_Reference('jwplayer_field_' . $x);
        }

        $fieldMap = array(

            tubepress_api_options_ui_CategoryNames::EMBEDDED => array(

                tubepress_jwplayer5_api_OptionNames::COLOR_BACK,
                tubepress_jwplayer5_api_OptionNames::COLOR_FRONT,
                tubepress_jwplayer5_api_OptionNames::COLOR_LIGHT,
                tubepress_jwplayer5_api_OptionNames::COLOR_SCREEN)
        );

        $this->expectRegistration(

            'tubepress_api_options_ui_BaseFieldProvider__jwplayer5',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->withArgument('field-provider-jwplayer5')
            ->withArgument('JW Player')
            ->withArgument(false)
            ->withArgument(true)
            ->withArgument(array())
            ->withArgument($fieldReferences)
            ->withArgument($fieldMap)
            ->withTag('tubepress_spi_options_ui_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $colors = array(

            tubepress_jwplayer5_api_OptionNames::COLOR_BACK,
            tubepress_jwplayer5_api_OptionNames::COLOR_FRONT,
            tubepress_jwplayer5_api_OptionNames::COLOR_LIGHT,
            tubepress_jwplayer5_api_OptionNames::COLOR_SCREEN,
        );

        $mockFieldBuilder = $this->mock(tubepress_api_options_ui_FieldBuilderInterface::_);

        foreach ($colors as $color) {

            $mockSpectrumField = $this->mock('tubepress_api_options_ui_FieldInterface');
            $mockFieldBuilder->shouldReceive('newInstance')->once()->with($color, 'spectrum')->andReturn($mockSpectrumField);
        }

        return array(
            tubepress_api_url_UrlFactoryInterface::_          => tubepress_api_url_UrlFactoryInterface::_,
            tubepress_api_template_TemplatingInterface::_     => tubepress_api_template_TemplatingInterface::_,
            tubepress_api_options_ContextInterface::_         => tubepress_api_options_ContextInterface::_,
            tubepress_api_options_ui_FieldBuilderInterface::_ => $mockFieldBuilder,
            tubepress_api_translation_TranslatorInterface::_  => tubepress_api_translation_TranslatorInterface::_,
            tubepress_api_util_StringUtilsInterface::_        => tubepress_api_util_StringUtilsInterface::_,
            tubepress_api_options_ReferenceInterface::_       => tubepress_api_options_ReferenceInterface::_,
            tubepress_api_environment_EnvironmentInterface::_ => tubepress_api_environment_EnvironmentInterface::_,
            tubepress_api_boot_BootSettingsInterface::_       => tubepress_api_boot_BootSettingsInterface::_,
        );
    }
}