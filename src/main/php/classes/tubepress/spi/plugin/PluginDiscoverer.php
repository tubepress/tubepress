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
 * Discovers plugins in directories.
 */
interface tubepress_spi_plugin_PluginDiscoverer
{
    const _ = 'tubepress_spi_plugin_PluginDiscoverer';

    /**
     * Recursively searches a directory (up to 2 levels) for valid TubePress plugins.
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
