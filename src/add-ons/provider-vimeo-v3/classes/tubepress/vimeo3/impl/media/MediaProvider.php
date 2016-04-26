<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
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
class tubepress_vimeo3_impl_media_MediaProvider implements tubepress_spi_media_MediaProviderInterface
{
    private static $_GALLERY_SOURCE_NAMES = array(

        tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_ALBUM,
        tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_APPEARS_IN,
        tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_CATEGORY,
        tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL,
        tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_GROUP,
        tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_LIKES,
        tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_SEARCH,
        tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_TAG,
        tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY,
    );

    private static $_MODE_TEMPLATE_MAP = array(

        tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_ALBUM      => 'album %s',
        tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_APPEARS_IN => 'videos in which %s appears',
        tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_CATEGORY   => 'category %s',
        tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_CHANNEL    => 'channel %s',
        tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_GROUP      => 'group %s',
        tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_LIKES      => 'liked by %s',
        tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_SEARCH     => 'matching search term %s',
        tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_TAG        => 'with tag %s',
        tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_UPLOADEDBY => 'uploads of %s',
    );

    /**
     * @var tubepress_api_media_HttpCollectorInterface
     */
    private $_httpCollector;

    /**
     * @var tubepress_spi_media_HttpFeedHandlerInterface
     */
    private $_feedHandler;

    /**
     * @var tubepress_api_collection_MapInterface
     */
    private $_properties;

    public function __construct(tubepress_api_media_HttpCollectorInterface     $httpCollector,
                                tubepress_spi_media_HttpFeedHandlerInterface   $feedHandler,
                                tubepress_api_environment_EnvironmentInterface $environment)
    {
        $this->_httpCollector = $httpCollector;
        $this->_feedHandler   = $feedHandler;
        $this->_properties    = new tubepress_internal_collection_Map();

        $baseUrlClone = $environment->getBaseUrl()->getClone();
        $miniIconUrl  = $baseUrlClone->addPath('src/add-ons/provider-vimeo-v3/web/images/icons/vimeo-icon-34w_x_34h.png')->toString();
        $this->getProperties()->put('miniIconUrl', $miniIconUrl);
        $this->getProperties()->put('untranslatedModeTemplateMap', self::$_MODE_TEMPLATE_MAP);
    }

    /**
     * {@inheritdoc}
     */
    public function getGallerySourceNames()
    {
        return self::$_GALLERY_SOURCE_NAMES;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'vimeo';
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayName()
    {
        return 'Vimeo';
    }

    /**
     * {@inheritdoc}
     */
    public function getMapOfMetaOptionNamesToAttributeDisplayNames()
    {
        return array(

            tubepress_api_options_Names::META_DISPLAY_TITLE    => tubepress_api_media_MediaItem::ATTRIBUTE_TITLE,
            tubepress_api_options_Names::META_DISPLAY_LENGTH   => tubepress_api_media_MediaItem::ATTRIBUTE_DURATION_FORMATTED,
            tubepress_api_options_Names::META_DISPLAY_AUTHOR   => tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME,
            tubepress_api_options_Names::META_DISPLAY_KEYWORDS => tubepress_api_media_MediaItem::ATTRIBUTE_KEYWORDS_FORMATTED,
            tubepress_api_options_Names::META_DISPLAY_URL      => tubepress_api_media_MediaItem::ATTRIBUTE_HOME_URL,

            tubepress_vimeo3_api_Constants::OPTION_LIKES => tubepress_api_media_MediaItem::ATTRIBUTE_LIKES_COUNT,

            tubepress_api_options_Names::META_DISPLAY_ID          => tubepress_api_media_MediaItem::ATTRIBUTE_ID,
            tubepress_api_options_Names::META_DISPLAY_VIEWS       => tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT,
            tubepress_api_options_Names::META_DISPLAY_UPLOADED    => tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_FORMATTED,
            tubepress_api_options_Names::META_DISPLAY_DESCRIPTION => tubepress_api_media_MediaItem::ATTRIBUTE_DESCRIPTION,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function ownsItem($mediaId)
    {
        return is_numeric($mediaId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchModeName()
    {
        return tubepress_vimeo3_api_Constants::GALLERYSOURCE_VIMEO_SEARCH;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryOptionName()
    {
        return tubepress_vimeo3_api_Constants::OPTION_VIMEO_SEARCH_VALUE;
    }

    /**
     * {@inheritdoc}
     */
    public function getMapOfFeedSortNamesToUntranslatedLabels()
    {
        return array(

            tubepress_vimeo3_api_Constants::ORDER_BY_ALPHABETICAL_A_Z => 'title (alphabetical)',            //>(translatable)<
            tubepress_vimeo3_api_Constants::ORDER_BY_ALPHABETICAL_Z_A => 'title (reverse alphabetical)',    //>(translatable)<
            tubepress_vimeo3_api_Constants::ORDER_BY_DEFAULT          => 'default',                         //>(translatable)<
            tubepress_vimeo3_api_Constants::ORDER_BY_LIKES            => 'most likes',                      //>(translatable)<
            tubepress_vimeo3_api_Constants::ORDER_BY_LONGEST          => 'duration (longest first)',        //>(translatable)<
            tubepress_vimeo3_api_Constants::ORDER_BY_NEWEST           => 'date published (newest first)',   //>(translatable)<
            tubepress_vimeo3_api_Constants::ORDER_BY_OLDEST           => 'date published (oldest first)',   //>(translatable)<
            tubepress_vimeo3_api_Constants::ORDER_BY_RELEVANCE        => 'relevance',                       //>(translatable)<
            tubepress_vimeo3_api_Constants::ORDER_BY_SHORTEST         => 'duration (shortest first)',       //>(translatable)<
            tubepress_vimeo3_api_Constants::ORDER_BY_VIEW_COUNT       => 'view count',                      //>(translatable)<
        );
    }

    /**
     * {@inheritdoc}
     */
    public function collectPage($pageNumber)
    {
        return $this->_httpCollector->collectPage($pageNumber, $this->_feedHandler);
    }

    /**
     * {@inheritdoc}
     */
    public function collectSingle($id)
    {
        return $this->_httpCollector->collectSingle($id, $this->_feedHandler);
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties()
    {
        return $this->_properties;
    }
}
