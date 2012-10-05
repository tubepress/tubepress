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
 * Displays the feed tab.
 */
class tubepress_impl_options_ui_tabs_FeedTab extends tubepress_impl_options_ui_tabs_AbstractTab
{
    /**
     * Get the untranslated title of this tab.
     *
     * @return string The untranslated title of this tab.
     */
    protected final function getRawTitle()
    {
        return 'Feed';  //>(translatable)<
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

            $fieldBuilder->build(tubepress_api_const_options_names_Feed::ORDER_BY,         tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),
            $fieldBuilder->build(tubepress_api_const_options_names_Feed::PER_PAGE_SORT,    tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),
            $fieldBuilder->build(tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP, tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
            $fieldBuilder->build(tubepress_api_const_options_names_Feed::VIDEO_BLACKLIST,  tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
            $fieldBuilder->build(tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER, tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
        );
    }
}