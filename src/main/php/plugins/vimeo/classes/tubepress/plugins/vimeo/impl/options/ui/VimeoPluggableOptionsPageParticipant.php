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
 * Plugs Vimeo into the options page.
 */
class tubepress_plugins_vimeo_impl_options_ui_VimeoPluggableOptionsPageParticipant implements tubepress_spi_options_ui_PluggableOptionsPageParticipant
{
    /**
     * @param string $tabName The name of the tab being built.
     *
     * @return array An array of fields that should be shown on the given tab. May be empty, never null.
     */
    public final function getFieldsForTab($tabName)
    {
        $fieldBuilder = tubepress_impl_patterns_sl_ServiceLocator::getOptionsUiFieldBuilder();

        switch ($tabName) {

            case tubepress_impl_options_ui_tabs_EmbeddedTab::TAB_NAME:

                return $this->getFieldsForEmbeddedTab($fieldBuilder);

            case tubepress_impl_options_ui_tabs_FeedTab::TAB_NAME:

                return $this->getFieldsForFeedTab($fieldBuilder);

            case tubepress_impl_options_ui_tabs_GallerySourceTab::TAB_NAME:

                return $this->getFieldsForGallerySourceTab($fieldBuilder);

            default:

                return array();
        }
    }

    /**
     * @return string The name that will be displayed in the options page filter (top right).
     */
    public final function getFriendlyName()
    {
        return 'Vimeo';    //>(translatable)<
    }

    /**
     * @return string All lowercase alphanumerics.
     */
    public final function getName()
    {
        return 'vimeo';
    }

    private function getFieldsForEmbeddedTab(tubepress_spi_options_ui_FieldBuilder $fieldBuilder)
    {
        return array(

            $fieldBuilder->build(

                tubepress_plugins_vimeo_api_const_options_names_Embedded::PLAYER_COLOR,
                tubepress_impl_options_ui_fields_ColorField::FIELD_CLASS_NAME
            )
        );
    }

    private function getFieldsForFeedTab(tubepress_spi_options_ui_FieldBuilder $fieldBuilder)
    {
        return array(

            $fieldBuilder->build(

                tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_KEY,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME
            ),

            $fieldBuilder->build(

                tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_SECRET,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME
            ),
        );
    }

    private function getFieldsForGallerySourceTab(tubepress_spi_options_ui_FieldBuilder $fieldBuilder)
    {
        $gallerySources = array(

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_ALBUM =>
                tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE,

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL =>
                tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_CHANNEL_VALUE,

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_SEARCH =>
                tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE,

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_UPLOADEDBY =>
                tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_UPLOADEDBY_VALUE,

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_APPEARS_IN =>
                tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_APPEARS_IN_VALUE,

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CREDITED =>
                tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_CREDITED_VALUE,

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_LIKES =>
                tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_LIKES_VALUE,

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_GROUP =>
                tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_GROUP_VALUE
        );

        $toReturn = array();

        foreach ($gallerySources as $name => $value) {

            $field = $fieldBuilder->build($value, tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME);

            $toReturn[] = new tubepress_impl_options_ui_fields_GallerySourceField($name, $field);
        }

        return $toReturn;
    }
}
