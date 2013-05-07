<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
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
     * @var string
     */
    private $_baseUrl;

    public function __construct()
    {
        $this->_version = tubepress_spi_version_Version::parse('3.1.0');
    }

    /**
     * Detects if the user is running TubePress Pro.
     *
     * @return boolean True is the user is running TubePress Pro. False otherwise.
     */
    public function isPro()
    {
        return is_readable(dirname(__FILE__) . '/../../../TubePressPro.php');
    }

    /**
     * Detects if the user is running within WordPress
     *
     * @return boolean True is the user is running within WordPress. False otherwise.
     */
    public function isWordPress()
    {
        return strpos(realpath(__FILE__), 'wp-content' . DIRECTORY_SEPARATOR . 'plugins') !== false
            || function_exists('wp_cron');
    }

    /**
     * Find the absolute path of the user's content directory. In WordPress, this will be
     * wp-content/tubepress. In standalone PHP, this will be tubepress/content. Confusing, I know.
     *
     * @return string The absolute path of the user's content directory.
     */
    public function getUserContentDirectory()
    {
        if (defined('TUBEPRESS_CONTENT_DIRECTORY')) {

            return rtrim(TUBEPRESS_CONTENT_DIRECTORY, DIRECTORY_SEPARATOR);
        }

        if ($this->isWordPress()) {

            if (! defined('WP_CONTENT_DIR' )) {

                define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
            }

            return WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'tubepress-content';

        } else {

            return TUBEPRESS_ROOT . DIRECTORY_SEPARATOR . 'tubepress-content';
        }
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
        if (!($url instanceof ehough_curly_Url)) {

            $url = new ehough_curly_Url($url);
        }

        $this->_baseUrl = rtrim($url->toString(), '/');
    }
}
