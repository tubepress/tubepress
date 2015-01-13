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

/**
 * Adds shortcode handlers to TubePress.
 */
class tubepress_app_impl_vendor_stash_FilesystemCacheBuilder
{
    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_platform_api_boot_BootSettingsInterface
     */
    private $_bootSettings;

    public function __construct(tubepress_app_api_options_ContextInterface        $context,
                                tubepress_platform_api_boot_BootSettingsInterface $bootSettings)
    {
        $this->_context      = $context;
        $this->_bootSettings = $bootSettings;
    }

    public function buildFilesystemDriver()
    {
        $dir = $this->_context->get(tubepress_app_api_options_Names::CACHE_DIRECTORY);

        /**
         * If a path was given, but it's not a directory, let's try to create it.
         */
        if ($dir != '' && !is_dir($dir)) {

            @mkdir($dir, 0755, true);
        }

        /**
         * If the directory exists, but isn't writable, let's try to change that.
         */
        if (is_dir($dir) && !is_writable($dir)) {

            @chmod($dir, 0755);
        }

        /**
         * If we don't have a writable directory, use the system temp directory.
         */
        if (!is_dir($dir) || !is_writable($dir)) {

            $dir = $this->_bootSettings->getPathToSystemCacheDirectory() . '/api-calls';
        }

        $driver = new ehough_stash_driver_FileSystem();
        $driver->setOptions(array('path' => $dir));

        return $driver;
    }
}