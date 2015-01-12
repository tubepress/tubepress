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

class tubepress_wordpress_impl_wp_ActivationHook
{
    /**
     * @var tubepress_platform_api_boot_BootSettingsInterface
     */
    private $_bootSettings;

    /**
     * @var ehough_filesystem_FilesystemInterface
     */
    private $_fs;

    public function __construct(tubepress_platform_api_boot_BootSettingsInterface $bootSettings,
                                ehough_filesystem_FilesystemInterface    $fileSystem)
    {
        $this->_bootSettings = $bootSettings;
        $this->_fs           = $fileSystem;
    }

    public function execute()
    {
        /* add the content directory if it's not already there */
        if (!is_dir(WP_CONTENT_DIR . '/tubepress-content')) {

            $this->_tryToMirror(
                TUBEPRESS_ROOT . '/src/add-ons/wordpress/resources/user-content-skeleton',
                WP_CONTENT_DIR . '/tubepress-content');
        }

        /* add the starter theme if it's not already there */
        if (!is_dir(WP_CONTENT_DIR . '/tubepress-content/themes/starter')) {

            $this->_tryToMirror(
                TUBEPRESS_ROOT . '/src/add-ons/wordpress/resources/user-content-skeleton/themes/starter',
                WP_CONTENT_DIR . '/tubepress-content/themes/starter');
        }

        /* add templates to the starter theme if necessary */
        if (!is_dir(WP_CONTENT_DIR . '/tubepress-content/themes/starter/templates')) {

            $this->_tryToMirror(
                TUBEPRESS_ROOT . '/src/add-ons/core/templates/public',
                WP_CONTENT_DIR . '/tubepress-content/themes/starter/templates');
        }
    }

    private function _tryToMirror($source, $dest)
    {
        try {

            $this->_fs->mirror($source, $dest);

        } catch (Exception $e) {

            //ignore
        }
    }
}