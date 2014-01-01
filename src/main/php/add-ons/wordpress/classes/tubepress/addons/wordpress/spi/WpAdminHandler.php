<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

interface tubepress_addons_wordpress_spi_WpAdminHandler
{
    const _ = 'tubepress_addons_wordpress_spi_WpAdminHandler';

    /**
     * Prints the options page.
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

