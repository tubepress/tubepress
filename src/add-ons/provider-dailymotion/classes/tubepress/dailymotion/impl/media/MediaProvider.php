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
class tubepress_dailymotion_impl_media_MediaProvider implements tubepress_spi_media_MediaProviderInterface
{
    private static $_GALLERY_SOURCE_NAMES = array(

        tubepress_dailymotion_api_Constants::GALLERY_SOURCE_FAVORITES,
        tubepress_dailymotion_api_Constants::GALLERY_SOURCE_FEATURED,
        tubepress_dailymotion_api_Constants::GALLERY_SOURCE_LIST,
        tubepress_dailymotion_api_Constants::GALLERY_SOURCE_PLAYLIST,
        tubepress_dailymotion_api_Constants::GALLERY_SOURCE_RELATED,
        tubepress_dailymotion_api_Constants::GALLERY_SOURCE_SEARCH,
        tubepress_dailymotion_api_Constants::GALLERY_SOURCE_SUBSCRIPTIONS,
        tubepress_dailymotion_api_Constants::GALLERY_SOURCE_TAG,
        tubepress_dailymotion_api_Constants::GALLERY_SOURCE_USER,
    );

    private static $_MODE_TEMPLATE_MAP = array(

        tubepress_dailymotion_api_Constants::GALLERY_SOURCE_FAVORITES     => 'favorites of %s',
        tubepress_dailymotion_api_Constants::GALLERY_SOURCE_FEATURED      => 'featured videos of %s',
        tubepress_dailymotion_api_Constants::GALLERY_SOURCE_LIST          => 'manual list of videos: %s',
        tubepress_dailymotion_api_Constants::GALLERY_SOURCE_PLAYLIST      => 'playlist %s',
        tubepress_dailymotion_api_Constants::GALLERY_SOURCE_RELATED       => 'videos related to %s',
        tubepress_dailymotion_api_Constants::GALLERY_SOURCE_SEARCH        => 'search for %s',
        tubepress_dailymotion_api_Constants::GALLERY_SOURCE_SUBSCRIPTIONS => 'subscriptions of %s',
        tubepress_dailymotion_api_Constants::GALLERY_SOURCE_TAG           => 'videos tagged with %s',
        tubepress_dailymotion_api_Constants::GALLERY_SOURCE_USER          => 'uploads of %s',
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

    /**
     * @var tubepress_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    public function __construct(tubepress_api_media_HttpCollectorInterface     $httpCollector,
                                tubepress_spi_media_HttpFeedHandlerInterface   $feedHandler,
                                tubepress_api_environment_EnvironmentInterface $environment,
                                tubepress_api_util_StringUtilsInterface        $stringUtils)
    {
        $this->_httpCollector = $httpCollector;
        $this->_feedHandler   = $feedHandler;
        $this->_properties    = new tubepress_internal_collection_Map();
        $this->_stringUtils   = $stringUtils;

        $baseUrlClone = $environment->getBaseUrl()->getClone();
        $miniIconUrl  = $baseUrlClone->addPath('src/add-ons/provider-dailymotion/web/images/icons/dailymotion-icon-34w_x_34h.png')->toString();
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
        return 'dailymotion';
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayName()
    {
        return 'Dailymotion';
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
            tubepress_api_options_Names::META_DISPLAY_CATEGORY => tubepress_api_media_MediaItem::ATTRIBUTE_CATEGORY_DISPLAY_NAME,

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
        return is_string($mediaId) && $this->_stringUtils->startsWith($mediaId, 'dailymotion_');
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchModeName()
    {
        return tubepress_dailymotion_api_Constants::GALLERY_SOURCE_SEARCH;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryOptionName()
    {
        return tubepress_dailymotion_api_Constants::OPTION_SEARCH_VALUE;
    }

    /**
     * {@inheritdoc}
     */
    public function getMapOfFeedSortNamesToUntranslatedLabels()
    {
        return array(

            tubepress_dailymotion_api_Constants::ORDER_BY_DEFAULT    => 'default',                         //>(translatable)<
            tubepress_dailymotion_api_Constants::ORDER_BY_NEWEST     => 'date published (newest first)',   //>(translatable)<
            tubepress_dailymotion_api_Constants::ORDER_BY_OLDEST     => 'date published (oldest first)',   //>(translatable)<
            tubepress_dailymotion_api_Constants::ORDER_BY_RELEVANCE  => 'relevance',                       //>(translatable)<
            tubepress_dailymotion_api_Constants::ORDER_BY_VIEW_COUNT => 'view count',                      //>(translatable)<
            tubepress_dailymotion_api_Constants::ORDER_BY_RANDOM     => 'random',                          //>(translatable)<
            tubepress_dailymotion_api_Constants::ORDER_BY_RANKING    => 'ranking',                         //>(translatable)<
            tubepress_dailymotion_api_Constants::ORDER_BY_TRENDING   => 'trending',                        //>(translatable)<
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
        $id = str_replace('dailymotion_', '', $id);

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
