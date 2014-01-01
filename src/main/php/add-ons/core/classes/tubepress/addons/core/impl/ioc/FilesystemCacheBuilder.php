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
 * Adds shortcode handlers to TubePress.
 */
class tubepress_addons_core_impl_ioc_FilesystemCacheBuilder
{
    public function buildCache()
    {
        $context = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $dir     = $context->get(tubepress_api_const_options_names_Cache::CACHE_DIR);

        if (!$dir || !is_writable($dir)) {

            @mkdir($dir, 0755, true);
        }

        if (!$dir || !is_writable($dir)) {

            $fs  = tubepress_impl_patterns_sl_ServiceLocator::getFileSystem();
            $dir = $fs->getSystemTempDirectory() . DIRECTORY_SEPARATOR . 'tubepress-api-cache';
        }

        return new ehough_stash_Pool(new ehough_stash_driver_FileSystem(array(

            'path' => $dir
        )));
    }
}