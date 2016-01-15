<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 * 
 * This file is part of TubePress (http://tubepress.com)
 * 
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @api
 * @since 4.0.0
 */
interface tubepress_spi_media_MediaProviderInterface extends tubepress_api_media_CollectorInterface
{
    /**
     * @api
     * @since 4.0.0
     */
    const __ = 'tubepress_spi_media_MediaProviderInterface';

    /**
     * @return string The human-readable name of this media provider.
     *
     * @api
     * @since 4.0.0
     */
    function getDisplayName();

    /**
     * @return array An array of the valid option values for the "mode" option.
     *
     * @api
     * @since 4.0.0
     */
    function getGallerySourceNames();

    /**
     * @return string The name of this media provider. Never empty or null. All lowercase alphanumerics and dashes.
     *
     * @api
     * @since 4.0.0
     */
    function getName();

    /**
     * @return array
     *
     * @api
     * @since 4.0.0
     */
    function getMapOfFeedSortNamesToUntranslatedLabels();

    /**
     * @return array An associative array where the keys are this providers meta
     *               option names and the values are the corresponding media item
     *               attribute names.
     *
     * @api
     * @since 4.0.0
     */
    function getMapOfMetaOptionNamesToAttributeDisplayNames();

    /**
     * @return string The name of the "mode" value that this provider uses for searching, or null
     *                if this provider does not support searching.
     *
     * @api
     * @since 4.0.0
     */
    function getSearchModeName();

    /**
     * @return string The option name where TubePress should put the users search results.
     *
     * @api
     * @since 4.0.0
     */
    function getSearchQueryOptionName();

    /**
     * @param string $itemId The item ID.
     *
     * @return bool True if this provider "owns" the given item ID, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    function ownsItem($itemId);

    /**
     * @api
     * @since 4.1.11
     *
     * @return tubepress_api_collection_MapInterface
     */
    function getProperties();
}