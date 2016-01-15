<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
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
class tubepress_internal_contrib_Addon extends tubepress_internal_contrib_AbstractContributable implements tubepress_api_contrib_AddonInterface
{
    /**
     * Optional attributes.
     */
    private static $_PROPERTY_CLASSMAP            = 'classmap';
    private static $_PROPERTY_IOC_COMPILER_PASSES = 'compiler-passes';
    private static $_PROPERTY_IOC_EXTENSIONS      = 'extensions';

    public function __construct($name, $version, $title, array $authors, array $licenses)
    {
        parent::__construct($name, $version, $title, $authors, $licenses);

        $props = $this->getProperties();

        $props->put(self::$_PROPERTY_IOC_EXTENSIONS, array());
        $props->put(self::$_PROPERTY_IOC_COMPILER_PASSES, array());
        $props->put(self::$_PROPERTY_CLASSMAP, array());
    }

    /**
     * @return string[] Optional. An array of IoC container extension class names. May be empty, never null.
     *
     * @api
     * @since 4.0.0
     */
    public function getExtensionClassNames()
    {
        return $this->getProperties()->get(self::$_PROPERTY_IOC_EXTENSIONS);
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
        return $this->getProperties()->get(self::$_PROPERTY_IOC_COMPILER_PASSES);
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
        return $this->getProperties()->get(self::$_PROPERTY_CLASSMAP);

    }

    public function setClassMap(array $map)
    {
        $this->getProperties()->put(self::$_PROPERTY_CLASSMAP, $map);
    }

    public function setCompilerPasses(array $passes)
    {
        $this->getProperties()->put(self::$_PROPERTY_IOC_COMPILER_PASSES, $passes);
    }

    public function setExtensions(array $extensions)
    {
        $this->getProperties()->put(self::$_PROPERTY_IOC_EXTENSIONS, $extensions);
    }
}