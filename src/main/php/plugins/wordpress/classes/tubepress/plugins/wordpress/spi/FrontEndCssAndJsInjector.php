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

interface tubepress_plugins_wordpress_spi_FrontEndCssAndJsInjector
{
    const _ = 'tubepress_plugins_wordpress_spi_FrontEndCssAndJsInjector';

    /**
     * Prints out HTML and CSS into the HTML <head>.
     *
     * @return void
     */
    function printInHtmlHead();

    /**
     * Registers all the styles and scripts for the front end.
     *
     * @return void
     */
    function registerStylesAndScripts();
}

