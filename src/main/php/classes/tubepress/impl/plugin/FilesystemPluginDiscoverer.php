<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
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

        $finderFactory = tubepress_impl_patterns_ioc_KernelServiceLocator::getFileSystemFinderFactory();

        $finder = $finderFactory->createFinder()->files()->in($directory)->name('*.info');

        if ($doNotRecurse) {

            $finder->depth(0);
        }

        $toReturn = array();

        foreach ($finder as $infoFile) {

            if ($this->_hasMatchingPhpFile($infoFile)) {

                $plugin = $this->_buildPlugin($infoFile);

                if ($plugin !== null) {

                    if ($this->_logger->isDebugEnabled()) {

                        $this->_logger->debug('Found valid plugin at ' . $infoFile->getRealpath());
                    }

                    $toReturn[] = $plugin;
                }

            } else {

                if ($this->_logger->isDebugEnabled()) {

                    $this->_logger->debug('Found a .info file but missing .php file: ' . $infoFile->getRealpath());
                }
            }
        }

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Found %d valid plugin(s) from %s' , count($toReturn), $directory));
        }

        return $toReturn;
    }

    private function _hasMatchingPhpFile($infoFile)
    {
        $directory            = dirname($infoFile->getRealpath());
        $nameWithoutExtension = str_replace('.info', '', basename($infoFile->getRealpath()));
        $phpFilePath          = "$directory/$nameWithoutExtension.php";

        return is_file($phpFilePath) && is_readable($phpFilePath);
    }

    private function _buildPlugin($infoFile)
    {
        $infoFileContents = @parse_ini_file($infoFile);

        if ($infoFileContents === false) {

            if ($this->_logger->isDebugEnabled()) {

                $this->_logger->debug('Could not parse info file at ' . $infoFile->getRealpath());
            }

            return null;
        }

        try {

            return new tubepress_impl_plugin_PluginBase(

                $infoFileContents['name'],
                $infoFileContents['description'],
                $infoFileContents['version'],
                str_replace('.info', '', basename($infoFile->getRealpath())),
                dirname($infoFile->getRealpath())
            );

        } catch (Exception $e) {

            $this->_logger->warn('Caught exception when parsing info file at ' . $infoFile->getRealpath() . ': ' . $e->getMessage());

            return null;
        }

    }


}
