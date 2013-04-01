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
class tubepress_impl_plugin_FilesystemPluginDiscoverer implements tubepress_spi_plugin_PluginDiscoverer
{
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Filesystem Plugin Discoverer');
    }

    /**
     * Recursively searches a directory for valid TubePress plugins.
     *
     * @param string $directory The path of the directory in which to search.
     *
     * @return array An array of TubePress plugins, which may be empty. Never null.
     */
    public final function findPluginsRecursivelyInDirectory($directory)
    {
        return $this->_find($directory, false);
    }

    /**
     * Shallowly searches a directory for valid TubePress plugins.
     *
     * @param string $directory The path of the directory in which to search.
     *
     * @return array An array of TubePress plugins, which may be empty. Never null.
     */
    public final function findPluginsNonRecursivelyInDirectory($directory)
    {
        return $this->_find($directory, true);
    }

    private function _find($directory, $doNotRecurse)
    {
        if (! is_dir($directory)) {

            return array();
        }

        $finderFactory = tubepress_impl_patterns_sl_ServiceLocator::getFileSystemFinderFactory();

        $finder = $finderFactory->createFinder()->files()->in($directory)->name('*.info');

        if ($doNotRecurse) {

            //this helps with testing :/
            $finder = $finder->depth(0);

        } else {

            //this helps with testing :/
            $finder = $finder->depth('< 3');
        }

        $toReturn = array();

        foreach ($finder as $infoFile) {

            $plugin = $this->_buildPlugin($infoFile);

            if ($plugin !== null) {

                if ($this->_logger->isDebugEnabled()) {

                    $this->_logger->debug('Found valid plugin at ' . $infoFile->getRealpath());
                }

                $toReturn[] = $plugin;
            }
        }

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Found %d valid plugin(s) from %s' , count($toReturn), $directory));
        }

        return $toReturn;
    }

    private function _buildPlugin($infoFile)
    {
        $infoFileContents = @parse_ini_file($infoFile);

        if ($infoFileContents === false || empty($infoFileContents)) {

            if ($this->_logger->isDebugEnabled()) {

                $this->_logger->debug('Could not parse info file at ' . $infoFile->getRealpath());
            }

            return null;
        }

        try {

            return new tubepress_impl_plugin_PluginBase(

                $infoFileContents[tubepress_spi_plugin_Plugin::ATTRIBUTE_NAME],

                $infoFileContents[tubepress_spi_plugin_Plugin::ATTRIBUTE_DESC],

                $infoFileContents[tubepress_spi_plugin_Plugin::ATTRIBUTE_VERSION],

                str_replace('.info', '', basename($infoFile->getRealpath())),

                dirname($infoFile->getRealpath()),

                isset($infoFileContents[tubepress_spi_plugin_Plugin::ATTRIBUTE_IOC_EXTENSIONS]) ?
                    $infoFileContents[tubepress_spi_plugin_Plugin::ATTRIBUTE_IOC_EXTENSIONS] : array(),

                isset($infoFileContents[tubepress_spi_plugin_Plugin::ATTRIBUTE_IOC_COMPILER_PASSES]) ?
                    $infoFileContents[tubepress_spi_plugin_Plugin::ATTRIBUTE_IOC_COMPILER_PASSES] : array(),

                isset($infoFileContents[tubepress_spi_plugin_Plugin::ATTRIBUTE_CLASSPATH_ROOTS]) ?
                    $infoFileContents[tubepress_spi_plugin_Plugin::ATTRIBUTE_CLASSPATH_ROOTS] : array()
            );

        } catch (Exception $e) {

            $this->_logger->warn('Caught exception when parsing info file at ' . $infoFile->getRealpath() . ': ' . $e->getMessage());

            return null;
        }

    }
}
