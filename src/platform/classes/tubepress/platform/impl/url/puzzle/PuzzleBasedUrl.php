<?php
/**
 * Copyright 2006 - 2013 Eric D. Hough (http://ehough.com)
 *
 * This file is part of coauthor (https://github.com/ehough/coauthor)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_platform_impl_url_puzzle_PuzzleBasedUrl implements tubepress_platform_api_url_UrlInterface
{
    /**
     * @var tubepress_platform_api_url_QueryInterface
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
        'ftp'   => 21
    );

    public function __construct(puzzle_Url $delegate)
    {
        $this->_delegateUrl = $delegate;

        if ($this->_delegateUrl->getQuery()) {

            $this->_query = new tubepress_platform_impl_url_puzzle_PuzzleBasedQuery($this->_delegateUrl->getQuery());
        }
    }

    /**
     * Add a relative path to the currently set path.
     *
     * @param string $relativePath Relative path to add
     *
     * @return tubepress_platform_api_url_UrlInterface
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
     * Get the authority part of the URL
     *
     * @return null|string
     */
    public function getAuthority()
    {
        $userName  = $this->getUsername();
        $password  = $this->getPassword();
        $host      = $this->getHost();
        $port      = $this->getPort();
        $scheme    = $this->getScheme();

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
     * Get the fragment part of the URL
     *
     * @return null|string
     */
    public function getFragment()
    {
        return $this->_delegateUrl->getFragment();
    }

    /**
     * Get the host part of the URL
     *
     * @return string
     */
    public function getHost()
    {
        return $this->_delegateUrl->getHost();
    }

    /**
     * Get the parts of the URL as an array
     *
     * @return array
     */
    public function getParts()
    {
        return $this->_delegateUrl->getParts();
    }

    /**
     * Get the password part of the URL
     *
     * @return null|string
     */
    public function getPassword()
    {
        return $this->_delegateUrl->getPassword();
    }

    /**
     * Get the path part of the URL
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_delegateUrl->getPath();
    }

    /**
     * Get the path segments of the URL as an array
     *
     * @return array
     */
    public function getPathSegments()
    {
        return $this->_delegateUrl->getPathSegments();
    }

    /**
     * Get the port part of the URl.
     *
     * If no port was set, this method will return the default port for the
     * scheme of the URI.
     *
     * @return int|null
     */
    public function getPort()
    {
        return $this->_delegateUrl->getPort();
    }

    /**
     * @return tubepress_platform_api_url_QueryInterface
     */
    public function getQuery()
    {
        return $this->_query;
    }

    /**
     * Get the scheme part of the URL
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->_delegateUrl->getScheme();
    }

    /**
     * Get the username part of the URl
     *
     * @return null|string
     */
    public function getUsername()
    {
        return $this->_delegateUrl->getUsername();
    }

    /**
     * Check if this is an absolute URL
     *
     * @return bool
     */
    public function isAbsolute()
    {
        return $this->_delegateUrl->isAbsolute();
    }

    /**
     * Removes dot segments from a URL
     *
     * @return tubepress_platform_api_url_UrlInterface
     * @link http://tools.ietf.org/html/rfc3986#section-5.2.4
     */
    public function removeDotSegments()
    {
        $this->_assertNotFrozen();

        $this->_delegateUrl->removeDotSegments();

        return $this;
    }

    /**
     * Set the fragment part of the URL
     *
     * @param string $fragment Fragment to set
     *
     * @return tubepress_platform_api_url_UrlInterface
     */
    public function setFragment($fragment)
    {
        $this->_assertNotFrozen();

        $this->_delegateUrl->setFragment($fragment);

        return $this;
    }

    /**
     * Set the host of the request.
     *
     * @param string $host Host to set (e.g. www.yahoo.com, yahoo.com)
     *
     * @return tubepress_platform_api_url_UrlInterface
     */
    public function setHost($host)
    {
        $this->_assertNotFrozen();

        $this->_delegateUrl->setHost($host);

        return $this;
    }

    /**
     * Set the password part of the URL
     *
     * @param string $password Password to set
     *
     * @return tubepress_platform_api_url_UrlInterface
     */
    public function setPassword($password)
    {
        $this->_assertNotFrozen();

        $this->_delegateUrl->setPassword($password);

        return $this;
    }

    /**
     * Set the path part of the URL
     *
     * @param string $path Path string to set
     *
     * @return tubepress_platform_api_url_UrlInterface
     */
    public function setPath($path)
    {
        $this->_assertNotFrozen();

        $this->_delegateUrl->setPath($path);

        return $this;
    }

    /**
     * Set the port part of the URL
     *
     * @param int $port Port to set
     *
     * @return tubepress_platform_api_url_UrlInterface
     */
    public function setPort($port)
    {
        $this->_assertNotFrozen();

        $this->_delegateUrl->setPort($port);

        return $this;
    }

    /**
     * Set the query part of the URL
     *
     * @param tubepress_platform_api_url_QueryInterface|string|array $query Query string value to set. Can
     *                                                             be a string that will be parsed into a tubepress_platform_api_url_QueryInterface object, an array
     *                                                             of key value pairs, or a tubepress_platform_api_url_QueryInterface object.
     *
     * @return tubepress_platform_api_url_UrlInterface
     * @throws InvalidArgumentException
     */
    public function setQuery($query)
    {
        $this->_assertNotFrozen();

        if ($query === null) {

            $this->_query = null;

            return $this;
        }

        if ($query instanceof tubepress_platform_api_url_QueryInterface) {

            $this->_query = $query;

            return $this;
        }

        if (is_string($query)) {

            $puzzleQuery = puzzle_Query::fromString($query);

        } else {

            $puzzleQuery = new puzzle_Query($query);
        }

        $this->_query = new tubepress_platform_impl_url_puzzle_PuzzleBasedQuery($puzzleQuery);

        return $this;
    }

    /**
     * Set the scheme part of the URL (http, https, ftp, etc)
     *
     * @param string $scheme Scheme to set
     *
     * @return tubepress_platform_api_url_UrlInterface
     */
    public function setScheme($scheme)
    {
        $this->_assertNotFrozen();

        $this->_delegateUrl->setScheme($scheme);

        return $this;
    }

    /**
     * Set the username part of the URL
     *
     * @param string $username Username to set
     *
     * @return tubepress_platform_api_url_UrlInterface
     */
    public function setUsername($username)
    {
        $this->_assertNotFrozen();

        $this->_delegateUrl->setUsername($username);

        return $this;
    }

    /**
     * Alias of toString()
     *
     * @return string
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
     * Returns the URL as a URL string
     *
     * @return string
     */
    public function toString()
    {
        return $this->__toString();
    }

    /**
     * Clones the given URL.
     *
     * @return tubepress_platform_api_url_UrlInterface
     */
    public function getClone()
    {
        return new self(puzzle_Url::fromString($this->toString()));
    }

    /**
     * @return void
     *
     * @api
     * @since 4.0.0
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
     * Prevent any modifications to this URL.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function freeze()
    {
        $this->_query->freeze();
        $this->_isFrozen = true;
    }

    /**
     * @return bool True if this URL is frozen, false otherwise.
     *
     * @api
     * @since 4.0.0
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