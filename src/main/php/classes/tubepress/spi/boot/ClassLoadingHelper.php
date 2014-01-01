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
 * Constructs an efficient classloader.
 */
interface tubepress_spi_boot_ClassLoadingHelper
{
    const _ = 'tubepress_spi_boot_ClassLoadingHelper';

    /**
     * Load the rest of the default classmap into this classloader.
     *
     * @param ehough_pulsar_ComposerClassLoader $classLoader
     */
    function prime(ehough_pulsar_ComposerClassLoader &$classLoader);

    /**
     * Loads the PSR-0 class paths and any classmaps for this add-on into
     * the system's primary classloader.
     *
     * @param array                             $addons
     * @param ehough_pulsar_ComposerClassLoader $classLoader
     *
     * @return void
     */
    function addClassHintsForAddons(array $addons, ehough_pulsar_ComposerClassLoader $classLoader);
}