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
     * @var array Cached list of the available pluggable field builders.
     */
    private $_pluggableFieldBuilders;

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
        $this->_initialize();

        $field = $this->_buildFromPluggables($name, $type);

        if ($field) {

            return $field;
        }

        if (! class_exists($type)) {

            return null;
        }

        $ref = new ReflectionClass($type);

        return $ref->newInstance($name);
    }

    private function _buildFromPluggables($name, $type)
    {
        foreach ($this->_pluggableFieldBuilders as $pluggableFieldBuilder)
        {
            $field = $pluggableFieldBuilder->build($name, $type);

            if ($field) {

                return $field;
            }
        }

        return null;
    }

    private function _initialize()
    {
        if (isset($this->_pluggableFieldBuilders)) {

            return;
        }

        $this->_pluggableFieldBuilders = tubepress_impl_patterns_sl_ServiceLocator::getFieldBuilders();
    }
}