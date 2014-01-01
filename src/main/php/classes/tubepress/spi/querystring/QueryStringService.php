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
 * Handles some tasks related to the query string
 */
interface tubepress_spi_querystring_QueryStringService
{
    const _ = 'tubepress_spi_querystring_QueryStringService';

    /**
     * Returns what's in the address bar
     *
     * @param array $serverVars The PHP $_SERVER array
     *
     * @return string What's in the address bar
     */
    function getFullUrl($serverVars);
}
