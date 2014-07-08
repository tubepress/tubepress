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
 * URL utilities.
 */
class tubepress_lib_util_impl_UrlUtils implements tubepress_lib_util_api_UrlUtilsInterface
{
    public function getAsStringWithoutSchemeAndAuthority(tubepress_lib_url_api_UrlInterface $url)
    {
        $scheme    = $url->getScheme();
        $authority = $url->getAuthority();
        $toRemove  = "$scheme://$authority";
        $asString  = $url->toString();
        $toReturn  = str_replace($toRemove, '', $asString);

        return '/' . ltrim($toReturn, '/');
    }
}