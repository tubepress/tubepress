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
 * Simple implementation of a plugin.
 */
class tubepress_impl_plugin_DefaultPluginRegistry implements tubepress_spi_plugin_PluginRegistry
{
    /**
     * Loads the given plugin into the system.
     *
     * @param tubepress_spi_plugin_Plugin $plugin
     *
     * @return mixed Null if the plugin loaded normally, otherwise a string error message.
     */
    public final function load(tubepress_spi_plugin_Plugin $plugin)
    {
        $pluginFile = $plugin->getAbsolutePathOfDirectory() . DIRECTORY_SEPARATOR
            . $plugin->getFileNameWithoutExtension() . '.php';

        if (! is_file($pluginFile) || ! is_readable($pluginFile)) {

            return "$pluginFile does not exist";
        }

        try {
            //load the plugin
            /** @noinspection PhpIncludeInspection */
            include $pluginFile;

        } catch (Exception $e) {

            $this->_isLoading = false;

            return 'Hit exception when trying to load ' . $plugin->getName() . ': ' . $e->getMessage();
        }

        return null;
    }
}
