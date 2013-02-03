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
 * Builds fields with reflection.
 */
class tubepress_impl_options_ui_DefaultFieldBuilder implements tubepress_spi_options_ui_FieldBuilder
{
    /**
     * Build a single field with the given name and type.
     *
     * @param string $name            The name of the field to build.
     * @param string $type            The name of the class to construct to represent this field.
     *
     * @return tubepress_spi_options_ui_Field The constructed field.
     */
    public final function build($name, $type)
    {
        $ref = new ReflectionClass($type);

        return $ref->newInstance($name);
    }

    /**
     * Builds the multi-select dropdown for meta display.
     *
     * @return tubepress_impl_options_ui_fields_MetaMultiSelectField The constructed field.
     */
    public final function buildMetaDisplayMultiSelectField()
    {
        return new tubepress_impl_options_ui_fields_MetaMultiSelectField();
    }
}