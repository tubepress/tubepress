<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_url_impl_puzzle_PuzzleBasedUrl implements tubepress_api_url_UrlInterface
{
    /**
     * @var tubepress_api_url_QueryInterface
     */
    private $_query = null;

    /**
     * @var puzzle_Url
     */
    private $_delegateUrl;

    /**
     * @var bool
     */
    private $_isFrozen = false;

    private static $_DEFAULT_PORTS = array(
        'http'  => 80,
        'https' => 443,
        'ftp'   => 21,
    );

    public function __construct(puzzle_Url $delegate)
    {
        $this->_delegateUrl = $delegate;

        if ($this->_delegateUrl->getQuery()) {

            $this->_query = new tubepress_url_impl_puzzle_PuzzleBasedQuery($this->_delegateUrl->getQuery());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addPath($relativePath)
    {
        $this->_assertNotFrozen();

        if ($this->getPath() === '/') {

            $this->_delegateUrl->setPath('');
        }

        $this->_delegateUrl->addPath($relativePath);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthority()
    {
        $userName = $this->getUsername();
        $password = $this->getPassword();
        $host     = $this->getHost();
        $port     = $this->getPort();
        $scheme   = $this->getScheme();

        if ($port && isset(self::$_DEFAULT_PORTS[$scheme]) && intval($port) === self::$_DEFAULT_PORTS[$scheme]) {

            $port = '';
        }

        $authority = '';

        if ($userName) {

            $authority .= $userName;
        }

        if ($password) {

            $authority .= ":$password";
        }

        if ($userName || $password) {

            $authority .= '@';
        }

        $authority .= "$host";

        if ($port) {

            $authority .= ":$port";
        }

        return $authority;
    }

    /**
     * {@inheritdoc}
     */
    public function getFragment()
    {
        return $this->_delegateUrl->getFragment();
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return $this->_delegateUrl->getHost();
    }

    /**
     * {@inheritdoc}
     */
    public function getParts()
    {
        return $this->_delegateUrl->getParts();
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->_delegateUrl->getPassword();
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->_delegateUrl->getPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getPathSegments()
    {
        return $this->_delegateUrl->getPathSegments();
    }

    /**
     * {@inheritdoc}
     */
    public function getPort()
    {
        return $this->_delegateUrl->getPort();
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        return $this->_query;
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme()
    {
        return $this->_delegateUrl->getScheme();
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->_delegateUrl->getUsername();
    }

    /**
     * {@inheritdoc}
     */
    public function isAbsolute()
    {
        return $this->_delegateUrl->isAbsolute();
    }

    /**
     * {@inheritdoc}
     */
    public function removeDotSegments()
    {
        $this->_assertNotFrozen();

        $this->_delegateUrl->removeDotSegments();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFragment($fragment)
    {
        $this->_assertNotFrozen();

        $this->_delegateUrl->setFragment($fragment);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setHost($host)
    {
        $this->_assertNotFrozen();

        $this->_delegateUrl->setHost($host);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPassword($password)
    {
        $this->_assertNotFrozen();

        $this->_delegateUrl->setPassword($password);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($path)
    {
        $this->_assertNotFrozen();

        $this->_delegateUrl->setPath($path);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPort($port)
    {
        $this->_assertNotFrozen();

        $this->_delegateUrl->setPort($port);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuery($query)
    {
        $this->_assertNotFrozen();

        if ($query === null) {

            $this->_query = null;

            return $this;
        }

        if ($query instanceof tubepress_api_url_QueryInterface) {

            $this->_query = $query;

            return $this;
        }

        if (is_string($query)) {

            $puzzleQuery = puzzle_Query::fromString($query);

        } else {

            $puzzleQuery = new puzzle_Query($query);
        }

        $this->_query = new tubepress_url_impl_puzzle_PuzzleBasedQuery($puzzleQuery);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setScheme($scheme)
    {
        $this->_assertNotFrozen();

        $this->_delegateUrl->setScheme($scheme);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsername($username)
    {
        $this->_assertNotFrozen();

        $this->_delegateUrl->setUsername($username);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $parts = $this->_delegateUrl->getParts();

        if ($this->_query) {

            $parts['query'] = $this->_query;

        } else {

            unset($parts['query']);
        }

        return puzzle_Url::buildUrl($parts);
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        return $this->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function getClone()
    {
        return new self(puzzle_Url::fromString($this->toString()));
    }

    /**
     * {@inheritdoc}
     */
    public function removeSchemeAndAuthority()
    {
        $this->_assertNotFrozen();

        $this->setScheme(null);
        $this->setHost(null);
        $this->setUsername(null);
        $this->setPort(null);
        $this->setPassword(null);
    }

    /**
     * {@inheritdoc}
     */
    public function freeze()
    {
        $this->_query->freeze();
        $this->_isFrozen = true;
    }

    /**
     * {@inheritdoc}
     */
    public function isFrozen()
    {
        return $this->_isFrozen;
    }

    private function _assertNotFrozen()
    {
        if ($this->_isFrozen) {

            throw new BadMethodCallException('URL is frozen');
        }
    }
}
