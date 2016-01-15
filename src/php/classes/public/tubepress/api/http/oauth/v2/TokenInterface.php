<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @api
 * @since 4.2.0
 */
interface tubepress_api_http_oauth_v2_TokenInterface
{
    /**
     * @api
     * @since 4.2.0
     *
     * Denotes an unknown end of life time.
     */
    const EOL_UNKNOWN = -9001;

    /**
     * @api
     * @since 4.2.0
     *
     * Denotes a token which never expires, should only happen in OAuth1.
     */
    const EOL_NEVER_EXPIRES = -9002;

    /**
     * @api
     * @since 4.2.0
     *
     * @return string|null The access token, or null if not set.
     */
    function getAccessToken();

    /**
     * @api
     * @since 4.2.0
     *
     * @return int
     */
    function getEndOfLifeUnixTime();

    /**
     * @api
     * @since 4.2.0
     *
     * @return string|null The refresh token value, or null if not available.
     */
    function getRefreshToken();

    /**
     * @api
     * @since 4.2.0
     *
     * @return array An array, which may be empty but not null, of extra parameters for this token.
     */
    function getExtraParams();

    /**
     * @api
     * @since 4.2.0
     *
     * @return bool True if this token has expired, false otherwise.
     */
    function isExpired();
}
