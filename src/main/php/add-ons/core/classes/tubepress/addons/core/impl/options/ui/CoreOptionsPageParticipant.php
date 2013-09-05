<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Registers the core options for use by the options page.
 */
class tubepress_addons_core_impl_options_ui_CoreOptionsPageParticipant implements tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface
{
    const PARTICIPANT_ID = 'core';

    const CATEGORY_ID_ADVANCED      = 'advanced-category';
    const CATEGORY_ID_CACHE         = 'cache-category';
    const CATEGORY_ID_FEED          = 'feed-category';
    const CATEGORY_ID_GALLERYSOURCE = 'gallerysource-category';
    const CATEGORY_ID_META          = 'meta-category';
    const CATEGORY_ID_PLAYER        = 'player-category';
    const CATEGORY_ID_THUMBS        = 'thumbs-category';

    /**
     * @var tubepress_spi_options_ui_OptionsPageFieldInterface[]
     */
    private $_cachedFields;

    /**
     * @var tubepress_spi_provider_PluggableVideoProviderService[]
     */
    private $_videoProviders;

    /**
     * @var tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface[]
     */
    private $_optionsPageParticipants;

    /**
     * @return string The name of the item that is displayed to the user.
     */
    public function getTranslatedDisplayName()
    {
        return 'Core';  //this will never be shown, so don't translate
    }

    /**
     * @return string The page-unique identifier for this item.
     */
    public function getId()
    {
        return self::PARTICIPANT_ID;
    }

    /**
     * @return tubepress_spi_options_ui_OptionsPageItemInterface[] The categories that this participant supplies.
     */
    public function getCategories()
    {
        return array(

            new tubepress_impl_options_ui_OptionsPageItem(self::CATEGORY_ID_GALLERYSOURCE, 'Which videos?'),  //>(translatable)<)
            new tubepress_impl_options_ui_OptionsPageItem(self::CATEGORY_ID_THUMBS,        'Thumbnails'),     //>(translatable)<')
            new tubepress_impl_options_ui_OptionsPageItem(self::CATEGORY_ID_PLAYER,        'Player'),         //>(translatable)<)
            new tubepress_impl_options_ui_OptionsPageItem(self::CATEGORY_ID_META,          'Meta'),           //>(translatable)<)
            new tubepress_impl_options_ui_OptionsPageItem(self::CATEGORY_ID_FEED,          'Feed'),           //>(translatable)<)
            new tubepress_impl_options_ui_OptionsPageItem(self::CATEGORY_ID_CACHE,         'Cache'),          //>(translatable)<)
            new tubepress_impl_options_ui_OptionsPageItem(self::CATEGORY_ID_ADVANCED,      'Advanced'),       //>(translatable)<)
        );
    }

    /**
     * @return tubepress_spi_options_ui_OptionsPageFieldInterface[] The fields that this options page participant provides.
     */
    public function getFields()
    {
        if (isset($this->_cachedFields)) {

            return $this->_cachedFields;
        }

        return array(

            //Filter field
            new tubepress_addons_core_impl_options_ui_fields_FilterMultiSelectField($this->_optionsPageParticipants),

            //Thumbnail fields
            new tubepress_addons_core_impl_options_ui_fields_ThemeField(),
            new tubepress_impl_options_ui_fields_TextField(tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT),
            new tubepress_impl_options_ui_fields_TextField(tubepress_api_const_options_names_Thumbs::THUMB_WIDTH),
            new tubepress_impl_options_ui_fields_BooleanField(tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION),
            new tubepress_impl_options_ui_fields_BooleanField(tubepress_api_const_options_names_Thumbs::FLUID_THUMBS),
            new tubepress_impl_options_ui_fields_BooleanField(tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE),
            new tubepress_impl_options_ui_fields_BooleanField(tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW),
            new tubepress_impl_options_ui_fields_BooleanField(tubepress_api_const_options_names_Thumbs::HQ_THUMBS),
            new tubepress_impl_options_ui_fields_BooleanField(tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS),
            new tubepress_impl_options_ui_fields_TextField(tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE),

            //Player fields
            new tubepress_impl_options_ui_fields_DropdownField(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION),
            new tubepress_impl_options_ui_fields_DropdownField(tubepress_api_const_options_names_Embedded::PLAYER_IMPL),
            new tubepress_impl_options_ui_fields_TextField(tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT),
            new tubepress_impl_options_ui_fields_TextField(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH),
            new tubepress_impl_options_ui_fields_BooleanField(tubepress_api_const_options_names_Embedded::LAZYPLAY),
            new tubepress_impl_options_ui_fields_BooleanField(tubepress_api_const_options_names_Embedded::SHOW_INFO),
            new tubepress_impl_options_ui_fields_BooleanField(tubepress_api_const_options_names_Embedded::AUTONEXT),
            new tubepress_impl_options_ui_fields_BooleanField(tubepress_api_const_options_names_Embedded::AUTOPLAY),
            new tubepress_impl_options_ui_fields_BooleanField(tubepress_api_const_options_names_Embedded::LOOP),
            new tubepress_impl_options_ui_fields_BooleanField(tubepress_api_const_options_names_Embedded::ENABLE_JS_API),

            //Meta fields
            new tubepress_addons_core_impl_options_ui_fields_MetaMultiSelectField($this->_videoProviders),
            new tubepress_impl_options_ui_fields_TextField(org_tubepress_api_const_options_names_Meta::DATEFORMAT),
            new tubepress_impl_options_ui_fields_BooleanField(org_tubepress_api_const_options_names_Meta::RELATIVE_DATES),
            new tubepress_impl_options_ui_fields_TextField(org_tubepress_api_const_options_names_Meta::DESC_LIMIT),

            //Feed fields
            new tubepress_impl_options_ui_fields_DropdownField(tubepress_api_const_options_names_Feed::ORDER_BY),
            new tubepress_impl_options_ui_fields_DropdownField(tubepress_api_const_options_names_Feed::PER_PAGE_SORT),
            new tubepress_impl_options_ui_fields_TextField(tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP),
            new tubepress_impl_options_ui_fields_TextField(tubepress_api_const_options_names_Feed::VIDEO_BLACKLIST),
            new tubepress_impl_options_ui_fields_TextField(tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER),

            //Cache fields
            new tubepress_impl_options_ui_fields_BooleanField(tubepress_api_const_options_names_Cache::CACHE_ENABLED),
            new tubepress_impl_options_ui_fields_TextField(tubepress_api_const_options_names_Cache::CACHE_DIR),
            new tubepress_impl_options_ui_fields_TextField(tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS),
            new tubepress_impl_options_ui_fields_TextField(tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR),

            //Advanced fields
            new tubepress_impl_options_ui_fields_BooleanField(tubepress_api_const_options_names_Advanced::DEBUG_ON),
            new tubepress_impl_options_ui_fields_BooleanField(tubepress_api_const_options_names_Advanced::HTTPS),
            new tubepress_impl_options_ui_fields_DropdownField(tubepress_api_const_options_names_Advanced::HTTP_METHOD),
        );
    }

    /**
     * @return array An associative array, which may be empty, where the keys are category IDs and the values
     *               are arrays of field IDs that belong in the category.
     */
    public function getCategoryIdsToFieldIdsMap()
    {
        return array(

            self::CATEGORY_ID_THUMBS => array(

                tubepress_api_const_options_names_Thumbs::THEME,
                tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT,
                tubepress_api_const_options_names_Thumbs::THUMB_WIDTH,
                tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION,
                tubepress_api_const_options_names_Thumbs::FLUID_THUMBS,
                tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE,
                tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW,
                tubepress_api_const_options_names_Thumbs::HQ_THUMBS,
                tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS,
                tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE,
            ),

            self::CATEGORY_ID_PLAYER => array(

                tubepress_api_const_options_names_Embedded::PLAYER_LOCATION,
                tubepress_api_const_options_names_Embedded::PLAYER_IMPL,
                tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT,
                tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH,
                tubepress_api_const_options_names_Embedded::LAZYPLAY,
                tubepress_api_const_options_names_Embedded::SHOW_INFO,
                tubepress_api_const_options_names_Embedded::AUTONEXT,
                tubepress_api_const_options_names_Embedded::AUTOPLAY,
                tubepress_api_const_options_names_Embedded::LOOP,
                tubepress_api_const_options_names_Embedded::ENABLE_JS_API,
            ),

            self::CATEGORY_ID_META => array(

                tubepress_addons_core_impl_options_ui_fields_MetaMultiSelectField::FIELD_ID,
                org_tubepress_api_const_options_names_Meta::DATEFORMAT,
                org_tubepress_api_const_options_names_Meta::RELATIVE_DATES,
                org_tubepress_api_const_options_names_Meta::DESC_LIMIT,
            ),

            self::CATEGORY_ID_FEED => array(

                tubepress_api_const_options_names_Feed::ORDER_BY,
                tubepress_api_const_options_names_Feed::PER_PAGE_SORT,
                tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP,
                tubepress_api_const_options_names_Feed::VIDEO_BLACKLIST,
                tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER,
            ),

            self::CATEGORY_ID_CACHE => array(

                tubepress_api_const_options_names_Cache::CACHE_ENABLED,
                tubepress_api_const_options_names_Cache::CACHE_DIR,
                tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS,
                tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR,
            ),

            self::CATEGORY_ID_ADVANCED => array(

                tubepress_api_const_options_names_Advanced::DEBUG_ON,
                tubepress_api_const_options_names_Advanced::HTTPS,
                tubepress_api_const_options_names_Advanced::HTTP_METHOD
            )
        );
    }

    /**
     * @param tubepress_spi_provider_PluggableVideoProviderService[] $videoProviders
     */
    public function setVideoProviders(array $videoProviders)
    {
        $this->_videoProviders = $videoProviders;
    }

    /**
     * @param tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface[] $participants
     */
    public function setOptionsPageParticipants(array $participants)
    {
        $this->_optionsPageParticipants = $participants;
    }
}