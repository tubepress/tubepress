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
 * A TubePress theme handler
 */
class tubepress_core_theme_impl_ThemeLibrary implements tubepress_core_theme_api_ThemeLibraryInterface
{
    private static $_DEFAULT_THEME_NAME = 'tubepress/default';

    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var boolean
     */
    private $_shouldLog;

    /**
     * @var tubepress_core_environment_api_EnvironmentInterface
     */
    private $_environment;

    /**
     * @var array
     */
    private $_themeMap;

    /**
     * @var tubepress_core_url_api_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_util_LangUtilsInterface
     */
    private $_langUtils;

    public function __construct(array                                               $themeMap,
                                tubepress_core_options_api_ContextInterface         $context,
                                tubepress_core_environment_api_EnvironmentInterface $environment,
                                tubepress_core_url_api_UrlFactoryInterface          $urlFactory,
                                tubepress_api_util_LangUtilsInterface               $langUtils,
                                tubepress_api_log_LoggerInterface                   $log)
    {
        $this->_logger      = $log;
        $this->_themeMap    = $themeMap;
        $this->_environment = $environment;
        $this->_urlFactory  = $urlFactory;
        $this->_context     = $context;
        $this->_langUtils   = $langUtils;
        $this->_shouldLog   = $log->isEnabled();
    }

    /**
     * @param string|null $themeName The name of the theme to query, or null for the currently stored theme.
     *
     * @return tubepress_core_url_api_UrlInterface[] URLs of CSS stylesheets required for the current theme. May be empty but never null.
     *
     * @throws InvalidArgumentException If no such theme.
     */
    public function getStylesUrls($themeName = null)
    {
        if (!$themeName) {

            $themeName = $this->getCurrentThemeName();
        }

        $styles = $this->_recursivelyGetResourceUrlsForTheme($themeName, tubepress_core_theme_impl_ThemeBase::ATTRIBUTE_STYLES);
        $styles = array_map(array($this->_urlFactory, 'fromString'), $styles);

        return $styles;
    }

    /**
     * @param string|null $themeName The name of the theme to query, or null for the currently stored theme.
     *
     * @return tubepress_core_url_api_UrlInterface[] URLs of JS scripts required for the current theme. May be empty but never null.
     *
     * @throws InvalidArgumentException If no such theme.
     */
    public function getScriptsUrls($themeName = null)
    {
        if (!$themeName) {

            $themeName = $this->getCurrentThemeName();
        }

        $themeScripts   = $this->_recursivelyGetResourceUrlsForTheme($themeName, tubepress_core_theme_impl_ThemeBase::ATTRIBUTE_SCRIPTS);
        $themeScripts   = array_map(array($this->_urlFactory, 'fromString'), $themeScripts);
        $baseUrl        = $this->_urlFactory->fromString($this->_environment->getBaseUrl()->toString());
        $tubePressJsUrl = $baseUrl->addPath('/src/core/html/web/js/tubepress.js');

        array_unshift($themeScripts, $tubePressJsUrl);

        return $themeScripts;
    }

    /**
     * @param string $relativePath The relative path to the template.
     * @param string|null $themeName The theme name, or null to use the current theme.
     *
     * @return string The absolute path to the given template, or null if not found in the theme hierarchy.
     *
     * @throws InvalidArgumentException If no such theme.
     */
    public function getAbsolutePathToTemplate($relativePath, $themeName = null)
    {
        if ($themeName === null) {

            $themeName = $this->getCurrentThemeName();
        }

        $relativePath = ltrim($relativePath, DIRECTORY_SEPARATOR);

        /**
         * First try to load the template from the requested theme.
         */
        if (in_array($relativePath, $this->_themeMap[$themeName][tubepress_core_theme_ioc_compiler_ThemesPrimerPass::ATTRIBUTE_TEMPLATES])) {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Found direct hit for template "%s" in "%s" theme.', $relativePath, $themeName));
            }

            return $this->_toAbsPath($themeName, $relativePath);
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('No direct hit for template "%s" in "%s" theme. Checking theme parent (if any).', $relativePath, $themeName));
        }

        while (true) {

            if (!isset($this->_themeMap[$themeName][tubepress_core_theme_ioc_compiler_ThemesPrimerPass::ATTRIBUTE_PARENT])) {

                break;
            }

            /**
             * Next try the parent.
             */
            $parent = $this->_themeMap[$themeName][tubepress_core_theme_ioc_compiler_ThemesPrimerPass::ATTRIBUTE_PARENT];

            if (!$parent) {

                break;
            }

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Parent of "%s" theme is "%s".', $themeName, $parent));
            }

            $themeName = $parent;

            if (in_array($relativePath, $this->_themeMap[$themeName][tubepress_core_theme_ioc_compiler_ThemesPrimerPass::ATTRIBUTE_TEMPLATES])) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('Found template "%s" in theme "%s".', $relativePath, $themeName));
                }

                return $this->_toAbsPath($themeName, $relativePath);
            }
        }

        return null;
    }

    /**
     * @param string $themeName The name of the theme to query, or null for the currently stored theme.
     *
     * @return string[] URLs of screenshots for the current theme. May be empty but never null.
     */
    public function getScreenshots($themeName = null)
    {
        if (!$themeName) {

            $themeName = $this->getCurrentThemeName();
        }

        $shots = $this->_getResourceUrlsForTheme($themeName, tubepress_core_theme_impl_ThemeBase::ATTRIBUTE_SCREENSHOTS);

        if ($this->_langUtils->isAssociativeArray($shots)) {

            return $shots;
        }

        $toReturn = array();
        foreach ($shots as $shot) {

            $toReturn[$shot] = $shot;
        }

        return $toReturn;
    }

    /**
     * @return string The current theme name.
     */
    public function getCurrentThemeName()
    {
        $currentTheme = $this->_context->get(tubepress_core_theme_api_Constants::OPTION_THEME);

        if ($currentTheme == '') {

            $currentTheme = self::$_DEFAULT_THEME_NAME;
        }

        if (array_key_exists($currentTheme, $this->_themeMap)) {

            return $currentTheme;
        }

        if (array_key_exists("tubepress/legacy-$currentTheme", $this->_themeMap)) {

            return "tubepress/legacy-$currentTheme";
        }

        if (array_key_exists("unknown/legacy-$currentTheme", $this->_themeMap)) {

            return "unknown/legacy-$currentTheme";
        }

        return self::$_DEFAULT_THEME_NAME;
    }

    /**
     * @return string[] An associative array of strings, which may be empty but never null, of all known theme
     *                  names to their untranslated titles.
     */
    public function getMapOfAllThemeNamesToTitles()
    {
        $toReturn = array();

        foreach ($this->_themeMap as $themeName => $data) {

            $themeTitle           = $data[tubepress_core_theme_ioc_compiler_ThemesPrimerPass::ATTRIBUTE_TITLE];
            $toReturn[$themeName] = $themeTitle;
        }

        return $toReturn;
    }

    private function _recursivelyGetResourceUrlsForTheme($themeName, $key)
    {
        $toReturn = $this->_getResourceUrlsForTheme($themeName, $key);

        if ($themeName === self::$_DEFAULT_THEME_NAME) {

            return $toReturn;
        }

        $parent = $this->_themeMap[$themeName][tubepress_core_theme_ioc_compiler_ThemesPrimerPass::ATTRIBUTE_PARENT];

        if (!$parent) {

            return $toReturn;
        }

        $toReturn = array_merge($this->_recursivelyGetResourceUrlsForTheme($parent, $key), $toReturn);

        return $toReturn;
    }

    private function _getResourceUrlsForTheme($themeName, $key)
    {
        $toReturn            = array();
        $themeAbsPath        = $this->_themeMap[$themeName][tubepress_core_theme_ioc_compiler_ThemesPrimerPass::ATTRIBUTE_THEME_ROOT];
        $themeBaseName       = basename($themeAbsPath);
        $resources           = $this->_themeMap[$themeName][$key];
        $isSystemTheme       = $this->_themeMap[$themeName][tubepress_core_theme_ioc_compiler_ThemesPrimerPass::ATTRIBUTE_IS_SYSTEM];

        if ($this->_langUtils->isAssociativeArray($resources)) {

            foreach ($resources as $keyUrl => $valUrl) {

                $key = $this->_resourceUrlToAbsolute($keyUrl, $isSystemTheme, $themeBaseName);
                $val = $this->_resourceUrlToAbsolute($valUrl, $isSystemTheme, $themeBaseName);

                $toReturn[$key] = $val;
            }

        } else {

            foreach ($resources as $url) {

                $toReturn[] = $this->_resourceUrlToAbsolute($url, $isSystemTheme, $themeBaseName);
            }
        }

        return $toReturn;
    }

    private function _resourceUrlToAbsolute($url, $isSystemTheme, $themeBaseName)
    {
        if (strpos($url, 'http') === 0) {

            try {

                $this->_urlFactory->fromString($url);

                return $url;

            } catch (InvalidArgumentException $e) {

                return null;
            }
        }

        if ($isSystemTheme) {

            $prefix = $this->_environment->getBaseUrl()->toString() . '/src/core/themes/web/';

        } else {

            $prefix = $this->_environment->getUserContentUrl()->toString() . '/themes/';
        }

        return $prefix . $themeBaseName . '/' . $url;
    }

    private function _toAbsPath($themeName, $relativeTemplatePath)
    {
        $themeAbsPath = $this->_themeMap[$themeName][tubepress_core_theme_ioc_compiler_ThemesPrimerPass::ATTRIBUTE_THEME_ROOT];
        $themeAbsPath = rtrim($themeAbsPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        return $themeAbsPath . $relativeTemplatePath;
    }
}