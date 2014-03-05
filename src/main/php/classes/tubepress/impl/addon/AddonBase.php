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
 * Simple implementation of an add-on.
 */
class tubepress_impl_addon_AddonBase extends tubepress_impl_addon_ContributableBase implements tubepress_spi_addon_Addon
{
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

    /**
     * @var array
     */
    private $_psr0ClassPathRoots = array();

    /**
     * @var array
     */
    private $_iocContainerCompilerPasses = array();

    /**
     * @var array
     */
    private $_iocContainerExtensions = array();

    /**
     * @var array
     */
    private $_classMap = array();

    public function setClassMap(array $map)
    {
        if (!empty($map) && !tubepress_impl_util_LangUtils::isAssociativeArray($map)) {

            throw new InvalidArgumentException('Class map must be an associative array');
        }

        $this->_classMap = $map;
    }

    public function setIocContainerExtensions(array $extensions)
    {
        $this->_validateArrayIsJustStrings($extensions, 'IoC container extensions must be strings');

        $this->_iocContainerExtensions = $extensions;
    }

    public function setIocContainerCompilerPasses(array $passes) {

        $this->_validateArrayIsJustStrings($passes, 'IoC container compiler passes must be strings');

        $this->_iocContainerCompilerPasses = $passes;
    }

    public function setPsr0ClassPathRoots(array $roots)
    {
        $this->_validateArrayIsJustStrings($roots, 'PSR-0 classpath roots must be strings');

        $this->_psr0ClassPathRoots = $roots;
    }

    /**
     * @return array Optional. An array of IOC container extension class names. May be empty, never null.
     */
    public function getIocContainerExtensions()
    {
        return $this->_iocContainerExtensions;
    }

    /**
     * @return array Optional. An array of IOC compiler pass class names. May be empty, never null.
     */
    public function getIocContainerCompilerPasses()
    {
        return $this->_iocContainerCompilerPasses;
    }

    /**
     * @return array Optional. An array of PSR-0 compliant class path roots. May be empty, never null.
     */
    public function getPsr0ClassPathRoots()
    {
        return $this->_psr0ClassPathRoots;
    }

    /**
     * @return array Optional. An associative array of class names to the absolute path of their file locations.
     */
    public function getClassMap()
    {
        return $this->_classMap;
    }

    private function _validateArrayIsJustStrings(array $array, $message)
    {
        foreach($array as $element) {

            if (!is_string($element)) {

                throw new InvalidArgumentException($message);
            }
        }
    }
}
