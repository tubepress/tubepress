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

class tubepress_http_impl_NonceManager implements tubepress_api_http_NonceManagerInterface
{
    /**
     * @var string
     */
    private static $_KEY = 'tubepress_nonce_master';

    /**
     * @return string The nonce value for the current session.
     */
    public function getNonce()
    {
        $this->_initNonce();

        return $_SESSION[self::$_KEY];
    }

    /**
     * @param mixed $value The incoming nonce value.
     *
     * @return bool True if the nonce is valid, false otherwise.
     */
    public function isNonceValid($value)
    {
        $this->_initNonce();

        return $_SESSION[self::$_KEY] === $value;
    }

    private function _initNonce()
    {
        if (!@session_start()) {

            throw new RuntimeException('Unable to start a session for the nonce manager.');
        }

        if (!isset($_SESSION[self::$_KEY])) {

            $rando                 = md5(mt_rand());
            $_SESSION[self::$_KEY] = $rando;
        }
    }
}