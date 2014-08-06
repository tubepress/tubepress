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
class tubepress_jwplayer5_impl_listeners_embedded_EmbeddedListener
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
     * @param tubepress_platform_api_url_UrlFactoryInterface $urlFactory URL factory
     * @param string                                         $mediaId    The video ID to play
     *
     * @return tubepress_platform_api_url_UrlInterface The URL of the data for this video.
     *
     * @api
     * @since 4.0.0
     */
    public function getDataUrlForMediaItem(tubepress_platform_api_url_UrlFactoryInterface $urlFactory, $mediaId)
    {
        return $urlFactory->fromString(sprintf('http://www.youtube.com/watch?v=%s', $mediaId));
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

            'embedded/jwplayer5.tpl.php',
            'embedded/longtail.tpl.php',
            TUBEPRESS_ROOT . '/src/add-ons/jwplayer5/templates/jwplayer5.tpl.php'
        );
    }

    /**
     * @return string The display name of this embedded player service.
     *
     * @api
     * @since 4.0.0
     */
    public function getUntranslatedDisplayName()
    {
        return 'JW Player (by Longtail Video)';  //>(translatable)<
    }

    /**
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
