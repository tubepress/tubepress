<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Builds fields!
 */
interface tubepress_spi_options_ui_FieldBuilder
{
    const _ = 'tubepress_spi_options_ui_FieldBuilder';

    /**
     * Build a single field with the given name and type.
     *
     * @param string $name            The name of the field to build.
     * @param string $type            The name of the class to construct to represent this field.
     *
     * @return tubepress_spi_options_ui_FieldInterface The constructed field, or null if unable to build.
     */
    function build($name, $type);
}