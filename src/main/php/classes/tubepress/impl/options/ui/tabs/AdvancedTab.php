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
 * Displays the advanced options tab.
 */
class tubepress_impl_options_ui_tabs_AdvancedTab extends tubepress_impl_options_ui_tabs_AbstractTab
{
    const _ = 'tubepress_impl_options_ui_tabs_AdvancedTab';

    /**
     * Get the untranslated title of this tab.
     *
     * @return string The untranslated title of this tab.
     */
    protected final function getRawTitle()
    {
        return 'Advanced';  //>(translatable)<
    }

    /**
     * Get the delegate form handlers.
     *
     * @return array An array of tubepress_spi_options_ui_FormHandler.
     */
    protected final function getDelegateFormHandlers()
    {
        $fieldBuilder = $this->getFieldBuilder();

        return array(

            $fieldBuilder->build(tubepress_api_const_options_names_Advanced::DEBUG_ON,               tubepress_impl_options_ui_fields_BooleanField::__),
            $fieldBuilder->build(tubepress_api_const_options_names_Advanced::KEYWORD,                tubepress_impl_options_ui_fields_TextField::__),
            $fieldBuilder->build(tubepress_api_const_options_names_Advanced::HTTPS,                  tubepress_impl_options_ui_fields_BooleanField::__),
            $fieldBuilder->build(tubepress_api_const_options_names_Advanced::DISABLE_HTTP_CURL,      tubepress_impl_options_ui_fields_BooleanField::__),
            $fieldBuilder->build(tubepress_api_const_options_names_Advanced::DISABLE_HTTP_EXTHTTP,   tubepress_impl_options_ui_fields_BooleanField::__),
            $fieldBuilder->build(tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FOPEN,     tubepress_impl_options_ui_fields_BooleanField::__),
            $fieldBuilder->build(tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FSOCKOPEN, tubepress_impl_options_ui_fields_BooleanField::__),
            $fieldBuilder->build(tubepress_api_const_options_names_Advanced::DISABLE_HTTP_STREAMS,   tubepress_impl_options_ui_fields_BooleanField::__),
        );
    }
}
