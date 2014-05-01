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
 * Provides access to the current URL (in the user's browser).
 *
 * @package TubePress\URL
 */
interface tubepress_api_url_CurrentUrlServiceInterface
{
    const _ = 'tubepress_api_url_CurrentUrlServiceInterface';

    /**
     * The current URL shown to the user.
     *
     * @return tubepress_api_url_UrlInterface
     *
     * @throws RuntimeException If unable to determine current URL.
     *
     * @since 4.0.0
     */
    function getUrl();
}