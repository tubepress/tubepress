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
class tubepress_impl_theme_ThemeBase extends tubepress_impl_addon_ContributableBase implements tubepress_spi_theme_ThemeInterface
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
        $rootPath)
    {
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
        if (!is_string($parentThemeName)) {

            throw new InvalidArgumentException('Theme parent name must be a string');
        }

        if (preg_match('~^[A-Za-z0-9-_\./]{1,100}$~', $parentThemeName) !== 1) {

            throw new InvalidArgumentException('Invalid parent theme name.');
        }

        $this->_parentThemeName = $parentThemeName;
    }

    /**
     * @param string[] $scripts
     */
    public function setScripts(array $scripts)
    {
        foreach ($scripts as $script) {

            if (!is_string($script)) {

                throw new InvalidArgumentException('Theme scripts must all be strings.');
            }

            if (!tubepress_impl_util_StringUtils::endsWith($script, '.js')) {

                throw new InvalidArgumentException('Theme scripts must all end in .js');
            }
        }

        $this->_scripts = $scripts;
    }

    /**
     * @param string[] $styles
     */
    public function setStyles(array $styles)
    {
        foreach ($styles as $style) {

            if (!is_string($style)) {

                throw new InvalidArgumentException('Theme styles must all be strings.');
            }

            if (!tubepress_impl_util_StringUtils::endsWith($style, '.css')) {

                throw new InvalidArgumentException('Theme styles must all end in .css');
            }
        }

        $this->_styles = $styles;
    }
}