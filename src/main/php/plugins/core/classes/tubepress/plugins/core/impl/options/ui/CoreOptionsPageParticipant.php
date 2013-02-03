<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Registers the core options for use by the options page.
 */
class tubepress_plugins_core_impl_options_ui_CoreOptionsPageParticipant implements tubepress_spi_options_ui_PluggableOptionsPageParticipant
{
    /**
     * @return string The name that will be displayed in the options page filter (top right).
     */
    public final function getFriendlyName()
    {
        return 'Core';    //>(translatable)<
    }

    /**
     * @return string All lowercase alphanumerics.
     */
    public final function getName()
    {
        return 'core';
    }

    /**
     * @param string $tabName The name of the tab being built.
     *
     * @return array An array of fields that should be shown on the given tab. May be empty, never null.
     */
    public final function getFieldsForTab($tabName)
    {
        switch ($tabName) {

            case tubepress_impl_options_ui_tabs_AdvancedTab::TAB_NAME:

                return $this->getFieldsForAdvancedTab();

            case tubepress_impl_options_ui_tabs_CacheTab::TAB_NAME:

                return $this->getFieldsForCacheTab();

            case tubepress_impl_options_ui_tabs_EmbeddedTab::TAB_NAME:

                return $this->getFieldsForEmbeddedTab();

            case tubepress_impl_options_ui_tabs_FeedTab::TAB_NAME:

                return $this->getFieldsForFeedTab();

            case tubepress_impl_options_ui_tabs_GallerySourceTab::TAB_NAME:

                return $this->getFieldsForGallerySourceTab();

            case tubepress_impl_options_ui_tabs_MetaTab::TAB_NAME:

                return $this->getFieldsForMetaTab();

            case tubepress_impl_options_ui_tabs_ThemeTab::TAB_NAME:

                return $this->getFieldsForThemeTab();

            case tubepress_impl_options_ui_tabs_ThumbsTab::TAB_NAME:

                return $this->getFieldsForThumbsTab();

            default:

                return array();
        }
    }

    private function getFieldsForAdvancedTab()
    {
        $fieldBuilder = tubepress_impl_patterns_sl_ServiceLocator::getOptionsUiFieldBuilder();

        return array(

            $fieldBuilder->build(tubepress_api_const_options_names_Advanced::DEBUG_ON,    tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME),
            $fieldBuilder->build(tubepress_api_const_options_names_Advanced::KEYWORD,     tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
            $fieldBuilder->build(tubepress_api_const_options_names_Advanced::HTTPS,       tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME),
            $fieldBuilder->build(tubepress_api_const_options_names_Advanced::HTTP_METHOD, tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),
        );
    }

    private function getFieldsForCacheTab()
    {
        $fieldBuilder = tubepress_impl_patterns_sl_ServiceLocator::getOptionsUiFieldBuilder();

        return array(

            $fieldBuilder->build(tubepress_api_const_options_names_Cache::CACHE_ENABLED,
                tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Cache::CACHE_DIR,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
        );
    }

    private function getFieldsForEmbeddedTab()
    {
        $fieldBuilder = tubepress_impl_patterns_sl_ServiceLocator::getOptionsUiFieldBuilder();

        return array(

            $fieldBuilder->build(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Embedded::PLAYER_IMPL,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Embedded::LAZYPLAY,
                tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Embedded::SHOW_INFO,
                tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Embedded::AUTONEXT,
                tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Embedded::AUTOPLAY,
                tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Embedded::LOOP,
                tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Embedded::ENABLE_JS_API,
                tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME),
        );
    }

    private function getFieldsForFeedTab()
    {
        $fieldBuilder = tubepress_impl_patterns_sl_ServiceLocator::getOptionsUiFieldBuilder();

        return array(

            $fieldBuilder->build(tubepress_api_const_options_names_Feed::ORDER_BY,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Feed::PER_PAGE_SORT,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Feed::VIDEO_BLACKLIST,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
        );
    }

    private function getFieldsForMetaTab()
    {
        $fieldBuilder = tubepress_impl_patterns_sl_ServiceLocator::getOptionsUiFieldBuilder();

        return array(

            $fieldBuilder->buildMetaDisplayMultiSelectField(),

            $fieldBuilder->build(tubepress_api_const_options_names_Meta::DATEFORMAT,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Meta::RELATIVE_DATES,
                tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Meta::DESC_LIMIT,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
        );
    }

    private function getFieldsForThemeTab()
    {
        $fieldBuilder = tubepress_impl_patterns_sl_ServiceLocator::getOptionsUiFieldBuilder();

        return array(

            $fieldBuilder->build(tubepress_api_const_options_names_Thumbs::THEME,
                tubepress_impl_options_ui_fields_ThemeField::FIELD_CLASS_NAME)
        );
    }

    private function getFieldsForThumbsTab()
    {
        $fieldBuilder = tubepress_impl_patterns_sl_ServiceLocator::getOptionsUiFieldBuilder();

        return array(

            $fieldBuilder->build(tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Thumbs::THUMB_WIDTH,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION,
                tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Thumbs::FLUID_THUMBS,
                tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE,
                tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW,
                tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Thumbs::HQ_THUMBS,
                tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS,
                tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME),

            $fieldBuilder->build(tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
        );
    }

    private function getFieldsForGallerySourceTab()
    {
        $fieldBuilder = tubepress_impl_patterns_sl_ServiceLocator::getOptionsUiFieldBuilder();

        return array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE =>
            $fieldBuilder->build(tubepress_api_const_options_names_Output::GALLERY_SOURCE,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
        );
    }
}