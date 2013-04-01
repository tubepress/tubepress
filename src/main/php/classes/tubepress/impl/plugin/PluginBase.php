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
 * Simple implementation of a plugin.
 */
class tubepress_impl_plugin_PluginBase implements tubepress_spi_plugin_Plugin
{
    /**
     * @var string
     */
    private $_name;

    /**
     * @var string
     */
    private $_description;

    /**
     * @var tubepress_spi_version_Version
     */
    private $_version;

    /**
     * @var string
     */
    private $_fileNameWithoutExtension;

    /**
     * @var string
     */
    private $_absPath;

    /**
     * @var array
     */
    private $_iocContainerExtensions;

    /**
     * @var array
     */
    private $_iocContainerCompilerPasses;

    /**
     * @var array
     */
    private $_psr0ClassPathRoots;

    public function __construct(

        $name,
        $description,
        $version,
        $fileNameWithoutExtension,
        $absolutePath,
        array $iocContainerExtensions,
        array $iocContainerPasses,
        array $psr0ClassPathRoots) {

        $this->_name                      = $name;
        $this->_description               = $description;
        $this->_absPath                   = $absolutePath;
        $this->_fileNameWithoutExtension = $fileNameWithoutExtension;

        if ($version instanceof tubepress_spi_version_Version) {

            $this->_version = $version;

        } else {

            $this->_version = tubepress_spi_version_Version::parse($version);
        }

        $this->_iocContainerExtensions     = $iocContainerExtensions;
        $this->_iocContainerCompilerPasses = $iocContainerPasses;
        $this->_psr0ClassPathRoots         = $psr0ClassPathRoots;
    }

    /**
     * @return string The friendly name of this plugin.
     */
    public final function getName()
    {
        return $this->_name;
    }

    /**
     * @return string The short (255 chars or less) description of this plugin.
     */
    public final function getDescription()
    {
        return $this->_description;
    }

    /**
     * @return tubepress_spi_version_Version The version of this plugin.
     */
    public final function getVersion()
    {
        return $this->_version;
    }

    /**
     * @return string The absolute path to the plugin's directory.
     */
    public final function getAbsolutePathOfDirectory()
    {
        return $this->_absPath;
    }

    /**
     * @return string The filename without the .info extension.
     */
    public final function getFileNameWithoutExtension()
    {
        return $this->_fileNameWithoutExtension;
    }

    /**
     * @return array An array of IOC container extensions. May be empty, never null.
     */
    public final function getIocContainerExtensions()
    {
        return $this->_iocContainerExtensions;
    }

    /**
     * @return array An array of PSR-0 compliant class path roots.
     */
    public final function getPsr0ClassPathRoots()
    {
        return $this->_psr0ClassPathRoots;
    }

    /**
     * @return array An array of IOC compiler passes. May be empty, never null.
     */
    public final function getIocContainerCompilerPasses()
    {
        return $this->_iocContainerCompilerPasses;
    }
}
