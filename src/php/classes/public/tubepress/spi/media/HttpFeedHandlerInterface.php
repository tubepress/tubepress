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
 * Collects items from providers.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_spi_media_HttpFeedHandlerInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_spi_media_HttpFeedHandlerInterface';

    /**
     * @return string The name of this feed handler. Never empty or null. All lowercase alphanumerics and dashes.
     *
     * @api
     * @since 4.0.0
     */
    function getName();

    /**
     * Gather data that might be needed from the feed to build attributes for this media item.
     *
     * @param tubepress_api_media_MediaItem $mediaItemId The media item.
     * @param int                               $index       The zero-based index.
     *
     * @return array
     *
     * @api
     * @since 4.0.0
     */
    function getNewItemEventArguments(tubepress_api_media_MediaItem $mediaItemId, $index);

    /**
     * Builds a request url for a single media item
     *
     * @param string $id The media item ID to search for
     *
     * @throws InvalidArgumentException If unable to build a URL for the given media item.
     *
     * @return tubepress_api_url_UrlInterface The URL for the single media item given.
     *
     * @api
     * @since 4.0.0
     */
    function buildUrlForItem($id);

    /**
     * Builds a URL for a list of media items
     *
     * @param int $currentPage The current page number of the gallery.
     *
     * @return tubepress_api_url_UrlInterface The request URL for this gallery.
     *
     * @api
     * @since 4.0.0
     */
    function buildUrlForPage($currentPage);

    /**
     * Count the number of media items that we think are in this feed.
     *
     * @return integer A count of media items in this feed.
     *
     * @api
     * @since 4.0.0
     */
    function getCurrentResultCount();

    /**
     * @param int $zeroBasedIndex
     *
     * @return string|null The item ID for the item at the given index, or null if unable to determine.
     */
    function getIdForItemAtIndex($zeroBasedIndex);

    /**
     * Determine why the given item cannot be used.
     *
     * @param integer $index The index into the feed.
     *
     * @return string The reason why we can't work with this media item, or null if we can.
     *
     * @api
     * @since 4.0.0
     */
    function getReasonUnableToUseItemAtIndex($index);

    /**
     * Count the total media items in this feed result.
     *
     * @return int The total result count of this query.
     *
     * @api
     * @since 4.0.0
     */
    function getTotalResultCount();

    /**
     * Perform post-construction activites for the feed.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    function onAnalysisComplete();

    /**
     * Perform pre-construction activites for the feed.
     *
     * @param mixed                          $feed The feed to construct.
     * @param tubepress_api_url_UrlInterface $url  The URL just fetched.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    function onAnalysisStart($feed, tubepress_api_url_UrlInterface $url);
}