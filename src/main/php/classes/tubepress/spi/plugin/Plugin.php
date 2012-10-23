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
 * A TubePress plugin.
 */
interface tubepress_spi_plugin_Plugin
{
    const _ = 'tubepress_spi_plugin_Plugin';

    /**
     * @return string The friendly name of this plugin.
     */
    function getName();

    /**
     * @return string The short (255 chars or less) description of this plugin.
     */
    function getDescription();

    /**
     * @return tubepress_spi_version_Version The version of this plugin.
     */
    function getVersion();

    /**
     * @return string The absolute path to the plugin's directory.
     */
    function getAbsolutePathOfDirectory();

    /**
     * @return string The filename without the .info extension.
     */
    function getFileNameWithoutExtension();

    /**
     * @return array An array of IOC container extensions. May be empty, never null.
     */
    function getIocContainerExtensions();

    /**
     * @return array An array of PSR-0 compliant class path roots.
     */
    function getPsr0ClassPathRoots();
}
