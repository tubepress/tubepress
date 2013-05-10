<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Registry of all add-ons.
 */
interface tubepress_spi_addon_AddonLoader
{
    const _ = 'tubepress_spi_addon_AddonLoader';

    /**
     * Loads the given add-on into the system. This consists of including any defined
     * bootstrap files, then calling boot() on any bootstrap services and classes.
     *
     * If errors are encountered, the loader will record them and make a best effort to continue
     * loading the add-on.
     *
     * @param tubepress_spi_addon_Addon $addon
     *
     * @return mixed An array of string error messages encountered while loading the
     *               add-on. May be empty, never null.
     */
    function load(tubepress_spi_addon_Addon $addon);
}
