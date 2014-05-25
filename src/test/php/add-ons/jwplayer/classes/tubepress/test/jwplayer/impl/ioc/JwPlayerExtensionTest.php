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
 * @covers tubepress_jwplayer_impl_ioc_JwPlayerExtension<extended>
 */
class tubepress_test_jwplayer_impl_ioc_JwPlayerExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{
    protected function buildSut()
    {
        return new tubepress_jwplayer_impl_ioc_JwPlayerExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            'tubepress_jwplayer_impl_embedded_JwPlayerEmbeddedProvider',
            'tubepress_jwplayer_impl_embedded_JwPlayerEmbeddedProvider'

        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_template_TemplateFactoryInterface::_))
            ->withTag(tubepress_core_api_embedded_EmbeddedProviderInterface::_);

        $this->expectRegistration(

            'tubepress_jwplayer_impl_listeners_template_JwPlayerTemplateVars',
            'tubepress_jwplayer_impl_listeners_template_JwPlayerTemplateVars'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
            ->withTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => tubepress_core_api_const_event_EventNames::TEMPLATE_EMBEDDED,
                'method'   => 'onEmbeddedTemplate',
                'priority' => 10000
            ));

        $this->expectRegistration(

            'tubepress_jwplayer_impl_options_JwPlayerOptionProvider',
            'tubepress_jwplayer_impl_options_JwPlayerOptionProvider'
        )->withTag(tubepress_core_api_options_EasyProviderInterface::_);

        $colors = array(

            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_BACK,
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_FRONT,
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT,
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN,
        );


        foreach ($colors as $color) {

            $this->expectRegistration(

                'jw_color_sanitizer_' . $color,
                'stdClass'
            )->withTag(tubepress_core_api_const_ioc_Tags::LTRIM_SUBJECT_LISTENER, array(
                    'event'    => tubepress_core_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . ".$color",
                    'charlist' => '#',
                    'priority' => 9500
                ));
        }

        $fieldIndex = 0;
        foreach ($colors as $color) {

            $this->expectRegistration(

                'jwplayer_field_' . $fieldIndex++,
                'tubepress_core_api_options_ui_FieldInterface'
            )->withFactoryService(new tubepress_api_ioc_Reference(tubepress_core_api_options_ui_FieldBuilderInterface::_))
                ->withFactoryMethod('newInstance')
                ->withArgument($color)
                ->withArgument('spectrum');
        }

        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; $x++) {
            $fieldReferences[] = new tubepress_api_ioc_Reference('jwplayer_field_' . $x);
        }

        $this->expectRegistration(

            'jw_player_field_provider',
            'tubepress_jwplayer_impl_options_ui_JwPlayerFieldProvider'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_translation_TranslatorInterface::_))
            ->withArgument($fieldReferences)
            ->withTag('tubepress_core_api_options_ui_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $colors = array(

            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_BACK,
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_FRONT,
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT,
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN,
        );

        $mockFieldBuilder = $this->mock(tubepress_core_api_options_ui_FieldBuilderInterface::_);

        foreach ($colors as $color) {

            $mockSpectrumField = $this->mock('tubepress_core_api_options_ui_FieldInterface');
            $mockFieldBuilder->shouldReceive('newInstance')->once()->with($color, 'spectrum')->andReturn($mockSpectrumField);
        }

        return array(
            tubepress_core_api_url_UrlFactoryInterface::_ => tubepress_core_api_url_UrlFactoryInterface::_,
            tubepress_core_api_template_TemplateFactoryInterface::_ => tubepress_core_api_template_TemplateFactoryInterface::_,
            tubepress_core_api_options_ContextInterface::_ => tubepress_core_api_options_ContextInterface::_,
            tubepress_core_api_options_ui_FieldBuilderInterface::_ => $mockFieldBuilder,
            tubepress_core_api_translation_TranslatorInterface::_ => tubepress_core_api_translation_TranslatorInterface::_,
            tubepress_api_util_StringUtilsInterface::_ => tubepress_api_util_StringUtilsInterface::_
        );
    }
}