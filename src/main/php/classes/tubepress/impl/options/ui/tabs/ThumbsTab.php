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
 * Displays the thumbnails tab.
 */
class tubepress_impl_options_ui_tabs_ThumbsTab extends tubepress_impl_options_ui_tabs_AbstractTab
{
    /**
     * Get the untranslated title of this tab.
     *
     * @return string The untranslated title of this tab.
     */
    protected final function getRawTitle()
    {
        return 'Thumbnails';  //>(translatable)<
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

            $fieldBuilder->build(tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT,     tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
            $fieldBuilder->build(tubepress_api_const_options_names_Thumbs::THUMB_WIDTH,      tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
            $fieldBuilder->build(tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION,  tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME),
            $fieldBuilder->build(tubepress_api_const_options_names_Thumbs::FLUID_THUMBS,     tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME),
            $fieldBuilder->build(tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE,   tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME),
            $fieldBuilder->build(tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW,   tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME),
            $fieldBuilder->build(tubepress_api_const_options_names_Thumbs::HQ_THUMBS,        tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME),
            $fieldBuilder->build(tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS,    tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME),
            $fieldBuilder->build(tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE, tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
        );
    }
}