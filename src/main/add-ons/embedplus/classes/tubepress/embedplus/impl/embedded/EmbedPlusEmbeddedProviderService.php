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
 * Plays videos with EmbedPlus.
 */
class tubepress_embedplus_impl_embedded_EmbedPlusEmbeddedProviderService implements tubepress_core_embedded_api_EmbeddedProviderInterface
{
    /**
     * @return string The name of this embedded player. Never empty or null. All lowercase alphanumerics and dashes.
     *
     * @api
     * @since 4.0.0
     */
    public function getName()
    {
        return 'embedplus';
    }

    /**
     * @return string[] The paths, to pass to the template factory, for this embedded provider.
     *
     * @api
     * @since 4.0.0
     */
    public function getPathsForTemplateFactory()
    {
        return array(

            'embedded/embedplus.tpl.php',
            TUBEPRESS_ROOT . '/src/main/add-ons/embedplus/resources/templates/embedded/embedplus.tpl.php'
        );
    }

    /**
     * @param tubepress_core_url_api_UrlFactoryInterface         $urlFactory URL factory
     * @param tubepress_core_provider_api_MediaProviderInterface $provider   The video provider
     * @param string                                             $videoId    The video ID to play
     *
     * @return tubepress_core_url_api_UrlInterface The URL of the data for this video.
     *
     * @api
     * @since 4.0.0
     */
    public function getDataUrlForVideo(tubepress_core_url_api_UrlFactoryInterface $urlFactory,
                                tubepress_core_provider_api_MediaProviderInterface $provider,
                                $videoId)
    {
        return $urlFactory->fromString(sprintf('http://www.youtube.com/embed/%s', $videoId));
    }

    /**
     * @return string The friendly name of this embedded player service.
     *
     * @api
     * @since 4.0.0
     */
    public function getUntranslatedDisplayName()
    {
        return 'EmbedPlus';
    }

    /**
     * @param tubepress_core_provider_api_MediaProviderInterface
     *
     * @return string[] An array of provider names that this embedded provider can handle.
     *
     * @api
     * @since 4.0.0
     */
    public function getCompatibleProviderNames()
    {
        return array('youtube');
    }
}