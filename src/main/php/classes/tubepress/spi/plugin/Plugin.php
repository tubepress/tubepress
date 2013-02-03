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
 * A TubePress plugin.
 */
interface tubepress_spi_plugin_Plugin
{
    const _ = 'tubepress_spi_plugin_Plugin';

    const ATTRIBUTE_CLASSPATH_ROOTS     = 'classPathRoots';
    const ATTRIBUTE_DESC                = 'description';
    const ATTRIBUTE_NAME                = 'name';
    const ATTRIBUTE_IOC_COMPILER_PASSES = 'iocContainerCompilerPasses';
    const ATTRIBUTE_IOC_EXTENSIONS      = 'iocContainerExtensions';
    const ATTRIBUTE_VERSION             = 'version';

    /**
     * @return string The friendly and globally unique name of this plugin. 100 characters or less.
     */
    function getName();

    /**
     * @return string The short (255 chars or less) description of this plugin.
     */
    function getDescription();

    /**
     * @return tubepress_spi_version_Version The version of this plugin.
     */
    function getVersion();

    /**
     * @return string The absolute path to the plugin's directory.
     */
    function getAbsolutePathOfDirectory();

    /**
     * @return string The filename without the .info extension.
     */
    function getFileNameWithoutExtension();

    /**
     * @return array An array of IOC container extensions. May be empty, never null.
     */
    function getIocContainerExtensions();

    /**
     * @return array An array of IOC compiler passes. May be empty, never null.
     */
    function getIocContainerCompilerPasses();

    /**
     * @return array An array of PSR-0 compliant class path roots. May be empty, never null.
     */
    function getPsr0ClassPathRoots();
}
