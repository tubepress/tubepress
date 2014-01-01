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

/**
 * Handles and responds to incoming Ajax commands.
 */
interface tubepress_spi_http_PluggableAjaxCommandService
{
    const _ = 'tubepress_spi_http_PluggableAjaxCommandService';

    /**
     * @return string The command name that this handler responds to.
     */
    function getName();

    /**
     * Handle the Ajax request.
     *
     * @return void
     */
    function handle();

    /**
     * @return integer The HTTP status code after handling this request.
     */
    function getHttpStatusCode();

    /**
     * @return string The HTML output after handling this request.
     */
    function getOutput();
}
