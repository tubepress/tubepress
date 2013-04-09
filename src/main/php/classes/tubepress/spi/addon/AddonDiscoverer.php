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
 * Discovers add-ons in directories.
 */
interface tubepress_spi_addon_AddonDiscoverer
{
    const _ = 'tubepress_spi_addon_AddonDiscoverer';

    /**
     * Discovers TubePress add-ons.
     *
     * @param string $directory The absolute path of a directory to search for add-ons.
     *
     * @return array An array of TubePress add-ons, which may be empty. Never null.
     */
    function findAddonsInDirectory($directory);
}
