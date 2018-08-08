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
class tubepress_url_impl_puzzle_UrlFactory implements tubepress_api_url_UrlFactoryInterface
{
    private static $_KEY_HTTPS = 'HTTPS';
    private static $_KEY_NAME  = 'SERVER_NAME';
    private static $_KEY_PORT  = 'SERVER_PORT';
    private static $_KEY_URI   = 'REQUEST_URI';

    /**
     * @var tubepress_api_url_UrlInterface
     */
    private $_cachedCurrentUrl;

    /**
     * @var array
     */
    private $_serverVars;

    public function __construct(array $serverVars = array())
    {
        if (count($serverVars) === 0) {

            $serverVars = $_SERVER;
        }

        $this->_serverVars = $serverVars;
    }

    /**
     * {@inheritdoc}
     */
    public function fromString($url)
    {
        if (!is_string($url)) {

            throw new InvalidArgumentException('tubepress_url_impl_puzzle_UrlFactory::fromString() can only accept strings.');
        }

        return new tubepress_url_impl_puzzle_PuzzleBasedUrl(puzzle_Url::fromString($url));
    }

    /**
     * {@inheritdoc}
     */
    public function fromCurrent()
    {
        if (!isset($this->_cachedCurrentUrl)) {

            $this->_cacheUrl();
        }

        return $this->_cachedCurrentUrl->getClone();
    }

    private function _cacheUrl()
    {
        $toReturn           = 'http';
        $requiredServerVars = array(
            self::$_KEY_PORT,
            self::$_KEY_URI,
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
        $host      = '';

        if (isset($_SERVER['HTTP_HOST'])) {

            $hostHeaderParts = explode(':', $_SERVER['HTTP_HOST']);
            $host            = $hostHeaderParts[0];

        } elseif (isset($_SERVER[self::$_KEY_NAME])) {

            $host = $_SERVER[self::$_KEY_NAME];

        } elseif (isset($_SERVER['SERVER_ADDR'])) {

            $host = $_SERVER['SERVER_ADDR'];
        }

        if ($this->_serverVars[self::$_KEY_PORT] != '80') {

            $toReturn .= sprintf('%s:%s%s',
                $host,
                $this->_serverVars[self::$_KEY_PORT],
                $this->_serverVars[self::$_KEY_URI]
            );

        } else {

            $toReturn .= $this->_serverVars[self::$_KEY_NAME] . $this->_serverVars[self::$_KEY_URI];
        }

        try {

            $this->_cachedCurrentUrl = $this->fromString($toReturn);

        } catch (InvalidArgumentException $e) {

            throw new RuntimeException($e->getMessage());
        }
    }
}
