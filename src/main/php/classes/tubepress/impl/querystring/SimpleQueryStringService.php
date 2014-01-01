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
class tubepress_impl_querystring_SimpleQueryStringService implements tubepress_spi_querystring_QueryStringService
{
    /**
     * Returns what's in the address bar
     *
     * @param array $serverVars The PHP $_SERVER array
     *
     * @return string What's in the address bar
     */
    public function getFullUrl($serverVars)
    {
        $pageURL = 'http';
        if (isset($serverVars['HTTPS']) && $serverVars['HTTPS'] == 'on') {
            $pageURL .= 's';
        }
        $pageURL .= '://';
        if ($serverVars['SERVER_PORT'] != '80') {
             $pageURL .= $serverVars['SERVER_NAME'].':'.
                 $serverVars['SERVER_PORT'].$serverVars['REQUEST_URI'];
        } else {
             $pageURL .= $serverVars['SERVER_NAME'].$serverVars['REQUEST_URI'];
        }
        return $pageURL;
    }
}
