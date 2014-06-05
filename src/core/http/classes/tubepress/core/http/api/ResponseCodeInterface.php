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
 * Wraps HTTP response code logic.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_core_http_api_ResponseCodeInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_core_http_api_ResponseCodeInterface';

    /**
     * Set a new HTTP response code.
     *
     * @param int $code The new HTTP response code.
     *
     * @return int The current HTTP response code.
     *
     * @api
     * @since 4.0.0
     */
    function setResponseCode($code);

    /**
     * @return int The current HTTP response code.
     *
     * @api
     * @since 4.0.0
     */
    function getCurrentResponseCode();
}