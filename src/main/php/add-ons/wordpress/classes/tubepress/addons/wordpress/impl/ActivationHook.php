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

class tubepress_addons_wordpress_impl_ActivationHook
{
    public function execute()
    {
        /* add the content directory if it's not already there */
        if (!is_dir(WP_CONTENT_DIR . '/tubepress-content')) {

            $this->_tryToMirror(
                TUBEPRESS_ROOT . '/src/main/resources/user-content-skeleton/tubepress-content',
                WP_CONTENT_DIR . '/tubepress-content');
        }
    }

    private function _tryToMirror($source, $dest)
    {
        $fs = tubepress_impl_patterns_sl_ServiceLocator::getFileSystem();

        try {

            $fs->mirror($source, $dest);

        } catch (Exception $e) {

            //ignore
        }
    }
}