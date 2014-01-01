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
class tubepress_impl_environment_SimpleEnvironmentDetector implements tubepress_spi_environment_EnvironmentDetector
{
    /**
     * @var tubepress_spi_version_Version
     */
    private $_version;

    /**
     * @var string TubePress installation URL.
     */
    private $_baseUrl;

    /**
     * @var string User content URL.
     */
    private $_userContentUrl;

    /**
     * @var bool Cache to reduce file lookups.
     */
    private $_cacheIsWordPress;

    /**
     * @var bool Cache to reduce file lookups.
     */
    private $_cacheIsPro;

    /**
     * @var string Cache to reduce computation.
     */
    private $_cacheUserContentDirectory;

    public function __construct()
    {
        $this->_version = tubepress_spi_version_Version::parse('3.1.3');
    }

    /**
     * Detects if the user is running TubePress Pro.
     *
     * @return boolean True is the user is running TubePress Pro. False otherwise.
     */
    public function isPro()
    {
        if (!isset($this->_cacheIsPro)) {

            $this->_cacheIsPro = is_readable(dirname(__FILE__) . '/../../../TubePressPro.php');
        }

        return $this->_cacheIsPro;
    }

    /**
     * Detects if the user is running within WordPress
     *
     * @return boolean True is the user is running within WordPress. False otherwise.
     */
    public function isWordPress()
    {
        if (!isset($this->_cacheIsWordPress)) {

            $this->_cacheIsWordPress = strpos(realpath(__FILE__), 'wp-content' . DIRECTORY_SEPARATOR . 'plugins') !== false
            || function_exists('wp_cron');
        }

        return $this->_cacheIsWordPress;
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
     * @return tubepress_spi_version_Version The current version.
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * @return string The base TubePress URL.
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    public function setBaseUrl($url)
    {
        $this->_baseUrl = $this->_urlToString($url);
    }

    /**
     * @return string The user content URL.
     */
    public function getUserContentUrl()
    {
        if (!isset($this->_userContentUrl)) {

            if (defined('TUBEPRESS_CONTENT_URL')) {

                $this->_userContentUrl = rtrim(TUBEPRESS_CONTENT_URL, DIRECTORY_SEPARATOR);

                return $this->_userContentUrl;
            }

            if ($this->isWordPress()) {

                /**
                 * @var $wordPressFunctionWrapper tubepress_addons_wordpress_spi_WordPressFunctionWrapper
                 */
                $wordPressFunctionWrapper = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);

                $prefix = $wordPressFunctionWrapper->content_url();

            } else {

                $prefix = $this->getBaseUrl();
            }

            $this->_userContentUrl = $this->_urlToString($prefix . '/tubepress-content');
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
        $this->_userContentUrl = $this->_urlToString($url);
    }

    private function _urlToString($url)
    {
        if (!($url instanceof ehough_curly_Url)) {

            $url = new ehough_curly_Url($url);
        }

        return rtrim($url->toString(), '/');
    }
}
