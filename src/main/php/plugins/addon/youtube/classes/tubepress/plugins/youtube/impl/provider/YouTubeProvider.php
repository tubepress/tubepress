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
 * Handles the heavy lifting for YouTube.
 */
class tubepress_plugins_youtube_impl_provider_YouTubeProvider implements tubepress_spi_provider_VideoProvider
{
    /**
     * @var array Options that this provider provided.
     */
    private static $_selfSuppliedOptions = array(

        tubepress_api_const_options_names_Embedded::AUTOHIDE,
        tubepress_api_const_options_names_Embedded::FULLSCREEN,
        tubepress_api_const_options_names_Embedded::HIGH_QUALITY,
        tubepress_api_const_options_names_Embedded::MODEST_BRANDING,
        tubepress_api_const_options_names_Embedded::PLAYER_HIGHLIGHT,
        tubepress_api_const_options_names_Embedded::PLAYER_IMPL,
        tubepress_api_const_options_names_Embedded::SHOW_RELATED,
        tubepress_api_const_options_names_Feed::DEV_KEY,
        tubepress_api_const_options_names_Feed::EMBEDDABLE_ONLY,
        tubepress_api_const_options_names_Feed::FILTER,
        tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_FAVORITES_VALUE,
        tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_FEATURED_VALUE,
        tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_DISCUSSED_VALUE,
        tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_RECENT_VALUE,
        tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_RESPONDED_VALUE,
        tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_POPULAR_VALUE,
        tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE,
        tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE,
        tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TOP_FAVORITES_VALUE,
        tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TOP_RATED_VALUE,
        tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE,
        tubepress_api_const_options_names_Meta::RATING,
        tubepress_api_const_options_names_Meta::RATINGS,
        tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS,
    );

    private static $_sourceToSortMap = array(

        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED => array(

            tubepress_api_const_options_values_OrderByValue::RELEVANCE,
            tubepress_api_const_options_values_OrderByValue::NEWEST,
            tubepress_api_const_options_values_OrderByValue::VIEW_COUNT,
            tubepress_api_const_options_values_OrderByValue::RATING
        ),

        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES => array(

            tubepress_api_const_options_values_OrderByValue::RELEVANCE,
            tubepress_api_const_options_values_OrderByValue::NEWEST,
            tubepress_api_const_options_values_OrderByValue::VIEW_COUNT,
            tubepress_api_const_options_values_OrderByValue::RATING
        ),

        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_SHARED => array(

            tubepress_api_const_options_values_OrderByValue::RELEVANCE,
            tubepress_api_const_options_values_OrderByValue::NEWEST,
            tubepress_api_const_options_values_OrderByValue::VIEW_COUNT,
            tubepress_api_const_options_values_OrderByValue::RATING
        ),

        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_POPULAR => array(

            tubepress_api_const_options_values_OrderByValue::RELEVANCE,
            tubepress_api_const_options_values_OrderByValue::NEWEST,
            tubepress_api_const_options_values_OrderByValue::VIEW_COUNT,
            tubepress_api_const_options_values_OrderByValue::RATING
        ),

        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT => array(

            tubepress_api_const_options_values_OrderByValue::RELEVANCE,
            tubepress_api_const_options_values_OrderByValue::NEWEST,
            tubepress_api_const_options_values_OrderByValue::VIEW_COUNT,
            tubepress_api_const_options_values_OrderByValue::RATING
        ),

        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED => array(

            tubepress_api_const_options_values_OrderByValue::RELEVANCE,
            tubepress_api_const_options_values_OrderByValue::NEWEST,
            tubepress_api_const_options_values_OrderByValue::VIEW_COUNT,
            tubepress_api_const_options_values_OrderByValue::RATING
        ),

        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED => array(

            tubepress_api_const_options_values_OrderByValue::RELEVANCE,
            tubepress_api_const_options_values_OrderByValue::NEWEST,
            tubepress_api_const_options_values_OrderByValue::VIEW_COUNT,
            tubepress_api_const_options_values_OrderByValue::RATING
        ),

        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED => array(

            tubepress_api_const_options_values_OrderByValue::RELEVANCE,
            tubepress_api_const_options_values_OrderByValue::NEWEST,
            tubepress_api_const_options_values_OrderByValue::VIEW_COUNT,
            tubepress_api_const_options_values_OrderByValue::RATING
        ),

        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TRENDING => array(

            tubepress_api_const_options_values_OrderByValue::RELEVANCE,
            tubepress_api_const_options_values_OrderByValue::NEWEST,
            tubepress_api_const_options_values_OrderByValue::VIEW_COUNT,
            tubepress_api_const_options_values_OrderByValue::RATING
        ),

        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RELATED => array(

            tubepress_api_const_options_values_OrderByValue::RELEVANCE,
            tubepress_api_const_options_values_OrderByValue::NEWEST,
            tubepress_api_const_options_values_OrderByValue::VIEW_COUNT,
            tubepress_api_const_options_values_OrderByValue::RATING
        ),

        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RESPONSES => array(

            tubepress_api_const_options_values_OrderByValue::RELEVANCE,
            tubepress_api_const_options_values_OrderByValue::NEWEST,
            tubepress_api_const_options_values_OrderByValue::VIEW_COUNT,
            tubepress_api_const_options_values_OrderByValue::RATING
        ),

        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST => array(

            tubepress_api_const_options_values_OrderByValue::POSITION,
            tubepress_api_const_options_values_OrderByValue::COMMENT_COUNT,
            tubepress_api_const_options_values_OrderByValue::DURATION,
            tubepress_api_const_options_values_OrderByValue::NEWEST,
            tubepress_api_const_options_values_OrderByValue::OLDEST,
            tubepress_api_const_options_values_OrderByValue::TITLE,
            tubepress_api_const_options_values_OrderByValue::VIEW_COUNT
        ),

        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES => array(

            tubepress_api_const_options_values_OrderByValue::RELEVANCE,
            tubepress_api_const_options_values_OrderByValue::NEWEST,
            tubepress_api_const_options_values_OrderByValue::VIEW_COUNT,
            tubepress_api_const_options_values_OrderByValue::RATING
        ),

        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH => array(

            tubepress_api_const_options_values_OrderByValue::RELEVANCE,
            tubepress_api_const_options_values_OrderByValue::NEWEST,
            tubepress_api_const_options_values_OrderByValue::VIEW_COUNT,
            tubepress_api_const_options_values_OrderByValue::RATING
        ),

        tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER => array(

            tubepress_api_const_options_values_OrderByValue::RELEVANCE,
            tubepress_api_const_options_values_OrderByValue::NEWEST,
            tubepress_api_const_options_values_OrderByValue::VIEW_COUNT,
            tubepress_api_const_options_values_OrderByValue::RATING
        ),
    );

    /**
     * @return string The human-friendly name of this video provider.
     */
    public final function getFriendlyName()
    {
        return 'YouTube';
    }

    /**
     * @return string The name of this video provider. Never empty or null. All lowercase alphanumerics and dashes.
     */
    public final function getName()
    {
        return 'youtube';
    }

    /**
     * Ask this video provider if it recognizes the given video ID.
     *
     * @param string $videoId The globally unique video identifier.
     *
     * @return boolean True if this provider recognizes the given video ID, false otherwise.
     */
    public final function recognizesVideoId($videoId)
    {
        return preg_match_all('/^[A-Za-z0-9-_]{11}$/', $videoId, $matches) === 1;
    }

    /**
     * Fetch a video gallery page.
     *
     * @param int $currentPage The requested page number of the gallery.
     *
     * @return tubepress_api_video_VideoGalleryPage The video gallery page for this page. May be empty, never null.
     */
    public final function fetchVideoGalleryPage($currentPage)
    {
        // TODO: Implement fetchVideoGalleryPage() method.
    }

    /**
     * Fetch a single video.
     *
     * @param string $videoId The video ID to fetch.
     *
     * @return tubepress_api_video_Video The video, or null if unable to retrive.
     */
    public final function fetchSingleVideo($videoId)
    {
        // TODO: Implement fetchSingleVideo() method.
    }

    /**
     * @param string $optionName The TubePress option name.
     *
     * @return boolean True if this option is applicable to the provider. False otherwise.
     */
    public final function optionIsApplicable($optionName)
    {
        /**
         * If this plugin provided the option, then yes we support it.
         */
        if (in_array($optionName, self::$_selfSuppliedOptions)) {

            return true;
        }
    }

    /**
     * @param string $playerImplementationName The player implementation name.
     *
     * @return boolean True if this provider can play videos with the given player implementation, false otherwise.
     */
    public final function canPlayVideosWithPlayerImplementation($playerImplementationName)
    {
        //currently YouTube can handle any of the providers
        return true;
    }

    /**
     * @return array An associative array where the keys are valid option values for the "mode" option, and the
     *               values are arrays representing the valid options for "orderBy" for the given source value.
     */
    public final function getGallerySourceNamesToSortOptionsMap()
    {
        return self::$_sourceToSortMap;
    }

    /**
     * @return array An array of strings, each representing an option name provided by this provider.
     */
    public final function getProvidedOptionNames()
    {
        return self::$_selfSuppliedOptions;
    }
}
