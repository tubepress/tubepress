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
interface tubepress_core_api_provider_EasyHttpProviderInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_core_api_provider_EasyHttpProviderInterface';

    /**
     * Builds a URL for a list of videos
     *
     * @param int $currentPage The current page number of the gallery.
     *
     * @return tubepress_core_api_url_UrlInterface The request URL for this gallery.
     *
     * @api
     * @since 4.0.0
     */
    function urlBuildForGallery($currentPage);

    /**
     * Builds a request url for a single video
     *
     * @param string $id The video ID to search for
     *
     * @throws InvalidArgumentException If unable to build a URL for the given video.
     *
     * @return tubepress_core_api_url_UrlInterface The URL for the single video given.
     *
     * @api
     * @since 4.0.0
     */
    function urlBuildForSingle($id);

    /**
     * Count the total videos in this feed result.
     *
     * @param mixed $feed The raw feed from the provider.
     *
     * @return int The total result count of this query, or 0 if there was a problem.
     *
     * @api
     * @since 4.0.0
     */
    function feedGetTotalResultCount($feed);

    /**
     * Determine if we can build a video from this element of the feed.
     *
     * @param integer $index The index into the feed.
     *
     * @return boolean True if we can build a video from this element, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    function feedCanWorkWithVideoAtIndex($index);

    /**
     * Count the number of videos that we think are in this feed.
     *
     * @param mixed $feed The feed.
     *
     * @return integer An estimated count of videos in this feed.
     *
     * @api
     * @since 4.0.0
     */
    function feedCountElements($feed);

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
    function freePrepareForAnalysis($feed);

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
    function feedOnAnalysisComplete($feed);

    /**
     * Let's subclasses add arguments to the video construction event.
     *
     * @param tubepress_core_api_event_EventInterface $event The event we're about to fire.
     *
     * @api
     * @since 4.0.0
     */
    function singleElementOnBeforeConstruction(tubepress_core_api_event_EventInterface $event);

    /**
     * Ask this video provider if it recognizes the given video ID.
     *
     * @param string $videoId The globally unique video identifier.
     *
     * @return boolean True if this provider recognizes the given video ID, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    function singleElementRecognizesId($videoId);

    /**
     * @return array An array of the valid option values for the "mode" option.
     *
     * @api
     * @since 4.0.0
     */
    function getGallerySourceNames();

    /**
     * @return string The name of this video provider. Never empty or null. All lowercase alphanumerics and dashes.
     *
     * @api
     * @since 4.0.0
     */
    function getName();

    /**
     * @return string The human-readable name of this video provider.
     *
     * @api
     * @since 4.0.0
     */
    function getFriendlyName();

    /**
     * @return array An array of meta names
     *
     * @apiu
     * @since 4.0.0
     */
    function getAdditionalMetaNames();

}