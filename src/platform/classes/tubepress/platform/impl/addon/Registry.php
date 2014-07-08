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
class tubepress_platform_impl_addon_Registry extends tubepress_platform_impl_contrib_AbstractRegistry implements tubepress_platform_api_contrib_RegistryInterface
{
    /**
     * @return tubepress_platform_api_contrib_ContributableInterface[] May be empty, never null.
     *
     * @api
     * @since 4.0.0
     */
    public function getAll()
    {
        return $this->findContributables('/src/core', '/add-ons');
    }

    protected function getCleanedAttributeValue($attributeName, $candidateValue, $manifestFileAbsPath, array $manifestContents)
    {
        switch ($attributeName) {

            case tubepress_platform_impl_addon_AddonBase::ATTRIBUTE_CLASSPATH_ROOTS:
            case tubepress_platform_impl_addon_AddonBase::ATTRIBUTE_CLASSMAP:

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
        return dirname(realpath($manifestFilePath)) . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * @return array A map of optional attributes.
     */
    protected function getOptionalAttributesMap()
    {
        return array(

            tubepress_platform_impl_addon_AddonBase::CATEGORY_AUTOLOAD             => array(

                tubepress_platform_impl_addon_AddonBase::ATTRIBUTE_CLASSPATH_ROOTS => 'Psr0ClassPathRoots',
                tubepress_platform_impl_addon_AddonBase::ATTRIBUTE_CLASSMAP        => 'ClassMap'
            ),
            tubepress_platform_impl_addon_AddonBase::CATEGORY_IOC                  => array(

                tubepress_platform_impl_addon_AddonBase::ATTRIBUTE_IOC_COMPILER_PASSES => 'MapOfCompilerPassClassNamesToPriorities',
                tubepress_platform_impl_addon_AddonBase::ATTRIBUTE_IOC_EXTENSIONS      => 'ExtensionClassNames',
            )
        );
    }

    /**
     * @return string The class name that this discoverer instantiates.
     */
    protected function getContributableClassName()
    {
        return 'tubepress_platform_impl_addon_AddonBase';
    }

    protected function getAdditionalRequiredConstructorArgs(array $manifestContents, $absPath)
    {
        return array();
    }

    protected function filter(array &$contributables)
    {
        $blackList = $this->getBootSettings()->getAddonBlacklistArray();

        if ($this->shouldLog()) {

            $this->getLogger()->debug(sprintf('Add-on blacklist: %s', json_encode($blackList)));
        }

        $addonCount = count($contributables);

        for ($x = 0; $x < $addonCount; $x++) {

            /**
             * @var $addon tubepress_platform_api_addon_AddonInterface
             */
            $addon     = $contributables[$x];
            $addonName = $addon->getName();

            if (in_array($addonName, $blackList)) {

                unset($contributables[$x]);
            }
        }

        if ($this->shouldLog()) {

            $this->getLogger()->debug(sprintf('After blacklist processing, we now have %d add-on(s)', count($contributables)));
        }
    }

    protected function getManifestName()
    {
        return 'manifest.json';
    }
}
