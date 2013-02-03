<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Registers a few extensions to allow TubePress to work with EmbedPlus.
 */
class tubepress_plugins_jwplayer_impl_patterns_ioc_JwPlayerIocContainerExtension implements ehough_iconic_api_extension_IExtension
{
    /**
     * Loads a specific configuration.
     *
     * @param ehough_iconic_impl_ContainerBuilder $container A ContainerBuilder instance
     *
     * @return void
     */
    public final function load(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            'tubepress_plugins_jwplayer_impl_embedded_JwPlayerPluggableEmbeddedPlayerService',
            'tubepress_plugins_jwplayer_impl_embedded_JwPlayerPluggableEmbeddedPlayerService'

        )->addTag(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);

        $container->register(

            'tubepress_plugins_jwplayer_impl_options_ui_JwPlayerOptionsPageParticipant',
            'tubepress_plugins_jwplayer_impl_options_ui_JwPlayerOptionsPageParticipant'

        )->addTag(tubepress_spi_options_ui_PluggableOptionsPageParticipant::_);
    }

    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     */
    public final function getAlias()
    {
        return 'jwplayer';
    }
}
