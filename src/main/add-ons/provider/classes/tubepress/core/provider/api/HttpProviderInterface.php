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
 * @api
 * @since 4.0.0
 */
interface tubepress_core_provider_api_HttpProviderInterface extends tubepress_core_provider_api_MediaProviderInterface
{
    /**
     * Builds a URL for a list of videos
     *
     * @param int $currentPage The current page number of the gallery.
     *
     * @return tubepress_core_url_api_UrlInterface The request URL for this gallery.
     *
     * @api
     * @since 4.0.0
     */
    function buildUrlForPage($currentPage);

    /**
     * Builds a request url for a single video
     *
     * @param string $id The video ID to search for
     *
     * @throws InvalidArgumentException If unable to build a URL for the given video.
     *
     * @return tubepress_core_url_api_UrlInterface The URL for the single video given.
     *
     * @api
     * @since 4.0.0
     */
    function buildUrlForSingle($id);

    /**
     * Count the total videos in this feed result.
     *
     * @param mixed $feed The raw feed from the provider.
     *
     * @return int The total result count of this query.
     *
     * @api
     * @since 4.0.0
     */
    function getTotalResultCount($feed);

    /**
     * Count the number of videos that we think are in this feed.
     *
     * @param mixed $feed The feed.
     *
     * @return integer A count of videos in this feed.
     *
     * @api
     * @since 4.0.0
     */
    function getCurrentResultCount($feed);

    /**
     * Determine if we can build a video from this element of the feed.
     *
     * @param integer $index The index into the feed.
     * @param mixed   $feed  The raw feed.
     *
     * @return boolean True if we can build a video from this element, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    function canWorkWithItemAtIndex($index, $feed);

    /**
     * Determine why the given item cannot be used.
     *
     * @param integer $index The index into the feed.
     * @param mixed   $feed  The raw feed.
     *
     * @return string The reason why we can't work with this video, or null if we can.
     *
     * @api
     * @since 4.0.0
     */
    function getReasonUnableToWorkWithItemAtIndex($index, $feed);

    /**
     * Perform pre-construction activites for the feed.
     *
     * @param mixed $feed The feed to construct.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    function onAnalysisStart($feed);

    /**
     * Perform post-construction activites for the feed.
     *
     * @param mixed $feed The feed we used.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    function onAnalysisComplete($feed);

    /**
     * Let's subclasses add arguments to the video construction event.
     *
     * @param tubepress_core_event_api_EventInterface $event The event we're about to fire.
     *
     * @api
     * @since 4.0.0
     */
    function onPreFireNewMediaItemEvent(tubepress_core_event_api_EventInterface $event);
}