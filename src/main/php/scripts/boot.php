<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

if (! function_exists('bootTubePress')) {

    /**
     * Primary bootstrapping for TubePress.
     */
    function bootTubePress()
    {
        /**
         * First, record the root path.
         */
        define('TUBEPRESS_ROOT', realpath(dirname(__FILE__) . '/../../../../'));

        /**
         * Second, we add our classloader.
         */
        require_once TUBEPRESS_ROOT . '/vendor/ehough/pulsar/src/main/php/ehough/pulsar/ComposerClassLoader.php';

        $loader = new ehough_pulsar_ComposerClassLoader(TUBEPRESS_ROOT . '/vendor/');
        $loader->registerFallbackDirectory(TUBEPRESS_ROOT . '/src/main/php/classes');
        $loader->registerFallbackDirectory(TUBEPRESS_ROOT . '/src/main/php/deprecated_code/classes');
        $loader->register();

        /*
         * Finally, hand off control to the TubePress bootstrapper. This will
         *
         * 1. Setup logging.
         * 2. Build and compile the core IOC container.
         * 3. Load system plugins
         * 4. Load user plugins
         */
        $bootStrapper = new tubepress_impl_bootstrap_TubePressBootstrapper();
        $bootStrapper->boot();
    }
}

/*
 * Don't boot twice.
 */
if (!defined('TUBEPRESS_ROOT')) {

    bootTubePress();
}