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
 * Simple implementation of a theme.
 */
class tubepress_app_theme_impl_ThemeBase extends tubepress_platform_impl_contrib_ContributableBase implements tubepress_app_theme_api_ThemeInterface
{
    /**
     * Optional attributes.
     */
    const ATTRIBUTE_SCRIPTS = 'scripts';
    const ATTRIBUTE_STYLES  = 'styles';
    const ATTRIBUTE_PARENT  = 'parent';

    /**
     * Containers.
     */
    const CATEGORY_RESOURCES  = 'resources';

    /**
     * @var string[]
     */
    private $_scripts = array();

    /**
     * @var string[]
     */
    private $_styles = array();

    /**
     * @var string
     */
    private $_parentThemeName = null;

    /**
     * @var string
     */
    private $_rootFilesystemPath;

    /**
     * @var bool
     */
    private $_isSystemTheme;

    public function __construct(

        $name,
        $version,
        $title,
        array $author,
        array $licenses,
        $isSystemTheme,
        $rootPath) {

        parent::__construct(
            $name,
            $version,
            $title,
            $author,
            $licenses
        );

        $this->_isSystemTheme = (boolean) $isSystemTheme;

        if (!is_dir($rootPath)) {

            throw new InvalidArgumentException(sprintf('%s is not a valid theme root', $rootPath));
        }

        $this->_rootFilesystemPath = rtrim(realpath($rootPath), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string[] An array, which may be empty but never null, of relative paths
     *                  (from the theme root) of scripts for this theme.
     */
    public function getScripts()
    {
        return $this->_scripts;
    }

    /**
     * @return string[] An array, which may be empty but never null, of relative paths
     *                  (from the theme root) of stylesheets for this theme.
     */
    public function getStyles()
    {
        return $this->_styles;
    }

    /**
     * @return string The name of this theme's parent. May be null.
     */
    public function getParentThemeName()
    {
        return $this->_parentThemeName;
    }

    /**
     * @return string The absolute path, with trailing slash, of this theme on the filesystem.
     */
    public function getRootFilesystemPath()
    {
        return $this->_rootFilesystemPath;
    }

    /**
     * @return bool True if this is a system theme, false otherwise.
     */
    public function isSystemTheme()
    {
        return $this->_isSystemTheme;
    }

    /**
     * @param string $parentThemeName
     */
    public function setParentThemeName($parentThemeName)
    {
        $this->validateContributableName($parentThemeName, 'parent theme name');

        $this->_parentThemeName = $parentThemeName;
    }

    /**
     * @param string[] $scripts
     */
    public function setScripts(array $scripts)
    {
        try {

            $this->validateArrayIsJustStrings($scripts, 'scripts');

        } catch (InvalidArgumentException $e) {

            throw new InvalidArgumentException('Each theme script must be a string.');
        }

        $this->_scripts = $scripts;
    }

    /**
     * @param string[] $styles
     */
    public function setStyles(array $styles)
    {
        try {

            $this->validateArrayIsJustStrings($styles, 'styles');

        } catch (InvalidArgumentException $e) {

            throw new InvalidArgumentException('Each theme style must be a string.');
        }


        $this->_styles = $styles;
    }
}