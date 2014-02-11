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
 * This is the fallback "boot config" file for TubePress. It has a single job;
 * to return an array of config values as seen on line 78 and below.
 */

if (!function_exists('__tubePressGetFilesystemCacheDirectory')) {

    function __tubePressGetFilesystemCacheDirectory()
    {
        if (!function_exists('sys_get_temp_dir')) {

            return null;
        }

        $tmp = rtrim(sys_get_temp_dir(), '/\\') . '/';

        $baseDir = $tmp . 'tubepress-boot-cache/' . md5(dirname(__FILE__)) . '/';

        if (!is_dir($baseDir)) {

            @mkdir($baseDir, 0770, true);
        }

        if (!is_writable($baseDir)) {

            return null;
        }

        return $baseDir;
    }
}

if (!function_exists('__tubePressBuildBootCacheDriver')) {

    function __tubePressBuildBootCacheDriver()
    {
        $filesystemCacheDirectory = __tubePressGetFilesystemCacheDirectory();

        if ($filesystemCacheDirectory !== null) {

            return new ehough_stash_driver_FileSystem(array(

                'path' => $filesystemCacheDirectory,
            ));
        }

        $opCodeCacheOptions = array(

            'ttl'       => 172800,    //two days
            'namespace' => 'tubepress-boot-cache',
        );

        if (ehough_stash_driver_Xcache::isAvailable()) {

            return new ehough_stash_driver_Xcache($opCodeCacheOptions);
        }

        if (ehough_stash_driver_Apc::isAvailable()) {

            return new ehough_stash_driver_Apc($opCodeCacheOptions);
        }

        return new ehough_stash_driver_Ephemeral();
    }
}

return array(

    /**
     * Boot cache configuration. This element is required.
     */
    'cache' => array(

        /**
         * "instance" defines an instance of ehough_stash_interfaces_PoolInterface that
         * TubePress will use to cache its boot configuration.
         */
        'instance'  => new ehough_stash_Pool(__tubePressBuildBootCacheDriver()),

        /**
         * "killerKey" defines the name of the query parameter that you can pass to
         * TubePress to purge the entire boot cache.
         */
        'killerKey' => 'tubepress_clear_boot_cache',
    ),

    /**
     * Add-on configuration.
     */
    'add-ons' => array(

        /**
         * "blacklist" is an array of add-on names (defined in their respective .json manifests)
         * that should *not* be loaded.
         */
        'blacklist' => array(),
    ),

    /**
     * Class loading configuration.
     */
    'classloader' => array(

        /**
         * "enabled" defines whether TubePress should use its own, high-performance classloader.
         * You can disable this if you'd like to rely on an external classloader.
         */
        'enabled' => true,
    )
);