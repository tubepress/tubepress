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
