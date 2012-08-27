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
 * Builds fields!
 */
interface org_tubepress_spi_options_ui_FieldBuilder
{
    const _ = 'org_tubepress_spi_options_ui_FieldBuilder';

    /**
     * Build a single field with the given name and type.
     *
     * @param string $name The name of the field to build.
     * @param string $type The name of the class to construct to represent this field.
     *
     * @return org_tubepress_spi_options_ui_Field The constructed field.
     */
    function build($name, $type);


    /**
     * Builds the multi-select dropdown for meta display.
     *
     * @return org_tubepress_impl_options_ui_fields_MetaMultiSelectField The constructed field.
     */
    function buildMetaDisplayMultiSelectField();
}
