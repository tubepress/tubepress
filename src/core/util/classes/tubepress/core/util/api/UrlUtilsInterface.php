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
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_core_util_api_UrlUtilsInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_core_util_api_UrlUtilsInterface';

    /**
     * @param tubepress_core_url_api_UrlInterface $url
     *
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    function getAsStringWithoutSchemeAndAuthority(tubepress_core_url_api_UrlInterface $url);
}