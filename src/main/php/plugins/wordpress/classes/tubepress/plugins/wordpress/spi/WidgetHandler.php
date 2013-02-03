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

interface tubepress_plugins_wordpress_spi_WidgetHandler
{
    const _ = 'tubepress_plugins_wordpress_spi_WidgetHandler';

    /**
     * Filter the content (which may be empty).
     */
    function printControlHtml();

    /**
     * Registers all the styles and scripts for the front end.
     *
     * @param array $opts The options.
     *
     * @return void
     */
    function printWidgetHtml($opts);

    /**
     * Registers ourselves as an admin menu.
     *
     * @return void
     */
    function registerWidget();
}

