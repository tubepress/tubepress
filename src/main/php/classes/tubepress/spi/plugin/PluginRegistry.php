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
 * Registry of all plugins.
 */
interface tubepress_spi_plugin_PluginRegistry
{
    const _ = 'tubepress_spi_plugin_PluginRegistry';

    /**
     * Loads the given plugin into the system.
     *
     * @param tubepress_spi_plugin_Plugin $plugin
     *
     * @return mixed Null if the plugin loaded normally, otherwise a string error message.
     */
    function load(tubepress_spi_plugin_Plugin $plugin);
}
