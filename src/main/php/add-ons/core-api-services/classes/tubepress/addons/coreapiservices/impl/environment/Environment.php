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
 * Detects TubePress's environment
 */
class tubepress_addons_coreapiservices_impl_environment_Environment implements tubepress_api_environment_EnvironmentInterface
{
    /**
     * @var tubepress_api_version_Version
     */
    private $_version;

    /**
     * @var tubepress_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_addons_wordpress_spi_WpFunctionsInterface
     */
    private $_wpFunctionsInterface;

    /**
     * @var tubepress_api_url_UrlInterface TubePress installation URL.
     */
    private $_baseUrl;

    /**
     * @var tubepress_api_url_UrlInterface User content URL.
     */
    private $_userContentUrl;

    /**
     * @var bool
     */
    private $_isPro = false;

    /**
     * @var string Cache to reduce computation.
     */
    private $_cacheUserContentDirectory;

    public function __construct(tubepress_api_url_UrlFactoryInterface $urlFactory)
    {
        $this->_version    = tubepress_api_version_Version::parse('9.9.9');
        $this->_urlFactory = $urlFactory;
    }

    /**
     * Detects if the user is running TubePress Pro.
     *
     * @return boolean True is the user is running TubePress Pro. False otherwise.
     */
    public function isPro()
    {
        return $this->_isPro;
    }

    /**
     * Detects if the user is running within WordPress
     *
     * @return boolean True is the user is running within WordPress. False otherwise.
     */
    public function isWordPress()
    {
        return isset($this->_wpFunctionsInterface);
    }

    /**
     * Find the absolute path of the user's content directory. In WordPress, this will be
     * wp-content/tubepress. In standalone PHP, this will be tubepress/content. Confusing, I know.
     *
     * @return string The absolute path of the user's content directory.
     */
    public function getUserContentDirectory()
    {
        if (!isset($this->_cacheUserContentDirectory)) {

            if (defined('TUBEPRESS_CONTENT_DIRECTORY')) {

                $this->_cacheUserContentDirectory = rtrim(TUBEPRESS_CONTENT_DIRECTORY, DIRECTORY_SEPARATOR);

                return $this->_cacheUserContentDirectory;
            }

            if ($this->isWordPress()) {

                if (! defined('WP_CONTENT_DIR' )) {

                    define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
                }

                $this->_cacheUserContentDirectory = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'tubepress-content';

            } else {

                $this->_cacheUserContentDirectory = TUBEPRESS_ROOT . DIRECTORY_SEPARATOR . 'tubepress-content';
            }
        }

        return $this->_cacheUserContentDirectory;
    }

    /**
     * Get the current TubePress version.
     *
     * @return tubepress_api_version_Version The current version.
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * @return tubepress_api_url_UrlInterface The base TubePress URL.
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    public function setBaseUrl($url)
    {
        $this->_baseUrl = $this->_toUrl($url);
    }

    /**
     * @return string The user content URL.
     */
    public function getUserContentUrl()
    {
        if (!isset($this->_userContentUrl)) {

            if (defined('TUBEPRESS_CONTENT_URL')) {

                $url = rtrim(TUBEPRESS_CONTENT_URL, DIRECTORY_SEPARATOR);

            } else {

                if ($this->isWordPress()) {

                    $url = $this->_wpFunctionsInterface->content_url();

                } else {

                    if ($this->getBaseUrl() === null) {

                        return null;
                    }

                    $url = $this->getBaseUrl()->toString();
                }

                $url .= '/tubepress-content';
            }

            try {

                $this->_userContentUrl = $this->_toUrl($url);

            } catch (InvalidArgumentException $e) {

                return null;
            }
        }

        return $this->_userContentUrl;
    }

    /**
     * Set the user content URL.
     *
     * @param mixed $url The user content URL.
     *
     * @return void
     */
    public function setUserContentUrl($url)
    {
        $this->_userContentUrl = $this->_toUrl($url);
    }

    public function setWpFunctionsInterface($wpFunctionsInterface)
    {
        if (!is_a($wpFunctionsInterface, 'tubepress_addons_wordpress_spi_WpFunctionsInterface')) {

            throw new InvalidArgumentException('Invalid argument to tubepress_addons_coreapiservices_impl_environment_Environment::setWpFunctionsInterface');
        }

        $this->_wpFunctionsInterface = $wpFunctionsInterface;
    }

    public function markAsPro()
    {
        $this->_isPro = true;
    }

    private function _toUrl($url)
    {
        if (!($url instanceof tubepress_api_url_UrlInterface)) {

            $url = $this->_urlFactory->fromString($url);
        }

        return $url;
    }
}