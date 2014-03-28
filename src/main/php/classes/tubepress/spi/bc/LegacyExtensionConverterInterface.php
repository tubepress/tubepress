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
 * Provides BC for legacy add-ons.
 */
interface tubepress_spi_bc_LegacyExtensionConverterInterface
{
    const _ = 'tubepress_spi_bc_LegacyExtensionConverterInterface';

    /**
     * @param boolean                            $shouldLog          Should we log?
     * @param ehough_epilog_Logger               $logger             Logger.
     * @param int                                $index              Index of add-on.
     * @param int                                $count              Total add-on count.
     * @param tubepress_spi_addon_AddonInterface $addon              The add-on itself.
     * @param string                             $extensionClassName The extension class name.
     *
     * @return boolean True if successfully loaded, false otherwise.
     */
    function evaluateLegacyExtensionClass($shouldLog, ehough_epilog_Logger $logger, $index, $count, tubepress_spi_addon_AddonInterface $addon, $extensionClassName);

    /**
     * @param tubepress_spi_addon_AddonInterface $addon The add-on.
     *
     * @return boolean True if this is a pre 3.2.x add-op, false otherwise.
     */
    function isLegacyAddon(tubepress_spi_addon_AddonInterface $addon);
}
