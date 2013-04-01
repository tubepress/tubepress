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
    private $_version;

    public function __construct()
    {
        $this->_version = tubepress_spi_version_Version::parse('2.5.0');
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
    function getUserContentDirectory()
    {
        if ($this->isWordPress()) {

            if (! defined('WP_CONTENT_DIR' )) {

                define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
            }

            return WP_CONTENT_DIR . '/tubepress-content';

        } else {

            return TUBEPRESS_ROOT . '/tubepress-content';
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
}
