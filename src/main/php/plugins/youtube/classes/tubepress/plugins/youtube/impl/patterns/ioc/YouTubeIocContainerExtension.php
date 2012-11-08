<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Registers a few extensions to allow TubePress to work with YouTube.
 */
class tubepress_plugins_youtube_impl_patterns_ioc_YouTubeIocContainerExtension implements ehough_iconic_api_extension_IExtension
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

            'tubepress_plugins_youtube_impl_provider_YouTubeUrlBuilder',
            'tubepress_plugins_youtube_impl_provider_YouTubeUrlBuilder'
        );

        $container->register(

            'tubepress_plugins_youtube_impl_embedded_YouTubePluggableEmbeddedPlayerService',
            'tubepress_plugins_youtube_impl_embedded_YouTubePluggableEmbeddedPlayerService'

        )->addTag(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);

        $container->register(

            'tubepress_plugins_youtube_impl_provider_YouTubePluggableVideoProviderService',
            'tubepress_plugins_youtube_impl_provider_YouTubePluggableVideoProviderService'

        )->addArgument(new ehough_iconic_impl_Reference('tubepress_plugins_youtube_impl_provider_YouTubeUrlBuilder'))
         ->addTag(tubepress_spi_provider_PluggableVideoProviderService::_);

        $container->register(

            'tubepress_plugins_youtube_impl_options_ui_YouTubeOptionsPageParticipant',
            'tubepress_plugins_youtube_impl_options_ui_YouTubeOptionsPageParticipant'

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
        return 'youtube';
    }
}