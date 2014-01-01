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
 * Discovers add-ons for TubePress.
 */
class tubepress_impl_boot_DefaultAddonDiscoverer extends tubepress_impl_boot_AbstractCachingBootHelper implements tubepress_spi_boot_AddonDiscoverer
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog = false;

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Default Add-on Discoverer');
    }

    /**
     * Discovers TubePress add-ons.
     *
     * @return array An array of TubePress add-ons, which may be empty. Never null.
     */
    public function findAddons()
    {
        $this->_shouldLog = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        $fromCache = $this->getCachedObject();

        if ($fromCache !== null) {

            return $fromCache;
        }

        $addons = $this->_discoverAddonsFromFilesystem();

        $this->_performBlacklisting($addons);

        $this->tryToCache($addons);

        return $addons;
    }

    /**
     * @return string
     */
    protected function getBootCacheConfigElementName()
    {
        return 'add-ons';
    }

    /**
     * @return ehough_epilog_Logger
     */
    protected function getLogger()
    {
        return $this->_logger;
    }

    /**
     * @param string $string The contents of the cache file.
     *
     * @return object The hydrated object, or null if there was a problem.
     */
    protected function hydrate($string)
    {
        return $this->hydrateByDeserialization($string);
    }

    /**
     * @param object $object The object to convert to a string for the cache.
     *
     * @return string The string representation of the object, or null if there was a problem.
     */
    protected function toString($object)
    {
        return $this->toStringBySerialization($object);
    }

    /**
     * @return bool True if we should log, false otherwise.
     */
    protected function shouldLog()
    {
        return $this->_shouldLog;
    }

    private function _discoverAddonsFromFilesystem()
    {
        /* load add-ons */
        $systemAddons = $this->_findSystemAddons();
        $userAddons   = $this->_findUserAddons();
        $allAddons    = array_merge($systemAddons, $userAddons);
        $addOnCount   = count($allAddons);

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Found %d add-ons (%d system and %d user) on the filesystem',
                $addOnCount, count($systemAddons), count($userAddons)));
        }

        return $allAddons;
    }

    private function _findSystemAddons()
    {
        $coreAddons = $this->_findAddonsInDirectory(TUBEPRESS_ROOT . '/src/main/php/add-ons');

        usort($coreAddons, array($this, '__callbackSystemAddonSorter'));

        return $coreAddons;
    }

    private function _findUserAddons()
    {
        $environmentDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();
        $userContentDir      = $environmentDetector->getUserContentDirectory();
        $userAddonsDir       = $userContentDir . '/add-ons';

        return $this->_findAddonsInDirectory($userAddonsDir);
    }

    /**
     * This is public for test purposes only!
     *
     * @internal
     */
    public function _findAddonsInDirectory($directory)
    {
        if (! is_dir($directory)) {

            return array();
        }

        $finderFactory = tubepress_impl_patterns_sl_ServiceLocator::getFileSystemFinderFactory();
        $finder        = $finderFactory->createFinder()->followLinks()->files()->in($directory)->name('*.json')->depth('< 2');

        $toReturn = array();

        /**
         * @var $infoFile SplFileInfo
         */
        foreach ($finder as $infoFile) {

            $addon = $this->_tryToBuildAddonFromFile($infoFile);

            if ($addon !== null) {

                if ($this->_shouldLog) {

                    $this->_logger->debug('Found valid add-on at ' . $infoFile->getRealpath());
                }

                $toReturn[] = $addon;
            }
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Found %d add-on(s) from %s' , count($toReturn), $directory));
        }

        return $toReturn;
    }

    private function _performBlacklisting(array &$addons)
    {
        $bootConfigService = tubepress_impl_patterns_sl_ServiceLocator::getBootHelperConfigService();
        $addonBlacklist    = $bootConfigService->getAddonBlacklistArray();

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Add-on blacklist: %s', json_encode($addonBlacklist)));
        }

        $addonCount = count($addons);

        for ($x = 0; $x < $addonCount; $x++) {

            /**
             * @var $addon tubepress_spi_addon_Addon
             */
            $addon     = $addons[$x];
            $addonName = $addon->getName();

            if (in_array($addonName, $addonBlacklist)) {

                unset($addons[$x]);
            }
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('After blacklist processing, we now have %d add-on(s)', count($addons)));
        }
    }

    private function _tryToBuildAddonFromFile(SplFileInfo $infoFile)
    {
        $manifestFilePath = realpath("$infoFile");
        $infoFileContents = @json_decode(file_get_contents($manifestFilePath), true);
        $shouldLog        = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        if ($infoFileContents === null || $infoFileContents === false || empty($infoFileContents)) {

            if ($shouldLog) {

                $this->_logger->debug('Could not parse add-on manifest file at ' . $manifestFilePath);
            }

            return null;
        }

        try {

            return $this->_constructAddonFromArray($infoFileContents, $manifestFilePath);

        } catch (Exception $e) {

            if ($shouldLog) {

                $this->_logger->warn('Caught exception when parsing info file at ' . $infoFile->getRealpath() . ': ' . $e->getMessage());
            }

            return null;
        }
    }

    public function __callbackSystemAddonSorter(tubepress_spi_addon_Addon $first, tubepress_spi_addon_Addon $second)
    {
        $firstName  = $first->getName();
        $secondName = $second->getName();

        /*
         * The core add-on always gets loaded first, the pro-core always last.
         */

        if ($firstName === 'tubepress-core-addon' || $secondName === 'tubepress-pro-core-addon') {

            return -1;
        }

        if ($firstName === 'tubepress-pro-core-addon' || $secondName === 'tubepress-core-addon') {

            return 1;
        }

        return 0;
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
            tubepress_spi_addon_Addon::CATEGORY_BOOTSTRAP            => array(

                tubepress_spi_addon_Addon::ATTRIBUTE_BOOT_CLASSES  => 'BootstrapClasses',
                tubepress_spi_addon_Addon::ATTRIBUTE_BOOT_FILES    => 'BootstrapFiles',
                tubepress_spi_addon_Addon::ATTRIBUTE_BOOT_SERVICES => 'BootstrapServices'
            )
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
            case tubepress_spi_addon_Addon::ATTRIBUTE_BOOT_FILES:

                return $this->_arrayValuesToAbsolutePaths($candidateValue, $manifestFileAbsPath);

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
        return dirname($manifestFilePath) . DIRECTORY_SEPARATOR . $path;
    }
}
