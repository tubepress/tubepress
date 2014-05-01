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
 *
 */
class tubepress_addons_coreapiservices_impl_url_CurrentUrlService implements tubepress_api_url_CurrentUrlServiceInterface
{
    private static $_KEY_HTTPS = 'HTTPS';
    private static $_KEY_NAME  = 'SERVER_NAME';
    private static $_KEY_PORT  = 'SERVER_PORT';
    private static $_KEY_URI   = 'REQUEST_URI';

    /**
     * @var tubepress_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_api_url_UrlInterface
     */
    private $_cachedUrl;

    /**
     * @var array
     */
    private $_serverVars;

    public function __construct(array $serverVars, tubepress_api_url_UrlFactoryInterface $urlFactory)
    {
        $this->_urlFactory = $urlFactory;
        $this->_serverVars = $serverVars;
    }

    /**
     * The current URL shown to the user.
     *
     * @return tubepress_api_url_UrlInterface
     */
    public function getUrl()
    {
        if (!isset($this->_cachedUrl)) {

            $this->_cacheUrl();
        }

        return $this->_cachedUrl;
    }

    private function _cacheUrl()
    {
        $toReturn           = 'http';
        $requiredServerVars = array(
            self::$_KEY_PORT,
            self::$_KEY_NAME,
            self::$_KEY_URI
        );

        foreach ($requiredServerVars as $requiredServerVar) {

            if (!isset($this->_serverVars[$requiredServerVar])) {

                throw new RuntimeException(sprintf('Missing $_SERVER variable: %s', $requiredServerVar));
            }
        }

        if (isset($this->_serverVars[self::$_KEY_HTTPS]) && $this->_serverVars[self::$_KEY_HTTPS] == 'on') {

            $toReturn .= 's';
        }

        $toReturn .= '://';

        if ($this->_serverVars[self::$_KEY_PORT] != '80') {

            $toReturn .= sprintf('%s:%s%s',
                $this->_serverVars[self::$_KEY_NAME],
                $this->_serverVars[self::$_KEY_PORT],
                $this->_serverVars[self::$_KEY_URI]
            );

        } else {

            $toReturn .= $this->_serverVars[self::$_KEY_NAME].$this->_serverVars[self::$_KEY_URI];
        }

        try {

            $this->_cachedUrl = $this->_urlFactory->fromString($toReturn);

        } catch (InvalidArgumentException $e) {

            throw new RuntimeException($e->getMessage());
        }
    }
}