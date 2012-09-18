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
 * Discovers plugins in directories.
 */
interface tubepress_spi_plugin_PluginDiscoverer
{
    const _ = 'tubepress_spi_plugin_PluginDiscoverer';

    /**
     * Recursively searches a directory for valid TubePress plugins.
     *
     * @param string $directory The path of the directory in which to search.
     *
     * @return array An array of TubePress plugins, which may be empty. Never null.
     */
    function findPluginsRecursivelyInDirectory($directory);

    /**
     * Shallowly searches a directory for valid TubePress plugins.
     *
     * @param string $directory The path of the directory in which to search.
     *
     * @return array An array of TubePress plugins, which may be empty. Never null.
     */
    function findPluginsNonRecursivelyInDirectory($directory);
}
