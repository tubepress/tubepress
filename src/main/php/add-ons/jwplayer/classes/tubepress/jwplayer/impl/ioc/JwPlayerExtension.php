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
 * Registers a few extensions to allow TubePress to work with EmbedPlus.
 */
class tubepress_jwplayer_impl_ioc_JwPlayerExtension implements tubepress_api_ioc_ContainerExtensionInterface
{
    /**
     * Allows extensions to load services into the TubePress IOC container.
     *
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder A tubepress_api_ioc_ContainerBuilderInterface instance.
     *
     * @return void
     *
     * @api
     * @since 3.1.0
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_jwplayer_impl_embedded_JwPlayerEmbeddedProvider',
            'tubepress_jwplayer_impl_embedded_JwPlayerEmbeddedProvider'

        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_url_UrlFactoryInterface::_))
            ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_template_TemplateFactoryInterface::_))
            ->addTag(tubepress_core_api_embedded_EmbeddedProviderInterface::_);

        $containerBuilder->register(

            'tubepress_jwplayer_impl_listeners_template_JwPlayerTemplateVars',
            'tubepress_jwplayer_impl_listeners_template_JwPlayerTemplateVars'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_options_ContextInterface::_))
         ->addTag(tubepress_core_api_const_ioc_Tags::EVENT_LISTENER, array(
                'event'    => tubepress_core_api_const_event_EventNames::TEMPLATE_EMBEDDED,
                'method'   => 'onEmbeddedTemplate',
                'priority' => 10000
        ));

        $containerBuilder->register(

            'tubepress_jwplayer_impl_options_JwPlayerOptionProvider',
            'tubepress_jwplayer_impl_options_JwPlayerOptionProvider'
        )->addTag(tubepress_core_api_options_EasyProviderInterface::_);

        $colors = array(

            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_BACK,
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_FRONT,
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT,
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN,
        );

        foreach ($colors as $color) {

            $containerBuilder->register(

                'jw_color_sanitizer_' . $color,
                'stdClass'
            )->addTag(tubepress_core_api_const_ioc_Tags::LTRIM_SUBJECT_LISTENER, array(
                'event'    => tubepress_core_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . ".$color",
                'charlist' => '#',
                'priority' => 9500
            ));
        }

        $fieldIndex = 0;
        foreach ($colors as $color) {

            $containerBuilder->register(

                'jwplayer_field_' . $fieldIndex++,
                'tubepress_core_api_options_ui_FieldInterface'
            )->setFactoryService(new tubepress_api_ioc_Reference(tubepress_core_api_options_ui_FieldBuilderInterface::_))
             ->setFactoryMethod('newInstance')
             ->addArgument($color)
             ->addArgument('spectrum');
        }

        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; $x++) {
            $fieldReferences[] = new tubepress_api_ioc_Reference('jwplayer_field_' . $x);
        }

        $containerBuilder->register(

            'jw_player_field_provider',
            'tubepress_jwplayer_impl_options_ui_JwPlayerFieldProvider'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_api_translation_TranslatorInterface::_))
         ->addArgument($fieldReferences)
         ->addTag('tubepress_core_api_options_ui_FieldProviderInterface');
    }
}