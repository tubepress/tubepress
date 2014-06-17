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
 * An embedded media player.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_core_embedded_api_EmbeddedProviderInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_core_embedded_api_EmbeddedProviderInterface';

    /**
     * @return string The name of this embedded player. Never empty or null. All lowercase alphanumerics and dashes.
     *
     * @api
     * @since 4.0.0
     */
    function getName();

    /**
     * @return string The display name of this embedded player service.
     *
     * @api
     * @since 4.0.0
     */
    function getUntranslatedDisplayName();

    /**
     * @return string[] The paths, to pass to the template factory, for this embedded provider.
     *
     * @api
     * @since 4.0.0
     */
    function getPathsForTemplateFactory();

    /**
     * @param tubepress_core_url_api_UrlFactoryInterface               $urlFactory URL factory
     * @param tubepress_core_media_provider_api_MediaProviderInterface $provider   The media provider
     * @param string                                                   $mediaId    The media ID to play
     *
     * @return tubepress_core_url_api_UrlInterface The URL of the data for this item.
     *
     * @api
     * @since 4.0.0
     */
    function getDataUrlForMediaItem(tubepress_core_url_api_UrlFactoryInterface               $urlFactory,
                                    tubepress_core_media_provider_api_MediaProviderInterface $provider,
                                    $mediaId);

    /**
     * @param tubepress_core_media_provider_api_MediaProviderInterface
     *
     * @return string[] An array of provider names that this embedded provider can handle.
     *
     * @api
     * @since 4.0.0
     */
    function getCompatibleProviderNames();
}