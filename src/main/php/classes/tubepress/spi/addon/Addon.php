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
 * A TubePress add-on.
 */
interface tubepress_spi_addon_Addon
{
    const _ = 'tubepress_spi_addon_Addon';

    /**
     * Required attributes.
     */
    const ATTRIBUTE_NAME                = 'name';
    const ATTRIBUTE_VERSION             = 'version';
    const ATTRIBUTE_TITLE               = 'title';
    const ATTRIBUTE_AUTHOR              = 'author';
    const ATTRIBUTE_LICENSES            = 'licenses';

    /**
     * Optional attributes.
     */
    const ATTRIBUTE_BOOT_FILES          = 'files';
    const ATTRIBUTE_BOOT_CLASSES        = 'classes';
    const ATTRIBUTE_BOOT_SERVICES       = 'services';
    const ATTRIBUTE_DESCRIPTION         = 'description';
    const ATTRIBUTE_KEYWORDS            = 'keywords';
    const ATTRIBUTE_URL_HOMEPAGE        = 'homepage';
    const ATTRIBUTE_URL_DOCUMENTATION   = 'docs';
    const ATTRIBUTE_URL_DEMO            = 'demo';
    const ATTRIBUTE_URL_DOWNLOAD        = 'download';
    const ATTRIBUTE_URL_BUGS            = 'bugs';
    const ATTRIBUTE_CLASSPATH_ROOTS     = 'psr-0';
    const ATTRIBUTE_CLASSMAP            = 'classmap';
    const ATTRIBUTE_IOC_COMPILER_PASSES = 'compiler-passes';
    const ATTRIBUTE_IOC_EXTENSIONS      = 'container-extensions';

    /**
     * Containers.
     */
    const CATEGORY_AUTOLOAD  = 'autoload';
    const CATEGORY_BOOTSTRAP = 'bootstrap';
    const CATEGORY_IOC       = 'inversion-of-control';
    const CATEGORY_URLS      = 'urls';

    /**
     * @return string The globally unique name of this add-on. Must be 100 characters or less,
     *                all lowercase, and contain only URL-safe characters ([a-z0-9-_\.]+).
     */
    function getName();

    /**
     * @return tubepress_spi_version_Version The version of this add-on.
     */
    function getVersion();

    /**
     * @return string A user-friendly title for the add-on. 255 characters or less.
    */
    function getTitle();

    /**
     * @return array An associative array of author information. The possible array keys are
     *               'name', 'email', and 'url'. 'name' is required, and the other fields are optional.
     */
    function getAuthor();

    /**
     * @return array An array of associative arrays of license information. The possible array keys are
     *               'url' and 'type'. 'url' is required and must link to the license text. 'type'
     *               may be supplied if the license is one of the official open source licenses found
     *               at http://www.opensource.org/licenses/alphabetical
     */
    function getLicenses();

    /**
     * @return array Optional. An array of absolute paths of files that will be include'd when this add-on
     *                         is loaded into the system.
     */
    function getBootstrapFiles();

    /**
     * @return array Optional. An array of service identifiers whose boot() function will be invoked
     *                         when this add-on is loaded into the system.
     */
    function getBootstrapServices();

    /**
     * @return array Optional. An array of fully-qualified class names whose boot() function will be invoked
     *                         when this add-on is loaded into the system.
     */
    function getBootstrapClasses();

    /**
     * @return string Optional. A longer description of this add-on. 1000 characters or less.
     */
    function getDescription();

    /**
     * @return array Optional. An array of keywords that might help folks discover this add-on. Only
     *               letters, numbers, hypens, and dots. Each keyword must be 30 characters or less.
     */
    function getKeywords();

    /**
     * @return ehough_curly_Url Optional. A link to the add-on's homepage.
     */
    function getHomepageUrl();

    /**
     * @return ehough_curly_Url Optional. A link to the add-on's documentation.
     */
    function getDocumentationUrl();

    /**
     * @return ehough_curly_Url Optional. A link to a live demo of the add-on.
     */
    function getDemoUrl();

    /**
     * @return ehough_curly_Url Optional. A link to a download URL.
     */
    function getDownloadUrl();

    /**
     * @return ehough_curly_Url Optional. A link to a bug tracker for this add-on.
     */
    function getBugTrackerUrl();

    /**
     * @return array Optional. An array of IOC container extension class names. May be empty, never null.
     */
    function getIocContainerExtensions();

    /**
     * @return array Optional. An array of IOC compiler pass class names. May be empty, never null.
     */
    function getIocContainerCompilerPasses();

    /**
     * @return array Optional. An array of PSR-0 compliant class path roots. May be empty, never null.
     */
    function getPsr0ClassPathRoots();

    /**
     * @return array Optional. An associative array of class names to the absolute path of their file locations.
     */
    function getClassMap();
}
