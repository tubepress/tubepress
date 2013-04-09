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
 * Finds TubePress add-ons in the filesystem.
 */
class tubepress_impl_addon_FilesystemAddonDiscoverer implements tubepress_spi_addon_AddonDiscoverer
{
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Filesystem Add-on Discoverer');
    }

    /**
     * Discovers TubePress add-ons.
     *
     * @param string $directory The absolute path of a directory to search for add-ons.
     *
     * @return array An array of TubePress add-ons, which may be empty. Never null.
     */
    public function findAddonsInDirectory($directory)
    {
        return $this->_find($directory);
    }

    private function _find($directory)
    {
        if (! is_dir($directory)) {

            return array();
        }

        $finderFactory = tubepress_impl_patterns_sl_ServiceLocator::getFileSystemFinderFactory();

        $finder = $finderFactory->createFinder()->files()->in($directory)->name('*.json')->depth(0);

        $toReturn = array();

        foreach ($finder as $infoFile) {

            $plugin = $this->_buildAddon($infoFile);

            if ($plugin !== null) {

                if ($this->_logger->isHandling(ehough_epilog_Logger::DEBUG)) {

                    $this->_logger->debug('Found valid add-on at ' . $infoFile->getRealpath());
                }

                $toReturn[] = $plugin;
            }
        }

        if ($this->_logger->isHandling(ehough_epilog_Logger::DEBUG)) {

            $this->_logger->debug(sprintf('Found %d valid add-on(s) from %s' , count($toReturn), $directory));
        }

        return $toReturn;
    }

    private function _buildAddon(SplFileInfo $infoFile)
    {
        $path = realpath("$infoFile");

        $infoFileContents = @json_decode(file_get_contents($path), true);

        if ($infoFileContents === null || $infoFileContents === false || empty($infoFileContents)) {

            if ($this->_logger->isHandling(ehough_epilog_Logger::DEBUG)) {

                $this->_logger->debug('Could not parse add-on manifest file at ' . $path);
            }

            return null;
        }

        try {

            return $this->_constructAddonFromArray($infoFileContents);

        } catch (Exception $e) {

            $this->_logger->warn('Caught exception when parsing info file at ' . $infoFile->getRealpath() . ': ' . $e->getMessage());

            return null;
        }
    }

    private function _constructAddonFromArray($manifest)
    {
        $requiredAttributeNames = array(

            tubepress_spi_addon_Addon::ATTRIBUTE_NAME,
            tubepress_spi_addon_Addon::ATTRIBUTE_VERSION,
            tubepress_spi_addon_Addon::ATTRIBUTE_TITLE,
            tubepress_spi_addon_Addon::ATTRIBUTE_AUTHOR,
            tubepress_spi_addon_Addon::ATTRIBUTE_LICENSES,
            tubepress_spi_addon_Addon::ATTRIBUTE_BOOTSTRAP
        );

        foreach ($requiredAttributeNames as $requiredAttributeName) {

            if (!isset($manifest[$requiredAttributeName])) {

                throw new RuntimeException("Manifest is missing $requiredAttributeName");
            }
        }

        $addon = new tubepress_impl_addon_AddonBase(

            $manifest[tubepress_spi_addon_Addon::ATTRIBUTE_NAME],
            $manifest[tubepress_spi_addon_Addon::ATTRIBUTE_VERSION],
            $manifest[tubepress_spi_addon_Addon::ATTRIBUTE_TITLE],
            $manifest[tubepress_spi_addon_Addon::ATTRIBUTE_AUTHOR],
            $manifest[tubepress_spi_addon_Addon::ATTRIBUTE_LICENSES],
            $manifest[tubepress_spi_addon_Addon::ATTRIBUTE_BOOTSTRAP]
        );

        $optionalAttributeNames = array(

            tubepress_spi_addon_Addon::ATTRIBUTE_DESCRIPTION         => 'Description',
            tubepress_spi_addon_Addon::ATTRIBUTE_KEYWORDS            => 'Keywords',
            tubepress_spi_addon_Addon::ATTRIBUTE_URL_HOMEPAGE        => 'HomepageUrl',
            tubepress_spi_addon_Addon::ATTRIBUTE_URL_DOCUMENTATION   => 'DocumentationUrl',
            tubepress_spi_addon_Addon::ATTRIBUTE_URL_DEMO            => 'DemoUrl',
            tubepress_spi_addon_Addon::ATTRIBUTE_URL_DOWNLOAD        => 'DownloadUrl',
            tubepress_spi_addon_Addon::ATTRIBUTE_URL_BUGS            => 'BugTrackerUrl',
            tubepress_spi_addon_Addon::ATTRIBUTE_CLASSPATH_ROOTS     => 'Psr0ClassPathRoots',
            tubepress_spi_addon_Addon::ATTRIBUTE_IOC_COMPILER_PASSES => 'IocContainerCompilerPasses',
            tubepress_spi_addon_Addon::ATTRIBUTE_IOC_EXTENSIONS      => 'IocContainerExtensions',
        );

        foreach ($optionalAttributeNames as $optionalAttributeName => $setterSuffix) {

            if (isset($manifest[$optionalAttributeName])) {

                $method = 'set' . $setterSuffix;

                $addon->$method($manifest[$optionalAttributeName]);
            }
        }

        return $addon;
    }
}
