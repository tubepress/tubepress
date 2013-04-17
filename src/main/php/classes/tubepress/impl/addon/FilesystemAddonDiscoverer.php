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
    /**
     * @var ehough_epilog_Logger
     */
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
        if (! is_dir($directory)) {

            return array();
        }

        $finderFactory = tubepress_impl_patterns_sl_ServiceLocator::getFileSystemFinderFactory();

        $finder = $finderFactory->createFinder()->files()->in($directory)->name('*.json')->depth('< 2');

        $toReturn = array();

        /**
         * @var $infoFile SplFileInfo
         */
        foreach ($finder as $infoFile) {

            $addon = $this->_tryToBuildAddonFromFile($infoFile);

            if ($addon !== null) {

                if ($this->_logger->isHandling(ehough_epilog_Logger::DEBUG)) {

                    $this->_logger->debug('Found valid add-on at ' . $infoFile->getRealpath());
                }

                $toReturn[] = $addon;
            }
        }

        if ($this->_logger->isHandling(ehough_epilog_Logger::DEBUG)) {

            $this->_logger->debug(sprintf('Found %d valid add-on(s) from %s' , count($toReturn), $directory));
        }

        return $toReturn;
    }

    private function _tryToBuildAddonFromFile(SplFileInfo $infoFile)
    {
        $manifestFilePath = realpath("$infoFile");

        $infoFileContents = @json_decode(file_get_contents($manifestFilePath), true);

        if ($infoFileContents === null || $infoFileContents === false || empty($infoFileContents)) {

            if ($this->_logger->isHandling(ehough_epilog_Logger::DEBUG)) {

                $this->_logger->debug('Could not parse add-on manifest file at ' . $manifestFilePath);
            }

            return null;
        }

        try {

            return $this->_constructAddonFromArray($infoFileContents, $manifestFilePath);

        } catch (Exception $e) {

            $this->_logger->warn('Caught exception when parsing info file at ' . $infoFile->getRealpath() . ': ' . $e->getMessage());

            return null;
        }
    }

    private function _constructAddonFromArray(array $manifestContentsAsArray, $manifestFileAbsPath)
    {
        $addon = $this->_buildAddonFromRequiredAttributes($manifestContentsAsArray);

        $this->_setOptionalAttributes($addon, $manifestContentsAsArray, $manifestFileAbsPath);

        return $addon;
    }

    private function _setOptionalAttributes(tubepress_spi_addon_Addon $addon, array $manifestContentsAsArray, $manifestFileAbsPath)
    {
        $optionalAttributeMap = array(

            tubepress_spi_addon_Addon::ATTRIBUTE_BOOTSTRAP           => 'Bootstrap',
            tubepress_spi_addon_Addon::ATTRIBUTE_DESCRIPTION         => 'Description',
            tubepress_spi_addon_Addon::ATTRIBUTE_KEYWORDS            => 'Keywords',
            tubepress_spi_addon_Addon::CATEGORY_URLS                 => array(

                tubepress_spi_addon_Addon::ATTRIBUTE_URL_HOMEPAGE       => 'HomepageUrl',
                tubepress_spi_addon_Addon::ATTRIBUTE_URL_DOCUMENTATION  => 'DocumentationUrl',
                tubepress_spi_addon_Addon::ATTRIBUTE_URL_DEMO           => 'DemoUrl',
                tubepress_spi_addon_Addon::ATTRIBUTE_URL_DOWNLOAD       => 'DownloadUrl',
                tubepress_spi_addon_Addon::ATTRIBUTE_URL_BUGS           => 'BugTrackerUrl',
            ),
            tubepress_spi_addon_Addon::CATEGORY_AUTOLOAD             => array(

                tubepress_spi_addon_Addon::ATTRIBUTE_CLASSPATH_ROOTS => 'Psr0ClassPathRoots',
                tubepress_spi_addon_Addon::ATTRIBUTE_CLASSMAP        => 'ClassMap'
            ),
            tubepress_spi_addon_Addon::CATEGORY_IOC                  => array(

                tubepress_spi_addon_Addon::ATTRIBUTE_IOC_COMPILER_PASSES => 'IocContainerCompilerPasses',
                tubepress_spi_addon_Addon::ATTRIBUTE_IOC_EXTENSIONS      => 'IocContainerExtensions',
            ),
        );

        $this->_setOptionalAttributesFromMap($addon, $manifestContentsAsArray, $manifestFileAbsPath, $optionalAttributeMap);
    }

    private function _setOptionalAttributesFromMap(tubepress_spi_addon_Addon $addon, array $manifestContentsAsArray, $manifestFileAbsPath, array $attributeNameToSetterNameMap)
    {
        foreach ($attributeNameToSetterNameMap as $optionalAttributeName => $setterSuffix) {

            /**
             * Dig into array if we need to.
             */
            if (is_array($setterSuffix)) {

                if (isset($manifestContentsAsArray[$optionalAttributeName])) {

                    $this->_setOptionalAttributesFromMap($addon, $manifestContentsAsArray[$optionalAttributeName], $manifestFileAbsPath, $setterSuffix);
                }

                continue;
            }

            if (isset($manifestContentsAsArray[$optionalAttributeName])) {

                $method = 'set' . $setterSuffix;

                $value = $this->_getCleanedAttribute($optionalAttributeName, $manifestContentsAsArray[$optionalAttributeName], $manifestFileAbsPath);

                $addon->$method($value);
            }
        }
    }

    private function _getCleanedAttribute($attributeName, $candidateValue, $manifestFileAbsPath)
    {
        switch ($attributeName) {

            case tubepress_spi_addon_Addon::ATTRIBUTE_CLASSPATH_ROOTS:
            case tubepress_spi_addon_Addon::ATTRIBUTE_CLASSMAP:

                return $this->_arrayValuesToAbsolutePaths($candidateValue, $manifestFileAbsPath);

            case tubepress_spi_addon_Addon::ATTRIBUTE_BOOTSTRAP:

                if (tubepress_impl_util_StringUtils::endsWith($candidateValue, '.php')) {

                    return $this->_getAbsolutePath($candidateValue, $manifestFileAbsPath);
                }

                return $candidateValue;

            default:

                return $candidateValue;
        }
    }

    private function _buildAddonFromRequiredAttributes(array $manifestContentsAsArray)
    {
        $requiredAttributeNames = array(

            tubepress_spi_addon_Addon::ATTRIBUTE_NAME,
            tubepress_spi_addon_Addon::ATTRIBUTE_VERSION,
            tubepress_spi_addon_Addon::ATTRIBUTE_TITLE,
            tubepress_spi_addon_Addon::ATTRIBUTE_AUTHOR,
            tubepress_spi_addon_Addon::ATTRIBUTE_LICENSES
        );

        foreach ($requiredAttributeNames as $requiredAttributeName) {

            if (!isset($manifestContentsAsArray[$requiredAttributeName])) {

                throw new RuntimeException("Manifest is missing $requiredAttributeName");
            }
        }

        return new tubepress_impl_addon_AddonBase(

            $manifestContentsAsArray[tubepress_spi_addon_Addon::ATTRIBUTE_NAME],
            $manifestContentsAsArray[tubepress_spi_addon_Addon::ATTRIBUTE_VERSION],
            $manifestContentsAsArray[tubepress_spi_addon_Addon::ATTRIBUTE_TITLE],
            $manifestContentsAsArray[tubepress_spi_addon_Addon::ATTRIBUTE_AUTHOR],
            $manifestContentsAsArray[tubepress_spi_addon_Addon::ATTRIBUTE_LICENSES]
        );
    }

    private function _arrayValuesToAbsolutePaths(array $paths, $manifestFilePath)
    {
        $toReturn = array();

        foreach ($paths as $prefix => $path) {

            if ($prefix) {

                $toReturn[$prefix] = $this->_getAbsolutePath($path, $manifestFilePath);

            } else {

                $toReturn[] = $this->_getAbsolutePath($path, $manifestFilePath);
            }
        }

        return $toReturn;
    }

    private function _getAbsolutePath($path, $manifestFilePath)
    {
        if (is_dir($path)) {

            return $path;
        }

        return dirname($manifestFilePath) . DIRECTORY_SEPARATOR . $path;
    }
}
