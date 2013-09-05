<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
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
     * @param tubepress_api_ioc_ContainerInterface $container A tubepress_api_ioc_ContainerInterface instance.
     *
     * @return void
     *
     * @api
     * @since 3.1.0
     */
    public function load(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            'tubepress_addons_jwplayer_impl_embedded_JwPlayerPluggableEmbeddedPlayerService',
            'tubepress_addons_jwplayer_impl_embedded_JwPlayerPluggableEmbeddedPlayerService'

        )->addTag(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);

        $container->register(

            'tubepress_addons_jwplayer_impl_options_ui_JwPlayerOptionsPageParticipant',
            'tubepress_addons_jwplayer_impl_options_ui_JwPlayerOptionsPageParticipant'

        )->addTag('tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface');

        $container->register(

            'tubepress_addons_jwplayer_impl_options_JwPlayerOptionsProvider',
            'tubepress_addons_jwplayer_impl_options_JwPlayerOptionsProvider'
        )->addTag(tubepress_spi_options_PluggableOptionDescriptorProvider::_);

        $container->register(

            'tubepress_addons_jwplayer_impl_listeners_template_JwPlayerTemplateVars',
            'tubepress_addons_jwplayer_impl_listeners_template_JwPlayerTemplateVars'
        )->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::TEMPLATE_EMBEDDED, 'method' => 'onEmbeddedTemplate', 'priority' => 10000));

        $container->register(

            'jw-color-sanitizer',
            'tubepress_impl_listeners_options_ColorSanitizingListener'
        )->addMethodCall('setApplicableOptionNames', array(array(
                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK,
                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_FRONT,
                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT,
                tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN,
            )))
         ->addTag(self::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::OPTIONS_NVP_PREVALIDATIONSET, 'method' => 'onPreValidationOptionSet', 'priority' => 9500));
    }
}
