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
 *
 * @api
 * @since 4.2.0
 */
interface tubepress_api_http_NonceManagerInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_api_http_NonceManagerInterface';

    /**
     * @return string The nonce value for the current session.
     */
    function getNonce();

    /**
     * @param mixed $value The incoming nonce value.
     *
     * @return bool True if the nonce is valid, false otherwise.
     */
    function isNonceValid($value);
}