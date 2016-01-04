<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * This file is read each time TubePress is invoked. It allows you to change a few settings. Simply
 * uncomment any of the array keys to activate a custom setting.
 */

return array(

    /**
     * System settings. It's unlikely these need to be changed.
     */
    'system' => array(

        /**
         * System cache.
         */
        'cache' => array(

            /**
             * "killerKey" defines the name of the query parameter that you can pass to
             * TubePress to purge the entire system cache.
             */
            //'killerKey' => 'tubepress_clear_system_cache',

            /**
             * "directory" defines the absolute path to a writable-directory on the filesystem
             * where TubePress stores its compiled service container.
             */
            'directory' => sys_get_temp_dir() . '/tubepress-integration-test-cache',

            /**
             * "enabled" turns on or off the entire boot cache. This is useful if you are developing
             * themes or add-ons for TubePress.
             */
            //'enabled' => true,
        ),

        /**
         * Add-ons.
         */
        'add-ons' => array(

            /**
             * "blacklist" is an array of add-on names (defined in their respective .json manifests)
             * that should *not* be loaded.
             */
            //'blacklist' => array(),
        ),

        /**
         * Class loading.
         */
        'classloader' => array(

            /**
             * "enabled" defines whether TubePress should use its own, high-performance classloader.
             * You can disable this if you'd like to rely on an external classloader.
             */
            //'enabled' => true,
        )
    )
);