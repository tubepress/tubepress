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
class tubepress_impl_addon_AddonFinder extends tubepress_impl_contrib_AbstractContributableFinder implements tubepress_spi_addon_AddonFinderInterface
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * @var string[]
     */
    private $_blacklist;

    /**
     * Discovers TubePress add-ons.
     *
     * @param array $blacklist The add-on blacklist.
     *
     * @return array An array of tubepress_spi_addon_AddonInterface instances, which may be empty. Never null.
     */
    public function findAddons(array $blacklist)
    {
        $this->_blacklist = $blacklist;

        return $this->findContributables('/src/main/php/add-ons', '/add-ons');
    }

    public function __callbackSystemAddonSorter(tubepress_spi_addon_AddonInterface $first, tubepress_spi_addon_AddonInterface $second)
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

    protected function getCleanedAttributeValue($attributeName, $candidateValue, $manifestFileAbsPath, array $manifestContents)
    {
        switch ($attributeName) {

            case tubepress_impl_addon_AddonBase::ATTRIBUTE_CLASSPATH_ROOTS:
            case tubepress_impl_addon_AddonBase::ATTRIBUTE_CLASSMAP:

                return $this->_arrayValuesToAbsolutePaths($candidateValue, $manifestFileAbsPath);

            default:

                return $candidateValue;
        }
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

    /**
     * @return array A map of optional attributes.
     */
    protected function getOptionalAttributesMap()
    {
        return array(

            tubepress_impl_addon_AddonBase::CATEGORY_AUTOLOAD             => array(

                tubepress_impl_addon_AddonBase::ATTRIBUTE_CLASSPATH_ROOTS => 'Psr0ClassPathRoots',
                tubepress_impl_addon_AddonBase::ATTRIBUTE_CLASSMAP        => 'ClassMap'
            ),
            tubepress_impl_addon_AddonBase::CATEGORY_IOC                  => array(

                tubepress_impl_addon_AddonBase::ATTRIBUTE_IOC_COMPILER_PASSES => 'IocContainerCompilerPasses',
                tubepress_impl_addon_AddonBase::ATTRIBUTE_IOC_EXTENSIONS      => 'IocContainerExtensions',
            )
        );
    }

    /**
     * @return string The class name that this discoverer instantiates.
     */
    protected function getContributableClassName()
    {
        return 'tubepress_impl_addon_AddonBase';
    }

    protected function getAdditionalRequiredConstructorArgs(array $manifestContents, $absPath)
    {
        return array();
    }

    protected function filter(array &$contributables)
    {
        if (!isset($this->_blacklist)) {

            //this only happens during testing
            return;
        }

        if ($this->shouldLog()) {

            $this->_logger->debug(sprintf('Add-on blacklist: %s', json_encode($this->_blacklist)));
        }

        $addonCount = count($contributables);

        for ($x = 0; $x < $addonCount; $x++) {

            /**
             * @var $addon tubepress_spi_addon_AddonInterface
             */
            $addon     = $contributables[$x];
            $addonName = $addon->getName();

            if (in_array($addonName, $this->_blacklist)) {

                unset($contributables[$x]);
            }
        }

        if ($this->shouldLog()) {

            $this->_logger->debug(sprintf('After blacklist processing, we now have %d add-on(s)', count($contributables)));
        }
    }

    protected function getLogger()
    {
        if (!isset($this->_logger)) {

            $this->_logger = ehough_epilog_LoggerFactory::getLogger('Default Add-on Discoverer');
        }

        return $this->_logger;
    }

    protected function sortSystemContributables(array &$contributables)
    {
        usort($contributables, array($this, '__callbackSystemAddonSorter'));
    }

    protected function getManifestName()
    {
        return '*.json';
    }
}
