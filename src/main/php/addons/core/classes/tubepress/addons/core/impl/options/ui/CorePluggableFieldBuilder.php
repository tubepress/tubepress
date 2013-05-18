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
 * Enables building of the meta display dropdown.
 */
class tubepress_addons_core_impl_options_ui_CorePluggableFieldBuilder implements tubepress_spi_options_ui_PluggableFieldBuilder
{
    /**
     * @var tubepress_spi_provider_PluggableVideoProviderService[]
     */
    private $_videoProviders;

    /**
     * Build a single field with the given name and type.
     *
     * @param string $name            The name of the field to build.
     * @param string $type            The name of the class to construct to represent this field.
     *
     * @return tubepress_spi_options_ui_Field The constructed field, or null if unable to build a field
     *                                        with this name or type.
     */
    public final function build($name, $type)
    {
        if ($type !== 'tubepress_impl_options_ui_fields_MetaMultiSelectField') {

            return null;
        }

        return new tubepress_impl_options_ui_fields_MetaMultiSelectField($this->_videoProviders);
    }

    public function setPluggableVideoProviders(array $providers)
    {
        $this->_videoProviders = $providers;
    }
}