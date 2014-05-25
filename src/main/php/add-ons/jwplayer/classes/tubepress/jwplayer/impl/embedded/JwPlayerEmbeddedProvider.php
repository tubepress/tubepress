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
 * Plays videos with JW Player.
 */
class tubepress_jwplayer_impl_embedded_JwPlayerEmbeddedProvider implements tubepress_core_api_embedded_EmbeddedProviderInterface
{
    /**
     * @return string The name of this embedded player. Never empty or null. All lowercase alphanumerics and dashes.
     *
     * @api
     * @since 4.0.0
     */
    public function getName()
    {
        return 'longtail';
    }

    /**
     * @param tubepress_core_api_url_UrlFactoryInterface         $urlFactory URL factory
     * @param tubepress_core_api_provider_VideoProviderInterface $provider   The video provider
     * @param string                                             $videoId    The video ID to play
     *
     * @return tubepress_core_api_url_UrlInterface The URL of the data for this video.
     *
     * @api
     * @since 4.0.0
     */
    public function getDataUrlForVideo(tubepress_core_api_url_UrlFactoryInterface $urlFactory,
                                tubepress_core_api_provider_VideoProviderInterface $provider,
                                $videoId)
    {
        return $urlFactory->fromString(sprintf('http://www.youtube.com/watch?v=%s', $videoId));
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

            'embedded/longtail.tpl.php',
            TUBEPRESS_ROOT . '/src/main/php/add-ons/jwplayer/resources/templates'
        );
    }

    /**
     * @return string The friendly name of this embedded player service.
     *
     * @api
     * @since 4.0.0
     */
    public function getUntranslatedDisplayName()
    {
        return 'JW Player (by Longtail Video)';  //>(translatable)<
    }

    /**
     * @param tubepress_core_api_provider_VideoProviderInterface
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
