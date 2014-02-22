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
interface tubepress_spi_addon_Addon extends tubepress_spi_addon_Contributable
{
    const _ = 'tubepress_spi_addon_Addon';

    /**
     * Optional attributes.
     */
    const ATTRIBUTE_CLASSPATH_ROOTS     = 'psr-0';
    const ATTRIBUTE_CLASSMAP            = 'classmap';
    const ATTRIBUTE_IOC_COMPILER_PASSES = 'compiler-passes';
    const ATTRIBUTE_IOC_EXTENSIONS      = 'container-extensions';

    /**
     * Containers.
     */
    const CATEGORY_AUTOLOAD  = 'autoload';
    const CATEGORY_IOC       = 'inversion-of-control';
    const CATEGORY_URLS      = 'urls';

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
