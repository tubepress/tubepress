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
class tubepress_plugins_vimeo_impl_patterns_ioc_VimeoIocContainerExtension implements ehough_iconic_api_extension_IExtension
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

            'tubepress_plugins_vimeo_impl_provider_VimeoUrlBuilder',
            'tubepress_plugins_vimeo_impl_provider_VimeoUrlBuilder'
        );

        $container->register(

            'tubepress_plugins_vimeo_impl_embedded_VimeoPluggableEmbeddedPlayerService',
            'tubepress_plugins_vimeo_impl_embedded_VimeoPluggableEmbeddedPlayerService'

        )->addTag(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);

        $container->register(

            'tubepress_plugins_vimeo_impl_provider_VimeoPluggableVideoProviderService',
            'tubepress_plugins_vimeo_impl_provider_VimeoPluggableVideoProviderService'

        )->addArgument(new ehough_iconic_impl_Reference('tubepress_plugins_vimeo_impl_provider_VimeoUrlBuilder'))
         ->addTag(tubepress_spi_provider_PluggableVideoProviderService::_);

        $container->register(

            'tubepress_plugins_vimeo_impl_options_ui_VimeoPluggableOptionsPageParticipant',
            'tubepress_plugins_vimeo_impl_options_ui_VimeoPluggableOptionsPageParticipant'

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
        return 'vimeo';
    }
}