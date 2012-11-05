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
        switch ($tabName) {

            case tubepress_impl_options_ui_tabs_EmbeddedTab::TAB_NAME:

                return $this->getFieldsForEmbeddedTab();

            case tubepress_impl_options_ui_tabs_FeedTab::TAB_NAME:

                return $this->getFieldsForFeedTab();

            case tubepress_impl_options_ui_tabs_GallerySourceTab::TAB_NAME:

                return $this->getFieldsForGallerySourceTab();

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

    private function getFieldsForEmbeddedTab()
    {
        $fieldBuilder = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionsUiFieldBuilder();

        return array(

            $fieldBuilder->build(

                tubepress_plugins_vimeo_api_const_options_names_Embedded::PLAYER_COLOR,
                tubepress_impl_options_ui_fields_ColorField::FIELD_CLASS_NAME
            )
        );
    }

    private function getFieldsForFeedTab()
    {
        $fieldBuilder = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionsUiFieldBuilder();

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

    private function getFieldsForGallerySourceTab()
    {
        $fieldBuilder = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionsUiFieldBuilder();

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
