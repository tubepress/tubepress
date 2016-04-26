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
class tubepress_wordpress_impl_listeners_wp_ActivationListener
{
    /**
     * @var tubepress_api_boot_BootSettingsInterface
     */
    private $_bootSettings;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $_fs;

    public function __construct(tubepress_api_boot_BootSettingsInterface $bootSettings,
        \Symfony\Component\Filesystem\Filesystem $fileSystem)
    {
        $this->_bootSettings = $bootSettings;
        $this->_fs           = $fileSystem;
    }

    public function onPluginActivation(tubepress_api_event_EventInterface $event)
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

        $templatePaths = array(
            'src/add-ons/embedded-embedplus/templates',
            'src/add-ons/embedded-jwplayer5/templates',
            'src/add-ons/gallery/templates',
            'src/add-ons/html/templates',
            'src/add-ons/player/templates',
            'src/add-ons/pro-player/templates',
            'src/add-ons/pro-search/templates',
            'src/add-ons/provider-dailymotion/templates',
            'src/add-ons/provider-vimeo-v3/templates',
            'src/add-ons/provider-youtube-v3/templates',
            'src/add-ons/search/templates',
            'src/add-ons/single/templates',
        );

        foreach ($templatePaths as $templatePath) {

            /* add templates to the starter theme if necessary */
            $this->_tryToMirror(
                TUBEPRESS_ROOT . "/$templatePath",
                WP_CONTENT_DIR . '/tubepress-content/themes/starter/templates'
            );
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
