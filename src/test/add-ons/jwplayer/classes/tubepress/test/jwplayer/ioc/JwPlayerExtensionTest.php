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
 * @covers tubepress_jwplayer_ioc_JwPlayerExtension<extended>
 */
class tubepress_test_jwplayer_ioc_JwPlayerExtensionTest extends tubepress_test_core_ioc_AbstractIocContainerExtensionTest
{
    protected function buildSut()
    {
        return new tubepress_jwplayer_ioc_JwPlayerExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            'tubepress_jwplayer_impl_embedded_JwPlayerEmbeddedProvider',
            'tubepress_jwplayer_impl_embedded_JwPlayerEmbeddedProvider'

        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_url_api_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_template_api_TemplateFactoryInterface::_))
            ->withTag(tubepress_core_embedded_api_EmbeddedProviderInterface::_);

        $this->expectRegistration(

            'tubepress_jwplayer_impl_listeners_template_JwPlayerTemplateVars',
            'tubepress_jwplayer_impl_listeners_template_JwPlayerTemplateVars'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_embedded_api_Constants::EVENT_TEMPLATE_EMBEDDED,
                'method'   => 'onEmbeddedTemplate',
                'priority' => 10000
            ));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_jwplayer', array(

            'defaultValues' => array(
                tubepress_jwplayer_api_Constants::OPTION_COLOR_BACK   => 'FFFFFF',
                tubepress_jwplayer_api_Constants::OPTION_COLOR_FRONT  => '000000',
                tubepress_jwplayer_api_Constants::OPTION_COLOR_LIGHT  => '000000',
                tubepress_jwplayer_api_Constants::OPTION_COLOR_SCREEN => '000000',
            ),
            'labels' => array(
                tubepress_jwplayer_api_Constants::OPTION_COLOR_BACK   => 'Background color',//>(translatable)<
                tubepress_jwplayer_api_Constants::OPTION_COLOR_FRONT  => 'Front color',     //>(translatable)<
                tubepress_jwplayer_api_Constants::OPTION_COLOR_LIGHT  => 'Light color',     //>(translatable)<
                tubepress_jwplayer_api_Constants::OPTION_COLOR_SCREEN => 'Screen color',    //>(translatable)<
            ),
            'descriptions' => array(
                tubepress_jwplayer_api_Constants::OPTION_COLOR_BACK   => sprintf('Default is %s', "FFFFFF"),   //>(translatable)<
                tubepress_jwplayer_api_Constants::OPTION_COLOR_FRONT  => sprintf('Default is %s', "000000"),   //>(translatable)<
                tubepress_jwplayer_api_Constants::OPTION_COLOR_LIGHT  => sprintf('Default is %s', "000000"),   //>(translatable)<
                tubepress_jwplayer_api_Constants::OPTION_COLOR_SCREEN => sprintf('Default is %s', "000000"),   //>(translatable)<
            )
        ));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_VALIDATION . '_jwplayer', array(
            'priority' => 3000,
            'map' => array(
                'hexColor' => array(
                    tubepress_jwplayer_api_Constants::OPTION_COLOR_BACK,
                    tubepress_jwplayer_api_Constants::OPTION_COLOR_FRONT,
                    tubepress_jwplayer_api_Constants::OPTION_COLOR_LIGHT,
                    tubepress_jwplayer_api_Constants::OPTION_COLOR_SCREEN,
                )
            )
        ));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_TRIMMER . '_jwplayer', array(

            'priority'    => 3000,
            'charlist'    => '#',
            'ltrim'       => true,
            'optionNames' => array(
                tubepress_jwplayer_api_Constants::OPTION_COLOR_BACK,
                tubepress_jwplayer_api_Constants::OPTION_COLOR_FRONT,
                tubepress_jwplayer_api_Constants::OPTION_COLOR_LIGHT,
                tubepress_jwplayer_api_Constants::OPTION_COLOR_SCREEN,
            )
        ));

        $colors = array(

            tubepress_jwplayer_api_Constants::OPTION_COLOR_BACK,
            tubepress_jwplayer_api_Constants::OPTION_COLOR_FRONT,
            tubepress_jwplayer_api_Constants::OPTION_COLOR_LIGHT,
            tubepress_jwplayer_api_Constants::OPTION_COLOR_SCREEN,
        );

        $fieldIndex = 0;
        foreach ($colors as $color) {

            $this->expectRegistration(

                'jwplayer_field_' . $fieldIndex++,
                'tubepress_core_options_ui_api_FieldInterface'
            )->withFactoryService(tubepress_core_options_ui_api_FieldBuilderInterface::_)
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
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
            ->withArgument($fieldReferences)
            ->withTag('tubepress_core_options_ui_api_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $colors = array(

            tubepress_jwplayer_api_Constants::OPTION_COLOR_BACK,
            tubepress_jwplayer_api_Constants::OPTION_COLOR_FRONT,
            tubepress_jwplayer_api_Constants::OPTION_COLOR_LIGHT,
            tubepress_jwplayer_api_Constants::OPTION_COLOR_SCREEN,
        );

        $mockFieldBuilder = $this->mock(tubepress_core_options_ui_api_FieldBuilderInterface::_);

        foreach ($colors as $color) {

            $mockSpectrumField = $this->mock('tubepress_core_options_ui_api_FieldInterface');
            $mockFieldBuilder->shouldReceive('newInstance')->once()->with($color, 'spectrum')->andReturn($mockSpectrumField);
        }

        return array(
            tubepress_core_url_api_UrlFactoryInterface::_ => tubepress_core_url_api_UrlFactoryInterface::_,
            tubepress_core_template_api_TemplateFactoryInterface::_ => tubepress_core_template_api_TemplateFactoryInterface::_,
            tubepress_core_options_api_ContextInterface::_ => tubepress_core_options_api_ContextInterface::_,
            tubepress_core_options_ui_api_FieldBuilderInterface::_ => $mockFieldBuilder,
            tubepress_core_translation_api_TranslatorInterface::_ => tubepress_core_translation_api_TranslatorInterface::_,
            tubepress_api_util_StringUtilsInterface::_ => tubepress_api_util_StringUtilsInterface::_
        );
    }
}