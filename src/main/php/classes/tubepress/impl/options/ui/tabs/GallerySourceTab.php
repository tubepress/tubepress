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
 * Displays the video source tab.
 */
class tubepress_impl_options_ui_tabs_GallerySourceTab extends tubepress_impl_options_ui_tabs_AbstractTab
{
    const TEMPLATE_VAR_CURRENT_MODE = 'org_tubepress_impl_options_ui_tabs_GallerySourceTab__mode';

    /**
     * Get the untranslated title of this tab.
     *
     * @return string The untranslated title of this tab.
     */
    protected final function getRawTitle()
    {
        return 'Which videos?';  //>(translatable)<
    }

    /**
     * Get the delegate form handlers.
     *
     * @return array An array of tubepress_spi_options_ui_FormHandler.
     */
    protected final function getDelegateFormHandlers()
    {
        $fieldBuilder = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionsUiFieldBuilder();

        return array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE =>
                $fieldBuilder->build(tubepress_api_const_options_names_Output::GALLERY_SOURCE, tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES =>
                $fieldBuilder->build(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_FAVORITES_VALUE, tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED =>
                $fieldBuilder->build(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_FEATURED_VALUE, tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER =>
                $fieldBuilder->build(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE, tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST =>
                $fieldBuilder->build(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE, tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH =>
                $fieldBuilder->build(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE, tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_POPULAR =>
                $fieldBuilder->build(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_POPULAR_VALUE, tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED =>
                $fieldBuilder->build(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TOP_RATED_VALUE, tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES =>
                $fieldBuilder->build(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TOP_FAVORITES_VALUE, tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED =>
                $fieldBuilder->build(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_DISCUSSED_VALUE, tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT =>
                $fieldBuilder->build(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_RECENT_VALUE, tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED =>
                $fieldBuilder->build(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_RESPONDED_VALUE, tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

        );
    }

    /**
     * Override point.
     *
     * Allows subclasses to perform additional modifications to the template.
     *
     * @param ehough_contemplate_api_Template $template The template for this tab.
     */
    protected final function addToTemplate(ehough_contemplate_api_Template $template)
    {
        $executionContext = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();

        $currentMode = $executionContext->get(tubepress_api_const_options_names_Output::GALLERY_SOURCE);

        $template->setVariable(self::TEMPLATE_VAR_CURRENT_MODE, $currentMode);
    }

    /**
     * Override point.
     *
     * Allows subclasses to change the template path.
     *
     * @param $originaltemplatePath string The original template path.
     *
     * @return string The (possibly) modified template path.
     */
    protected final function getModifiedTemplatePath($originaltemplatePath)
    {
        return 'src/main/resources/system-templates/options_page/gallery_source_tab.tpl.php';
    }
}