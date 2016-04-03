<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_http_oauth2_impl_token_Token implements tubepress_api_http_oauth_v2_TokenInterface
{
    /**
     * @var string
     */
    private $_accessToken = null;

    /**
     * @var string
     */
    private $_refreshToken = null;

    /**
     * @var int
     */
    private $_endOfLifeUnixTime = self::EOL_UNKNOWN;

    /**
     * @var array
     */
    private $_extraParams = array();

    /**
     * {@inheritdoc}
     */
    public function getAccessToken()
    {
        return $this->_accessToken;
    }

    /**
     * {@inheritdoc}
     */
    public function getEndOfLifeUnixTime()
    {
        return $this->_endOfLifeUnixTime;
    }

    /**
     * {@inheritdoc}
     */
    public function getRefreshToken()
    {
        return $this->_refreshToken;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtraParams()
    {
        return $this->_extraParams;
    }

    public function setAccessToken($token)
    {
        if (!is_string($token)) {

            throw new InvalidArgumentException('Access token must be a string');
        }

        $this->_accessToken = $token;
    }

    public function setEndOfLifeUnixTime($eol)
    {
        if (!is_int($eol)) {

            throw new InvalidArgumentException('End-of-life must be an integer');
        }

        $this->_endOfLifeUnixTime = $eol;
    }

    /**
     * @param int $lifetime
     */
    public function setLifetimeInSeconds($lifetime)
    {
        if (!is_int($lifetime) || $lifetime < 0) {

            throw new InvalidArgumentException('Token lifetime in seconds must be a non-negative integer');
        }

        $this->_endOfLifeUnixTime = $lifetime + time();
    }

    public function setRefreshToken($token)
    {
        if (!is_string($token)) {

            throw new InvalidArgumentException('Refresh token must be a string');
        }

        $this->_refreshToken = $token;
    }

    public function setExtraParams(array $params)
    {
        $this->_extraParams = $params;
    }

    public function markAsNeverExpires()
    {
        $this->_endOfLifeUnixTime = self::EOL_NEVER_EXPIRES;
    }

    /**
     * {@inheritdoc}
     */
    public function isExpired()
    {
        $eolUnixTime = $this->getEndOfLifeUnixTime();

        if ($eolUnixTime === self::EOL_NEVER_EXPIRES || $eolUnixTime === self::EOL_UNKNOWN) {

            return false;
        }

        return time() > $eolUnixTime;
    }
}
