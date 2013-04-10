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
 * Registers a few extensions to allow TubePress to work with YouTube.
 */
class tubepress_plugins_youtube_impl_patterns_ioc_YouTubeIocContainerExtension implements ehough_iconic_extension_ExtensionInterface
{

    /**
     * Loads a specific configuration.
     *
     * @param array            $config    An array of configuration values
     * @param ehough_iconic_ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws ehough_iconic_exception_InvalidArgumentException When provided tag is not defined in this extension
     *
     * @api
     */
    public final function load(array $config, ehough_iconic_ContainerBuilder $container)
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

        )->addArgument(new ehough_iconic_Reference('tubepress_plugins_youtube_impl_provider_YouTubeUrlBuilder'))
         ->addTag(tubepress_spi_provider_PluggableVideoProviderService::_);

        $container->register(

            'tubepress_plugins_youtube_impl_options_ui_YouTubeOptionsPageParticipant',
            'tubepress_plugins_youtube_impl_options_ui_YouTubeOptionsPageParticipant'

        )->addTag(tubepress_spi_options_ui_PluggableOptionsPageParticipant::_);

        $container->register(

            'tubepress_plugins_youtube_impl_filters_video_YouTubeVideoConstructionFilter',
            'tubepress_plugins_youtube_impl_filters_video_YouTubeVideoConstructionFilter'
        );

        $container->register(

            'tubepress_plugins_youtube_impl_http_responsehandling_YouTubeHttpErrorResponseHandler',
            'tubepress_plugins_youtube_impl_http_responsehandling_YouTubeHttpErrorResponseHandler'
        )->addTag('tubepress.impl.http.ResponseHandler');
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

    /**
     * Returns the namespace to be used for this extension (XML namespace).
     *
     * @return string The XML namespace
     *
     * @api
     */
    public function getNamespace()
    {
        return null;
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     *
     * @api
     */
    public function getXsdValidationBasePath()
    {
        return null;
    }
}