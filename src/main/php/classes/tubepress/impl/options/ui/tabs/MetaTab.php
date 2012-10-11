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
 * Displays the meta tab.
 */
class tubepress_impl_options_ui_tabs_MetaTab extends tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab
{
    /**
     * Get the untranslated title of this tab.
     *
     * @return string The untranslated title of this tab.
     */
    protected final function getRawTitle()
    {
        return 'Meta';  //>(translatable)<
    }

    /**
     * Get the delegate form handlers.
     *
     * @return array An array of tubepress_spi_options_ui_FormHandler.
     */
    protected final function getHardCodedFields()
    {
        $fieldBuilder = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionsUiFieldBuilder();

        return array(

            $fieldBuilder->buildMetaDisplayMultiSelectField(),

            $fieldBuilder->build(tubepress_api_const_options_names_Meta::DATEFORMAT,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME, $this->getName()),

            $fieldBuilder->build(tubepress_api_const_options_names_Meta::RELATIVE_DATES,
                tubepress_impl_options_ui_fields_BooleanField::FIELD_CLASS_NAME, $this->getName()),

            $fieldBuilder->build(tubepress_api_const_options_names_Meta::DESC_LIMIT,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME, $this->getName()),
        );
    }

    public final function getName()
    {
        return 'meta';
    }
}