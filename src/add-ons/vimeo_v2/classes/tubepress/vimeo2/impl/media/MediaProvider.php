<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Handles the heavy lifting for Vimeo.
 */
class tubepress_vimeo2_impl_media_MediaProvider implements tubepress_app_api_media_MediaProviderInterface
{
    private static $_GALLERY_SOURCE_NAMES = array(

        tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_ALBUM,
        tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_APPEARS_IN,
        tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL,
        tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_CREDITED,
        tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_GROUP,
        tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_LIKES,
        tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_SEARCH,
        tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY
    );

    private static $_MODE_TEMPLATE_MAP = array(

        tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_ALBUM      => 'album %s',
        tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_APPEARS_IN => 'videos in which %s appears',
        tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL    => 'channel %s',
        tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_CREDITED   => 'credited to %s',
        tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_GROUP      => 'group %s',
        tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_LIKES      => 'liked by %s',
        tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_SEARCH     => 'matching search term %s',
        tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY => 'uploads of %s',
    );

    /**
     * @var tubepress_app_api_media_HttpCollectorInterface
     */
    private $_httpCollector;

    /**
     * @var tubepress_app_api_media_HttpFeedHandlerInterface
     */
    private $_feedHandler;

    /**
     * @var tubepress_platform_api_collection_MapInterface
     */
    private $_properties;

    public function __construct(tubepress_app_api_media_HttpCollectorInterface     $httpCollector,
                                tubepress_app_api_media_HttpFeedHandlerInterface   $feedHandler,
                                tubepress_app_api_environment_EnvironmentInterface $environment)
    {
        $this->_httpCollector = $httpCollector;
        $this->_feedHandler   = $feedHandler;
        $this->_properties    = new tubepress_platform_impl_collection_Map();

        $baseUrlClone = $environment->getBaseUrl()->getClone();
        $miniIconUrl  = $baseUrlClone->addPath('src/add-ons/vimeo_v2/web/images/icons/vimeo-icon-34w_x_34h.png')->toString();
        $this->getProperties()->put('miniIconUrl', $miniIconUrl);
        $this->getProperties()->put('untranslatedModeTemplateMap', self::$_MODE_TEMPLATE_MAP);
    }


    /**
     * @return array An array of the valid option valu
es for the "mode" option.
     */
    public function getGallerySourceNames()
    {
        return self::$_GALLERY_SOURCE_NAMES;
    }

    /**
     * @return string The name of this video provider. Never empty or null. All lowercase alphanumerics and dashes.
     */
    public function getName()
    {
        return 'vimeo';
    }

    /**
     * @return string The human-readable name of this video provider.
     */
    public function getDisplayName()
    {
        return 'Vimeo';
    }

    /**
     * @return array An array of meta names
     */
    public function getMapOfMetaOptionNamesToAttributeDisplayNames()
    {
        return array(

            tubepress_app_api_options_Names::META_DISPLAY_TITLE       => tubepress_app_api_media_MediaItem::ATTRIBUTE_TITLE,
            tubepress_app_api_options_Names::META_DISPLAY_LENGTH      => tubepress_app_api_media_MediaItem::ATTRIBUTE_DURATION_FORMATTED,
            tubepress_app_api_options_Names::META_DISPLAY_AUTHOR      => tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME,
            tubepress_app_api_options_Names::META_DISPLAY_KEYWORDS    => tubepress_app_api_media_MediaItem::ATTRIBUTE_KEYWORDS_FORMATTED,
            tubepress_app_api_options_Names::META_DISPLAY_URL         => tubepress_app_api_media_MediaItem::ATTRIBUTE_HOME_URL,

            tubepress_vimeo2_api_Constants::OPTION_LIKES               => tubepress_app_api_media_MediaItem::ATTRIBUTE_LIKES_COUNT,

            tubepress_app_api_options_Names::META_DISPLAY_ID          => tubepress_app_api_media_MediaItem::ATTRIBUTE_ID,
            tubepress_app_api_options_Names::META_DISPLAY_VIEWS       => tubepress_app_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT,
            tubepress_app_api_options_Names::META_DISPLAY_UPLOADED    => tubepress_app_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_FORMATTED,
            tubepress_app_api_options_Names::META_DISPLAY_DESCRIPTION => tubepress_app_api_media_MediaItem::ATTRIBUTE_DESCRIPTION,
        );
    }

    /**
     * Ask this media provider if it recognizes the given item ID.
     *
     * @param string $mediaId The globally unique media item identifier.
     *
     * @return boolean True if this provider recognizes the given item ID, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function ownsItem($mediaId)
    {
        return is_numeric($mediaId);
    }

    /**
     * @return string The name of the "mode" value that this provider uses for searching.
     *
     * @api
     * @since 4.0.0
     */
    public function getSearchModeName()
    {
        return tubepress_vimeo2_api_Constants::GALLERYSOURCE_VIMEO_SEARCH;
    }

    /**
     * @return string The option name where TubePress should put the users search results.
     *
     * @api
     * @since 4.0.0
     */
    public function getSearchQueryOptionName()
    {
        return tubepress_vimeo2_api_Constants::OPTION_VIMEO_SEARCH_VALUE;
    }

    /**
     * @return array
     *
     * @api
     * @since 4.0.0
     */
    public function getMapOfFeedSortNamesToUntranslatedLabels()
    {
        return array(

            tubepress_vimeo2_api_Constants::ORDER_BY_COMMENT_COUNT => 'comment count',                   //>(translatable)<
            tubepress_vimeo2_api_Constants::ORDER_BY_DEFAULT       => 'default',                         //>(translatable)<
            tubepress_vimeo2_api_Constants::ORDER_BY_NEWEST        => 'date published (newest first)',   //>(translatable)<
            tubepress_vimeo2_api_Constants::ORDER_BY_OLDEST        => 'date published (oldest first)',   //>(translatable)<
            tubepress_vimeo2_api_Constants::ORDER_BY_RANDOM        => 'randomly',                        //>(translatable)<
            tubepress_vimeo2_api_Constants::ORDER_BY_RATING        => 'rating',                          //>(translatable)<
            tubepress_vimeo2_api_Constants::ORDER_BY_RELEVANCE     => 'relevance',                       //>(translatable)<
            tubepress_vimeo2_api_Constants::ORDER_BY_VIEW_COUNT    => 'view count',                      //>(translatable)<
        );
    }

    /**
     * Collects a media gallery page.
     *
     * @param int $pageNumber The page number.
     *
     * @return tubepress_app_api_media_MediaPage The media gallery page, never null.
     *
     * @api
     * @since 4.0.0
     */
    public function collectPage($pageNumber)
    {
        return $this->_httpCollector->collectPage($pageNumber, $this->_feedHandler);
    }

    /**
     * Fetch a single media item.
     *
     * @param string $id The media item ID to fetch.
     *
     * @return tubepress_app_api_media_MediaItem The media item, or null not found.
     *
     * @api
     * @since 4.0.0
     */
    public function collectSingle($id)
    {
        return $this->_httpCollector->collectSingle($id, $this->_feedHandler);
    }

    /**
     * @api
     * @since 4.1.11
     *
     * @return tubepress_platform_api_collection_MapInterface
     */
    public function getProperties()
    {
        return $this->_properties;
    }
}