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

interface tubepress_plugins_wordpress_spi_WpAdminHandler
{
    const _ = 'tubepress_plugins_wordpress_spi_WpAdminHandler';

    /**
     * Filter the content (which may be empty).
     */
    function printOptionsPageHtml();

    /**
     * Registers all the styles and scripts for the front end.
     *
     * @param string $hook The WP hook.
     *
     * @return void
     */
    function registerStylesAndScripts($hook);

    /**
     * Registers ourselves as an admin menu.
     *
     * @return void
     */
    function registerAdminMenuItem();

    /**
     * @param array $links An array of meta links for this plugin.
     *
     * @return void
     */
    function modifyMetaRowLinks($links, $file);
}

