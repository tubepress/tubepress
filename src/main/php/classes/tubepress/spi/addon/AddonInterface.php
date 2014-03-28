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
 * A TubePress add-on.
 */
interface tubepress_spi_addon_AddonInterface extends tubepress_spi_contrib_ContributableInterface
{
    const _ = 'tubepress_spi_addon_AddonInterface';

    /**
     * @return array Optional. An array of IOC container extension class names. May be empty, never null.
     */
    function getIocContainerExtensions();

    /**
     * @return array Optional. An array of IOC compiler pass class names. May be empty, never null.
     */
    function getIocContainerCompilerPasses();

    /**
     * @return array Optional. An array of PSR-0 compliant class path roots. May be empty, never null.
     */
    function getPsr0ClassPathRoots();

    /**
     * @return array Optional. An associative array of class names to the absolute path of their file locations.
     */
    function getClassMap();
}
