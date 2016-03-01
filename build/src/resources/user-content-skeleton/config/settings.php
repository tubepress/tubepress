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
     * Settings that many users will want to change for their environment.
     */
    'user' => array(

        /**
         * Various URLs used by TubePress. Typically only standalone PHP users will need to adjust these settings,
         * as they are auto-detected in other environments.
         */
        'urls' => array(

            /**
             * 'base' is the web-accessible URL of your TubePress installation.
             */
            //'base' => 'http://foo.com/path/to/tubepress',

            /**
             * 'userContent' is the URL to your TubePress Content Directory.
             */
            //'userContent' => 'http://foo.com/path/to/tubepress-content',

            /**
             * 'ajax' is the URL where TubePress's client-side code should send its Ajax requests.
             * ** Most users will not need or want to mess with this - TubePress will detect it for you. **
             */
            //'ajax' => 'http://foo.com/path/to/tubepress/ajaxEndpoint.php',
        ),
    ),

    /**
     * System settings. Most users won't need to change these.
     */
    'system' => array(

        /**
         * System cache.
         */
        'cache' => array(

            /**
             * "killerKey" defines the name of the query parameter that you can pass to
             * TubePress to purge the entire system cache. e.g. ?tubepress_clear_system_cache=true
             */
            //'killerKey' => 'tubepress_clear_system_cache',

            /**
             * "directory" defines the absolute path to a writable-directory on the filesystem
             * where TubePress can store its cache data.
             */
            //'directory' => '/some/directory',

            /**
             * "enabled" turns on or off the entire system cache. This is useful during development.
             */
            //'enabled' => true,

            /**
             * "serializationEncoding" defines the encoding, if any, that is applied to serialized
             * data before it is written to the cache. This can often help skirt around strange syntax errors.
             *
             * Can be on of: base64, urlencode, gzip-then-base64, or none
             */
            //'serializationEncoding' => 'base64'
        ),

        /**
         * Add-ons.
         */
        'add-ons' => array(

            /**
             * "blacklist" is an array of add-on names (defined in their respective manifest.json manifests)
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