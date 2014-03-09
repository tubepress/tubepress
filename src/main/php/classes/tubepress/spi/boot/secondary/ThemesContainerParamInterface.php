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
 * Discovers themes for TubePress.
 */
interface tubepress_spi_boot_secondary_ThemesContainerParamInterface
{
    const _ = 'tubepress_spi_boot_secondary_ThemesContainerParamInterface';

    /**
     * Discovers TubePress themes.
     *
     * @return array An array data of the discovered TubePress themes.
     */
    function getThemesContainerParameterValue();
}