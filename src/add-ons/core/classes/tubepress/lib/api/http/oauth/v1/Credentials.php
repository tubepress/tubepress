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
 * OAuth 1.0 credentials, which may be a token or an instance of client credentials.
 *
 * @api
 * @since 4.0.0
 */
class tubepress_lib_api_http_oauth_v1_Credentials
{
    /**
     * @var string
     */
    private $_identifier;

    /**
     * @var string
     */
    private $_secret;

    /**
     * @param string $identifier
     * @param string $secret
     *
     * @throws InvalidArgumentException If a non-string ID or secret is passed to the
     *                                  constructor.
     */
    public function __construct($identifier, $secret)
    {
        if (!is_string($identifier) || !is_string($secret)) {

            throw new InvalidArgumentException('Credentials identifier and secret must both be strings.');
        }

        $this->_identifier = $identifier;
        $this->_secret     = $secret;
    }

    /**
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }

    /**
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    public function getSecret()
    {
        return $this->_secret;
    }
}