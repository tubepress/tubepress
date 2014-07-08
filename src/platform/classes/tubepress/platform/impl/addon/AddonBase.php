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
class tubepress_platform_impl_addon_AddonBase extends tubepress_platform_impl_contrib_ContributableBase implements tubepress_platform_api_addon_AddonInterface
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
        if (!empty($map) && !$this->_isAssociativeArray($map)) {

            throw new InvalidArgumentException('Class map must be an associative array');
        }

        foreach ($map as $key => $value) {

            $this->validateIsString($key, 'each classmap prefix');
            $this->validateIsString($value, 'each classmap path');
        }

        $this->_classMap = $map;
    }

    public function setExtensionClassNames(array $extensions)
    {
        $this->validateArrayIsJustStrings($extensions, 'Each IoC container extension');

        $this->_iocContainerExtensions = $extensions;
    }

    public function setMapOfCompilerPassClassNamesToPriorities(array $passes) {

        if (!$this->_isAssociativeArray($passes)) {

            throw new InvalidArgumentException('Compiler pass config must be an associative array.');
        }

        $this->validateArrayIsJustStrings(array_keys($passes), 'Each IoC container compiler pass');

        $this->_iocContainerCompilerPasses = $passes;
    }

    public function setPsr0ClassPathRoots(array $roots)
    {
        $this->validateArrayIsJustStrings($roots, 'Each PSR-0 classpath root');

        $this->_psr0ClassPathRoots = $roots;
    }

    /**
     * @return array Optional. An array of IoC container extension class names. May be empty, never null.
     *
     * @api
     * @since 4.0.0
     */
    public function getExtensionClassNames()
    {
        return $this->_iocContainerExtensions;
    }

    /**
     * @return array Optional. An associative array of IOC compiler pass class names to their corresponding priorities.
     *                         Higher priorities will be processed first. May be empty, never null.
     *
     * @api
     * @since 4.0.0
     */
    public function getMapOfCompilerPassClassNamesToPriorities()
    {
        return $this->_iocContainerCompilerPasses;
    }

    /**
     * @return array Optional. An array of PSR-0 compliant class path roots. May be empty, never null.
     *
     * @api
     * @since 4.0.0
     */
    public function getPsr0ClassPathRoots()
    {
        return $this->_psr0ClassPathRoots;
    }

    /**
     * @return array Optional. An associative array of class names to the absolute path of their file locations.
     *                         May be empty, never null.
     *
     * @api
     * @since 4.0.0
     */
    public function getClassMap()
    {
        return $this->_classMap;
    }

    private function _isAssociativeArray($candidate)
    {
        return is_array($candidate)
            && ! empty($candidate)
            && count(array_filter(array_keys($candidate),'is_string')) == count($candidate);
    }
}
