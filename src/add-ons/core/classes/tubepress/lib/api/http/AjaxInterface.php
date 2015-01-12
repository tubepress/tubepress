<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Handles and responds to incoming Ajax commands.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_lib_api_http_AjaxInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_lib_api_http_AjaxInterface';

    /**
     * Handle the Ajax request.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    function handle();
}