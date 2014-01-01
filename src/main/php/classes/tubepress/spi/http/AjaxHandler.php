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
 * Handles and responds to incoming Ajax requests.
 */
interface tubepress_spi_http_AjaxHandler
{
    const _ = 'tubepress_spi_http_AjaxHandler';

    /**
     * Handles incoming requests.
     *
     * @return void Handle the request and output a response.
     */
    function handle();
}
