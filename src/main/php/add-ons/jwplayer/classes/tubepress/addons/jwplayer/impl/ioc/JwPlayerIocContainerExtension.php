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
class tubepress_addons_jwplayer_impl_ioc_JwPlayerIocContainerExtension implements tubepress_api_ioc_ContainerExtensionInterface
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
        $this->_registerPluggables($containerBuilder);

        $this->_registerListeners($containerBuilder);
    }

    private function _registerListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_addons_jwplayer_impl_listeners_template_JwPlayerTemplateVars',
            'tubepress_addons_jwplayer_impl_listeners_template_JwPlayerTemplateVars'
        )->addTag(self::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_api_const_event_EventNames::TEMPLATE_EMBEDDED,
                'method'   => 'onEmbeddedTemplate',
                'priority' => 10000
        ));

        $colors = array(

            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK,
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_FRONT,
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT,
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN,
        );

        foreach ($colors as $color) {

            $containerBuilder->register(

                'jw_color_sanitizer_' . $color,
                'tubepress_impl_listeners_options_ColorSanitizingListener'
            )->addTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array(
                    'event'    => tubepress_api_const_event_EventNames::OPTION_SINGLE_PRE_VALIDATION_SET . ".$color",
                    'method'   => 'onPreValidationOptionSet',
                    'priority' => 9500
            ));
        }
    }

    private function _registerPluggables(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_addons_jwplayer_impl_embedded_JwPlayerPluggableEmbeddedPlayerService',
            'tubepress_addons_jwplayer_impl_embedded_JwPlayerPluggableEmbeddedPlayerService'

        )->addTag(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);

        $containerBuilder->register(

            'tubepress_addons_jwplayer_impl_options_JwPlayerOptionProvider',
            'tubepress_addons_jwplayer_impl_options_JwPlayerOptionProvider'
        )->addTag(tubepress_spi_options_OptionProvider::_);

        $fieldIndex = 0;
        $fieldMap = array(

            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK   => 'tubepress_impl_options_ui_fields_SpectrumColorField',
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_FRONT  => 'tubepress_impl_options_ui_fields_SpectrumColorField',
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT  => 'tubepress_impl_options_ui_fields_SpectrumColorField',
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN => 'tubepress_impl_options_ui_fields_SpectrumColorField',
        );

        foreach ($fieldMap as $name => $class) {

            $containerBuilder->register('jwplayer_field_' . $fieldIndex++, $class)->addArgument($name);
        }

        $fieldReferences = array();

        for ($x = 0 ; $x < $fieldIndex; $x++) {

            $fieldReferences[] = new tubepress_impl_ioc_Reference('jwplayer_field_' . $x);
        }

        $map = array(

            tubepress_addons_core_api_const_options_ui_OptionsPageParticipantConstants::CATEGORY_ID_PLAYER => array(

                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK,
                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_FRONT,
                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT,
                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN)
        );

        $containerBuilder->register(

            'jw_player_options_page_participant',
            'tubepress_impl_options_ui_BaseOptionsPageParticipant'
        )->addArgument('jwplayer_participant')
         ->addArgument('JW Player')     //>(translatable)<)
         ->addArgument(array())
         ->addArgument($fieldReferences)
         ->addArgument($map)
         ->addArgument(false)
         ->addArgument(true)
         ->addTag('tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface');
    }
}
