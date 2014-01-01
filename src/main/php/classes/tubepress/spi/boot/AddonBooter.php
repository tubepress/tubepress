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
 * Handles loading add-ons into the system.
 */
interface tubepress_spi_boot_AddonBooter
{
    const _ = 'tubepress_spi_boot_AddonBooter';

    /**
     * Loads the given add-on into the system. This consists of including any defined
     * bootstrap files, then calling boot() on any bootstrap services and classes.
     *
     * If errors are encountered, the loader will record them and make a best effort to continue
     * loading the add-on.
     *
     * @param array $addons An array of tubepress_spi_addon_Addon instances.
     *
     * @return mixed An array of string error messages encountered while loading the
     *               add-ons. May be empty, never null.
     */
    function boot(array $addons);
}