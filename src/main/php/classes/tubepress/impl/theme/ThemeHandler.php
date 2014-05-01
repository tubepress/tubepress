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
class tubepress_impl_theme_ThemeHandler implements tubepress_spi_theme_ThemeHandlerInterface
{
    private static $_DEFAULT_THEME_NAME = 'tubepress/default';

    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * @var boolean
     */
    private $_shouldLog;

    /**
     * @var tubepress_api_environment_EnvironmentInterface
     */
    private $_environment;

    /**
     * @var array
     */
    private $_themeMap;

    /**
     * @var tubepress_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    public function __construct(array $themeMap,
                                tubepress_api_options_ContextInterface $context,
                                tubepress_api_environment_EnvironmentInterface $environment,
                                tubepress_api_url_UrlFactoryInterface $urlFactory)
    {
        $this->_logger      = ehough_epilog_LoggerFactory::getLogger('Theme Handler');
        $this->_themeMap    = $themeMap;
        $this->_environment = $environment;
        $this->_urlFactory  = $urlFactory;
        $this->_context     = $context;
    }

    /**
     * Gets an instance of a template appropriate for the current theme.
     *
     * @param string $pathToTemplate    The relative path (from the root of the user's theme directory,
     *                                  or the fallback directory) to the template.
     * @param string $fallBackDirectory The absolute path to a directory where this template, defined by the relative
     *                                  path, can be found. You should make sure that the template will *always* exist
     *                                  here.
     * @param string|null $themeName    The name of the theme to query, or null for the currently stored theme.
     *
     * @throws RuntimeException If the template could not be found.
     *
     * @return ehough_contemplate_api_Template The template instance.
     */
    public function getTemplateInstance($pathToTemplate, $fallBackDirectory, $themeName = null)
    {
        $pathToTemplate = ltrim($pathToTemplate, DIRECTORY_SEPARATOR);

        if ($this->_shouldLog()) {

            $this->_logger->debug(sprintf('Attempting to load template instance for "%s" with fallback directory at %s',
                $pathToTemplate, $fallBackDirectory));
        }

        if (!$themeName) {

            $themeName = $this->_calculateCurrentThemeName();
        }

        if ($this->_shouldLog()) {

            $this->_logger->debug("Getting template for theme '$themeName'");
        }

        $templateBuilder = tubepress_impl_patterns_sl_ServiceLocator::getTemplateBuilder();
        $filePath        = $this->_getFilePath($themeName, $pathToTemplate, $fallBackDirectory);
        $template        = $templateBuilder->getNewTemplateInstance($filePath);

        if ($this->_shouldLog()) {

            $this->_logger->debug("Successfully loaded template from $filePath");
        }

        return $template;
    }

    /**
     * @param string $themeName The name of the theme to query, or null for the currently stored theme.
     *
     * @return string[] URLs of CSS stylesheets required for the current theme. May be empty but never null.
     */
    public function getStyles($themeName = null)
    {
        if (!$themeName) {

            $themeName = $this->_calculateCurrentThemeName();
        }

        return $this->_recursivelyGetResourceUrlsForTheme($themeName, tubepress_impl_theme_ThemeBase::ATTRIBUTE_STYLES);
    }

    /**
     * @param string $themeName  The name of the theme to query, or null for the currently stored theme.
     *
     * @return string[] URLs of JS scripts required for the current theme. May be empty but never null.
     */
    public function getScripts($themeName = null)
    {
        if (!$themeName) {

            $themeName = $this->_calculateCurrentThemeName();
        }

        $themeScripts = $this->_recursivelyGetResourceUrlsForTheme($themeName, tubepress_impl_theme_ThemeBase::ATTRIBUTE_SCRIPTS);

        $baseUrl        = $this->_environment->getBaseUrl()->toString();
        $tubePressJsUrl = $baseUrl . '/src/main/web/js/tubepress.js';

        array_unshift($themeScripts, $tubePressJsUrl);

        return $themeScripts;
    }

    /**
     * @param string $themeName The name of the theme to query, or null for the currently stored theme.
     *
     * @return string[] URLs of screenshots for the current theme. May be empty but never null.
     */
    public function getScreenshots($themeName = null)
    {
        if (!$themeName) {

            $themeName = $this->_calculateCurrentThemeName();
        }

        $shots = $this->_getResourceUrlsForTheme($themeName, tubepress_impl_theme_ThemeBase::ATTRIBUTE_SCREENSHOTS);

        if (tubepress_impl_util_LangUtils::isAssociativeArray($shots)) {

            return $shots;
        }

        $toReturn = array();
        foreach ($shots as $shot) {

            $toReturn[$shot] = $shot;
        }

        return $toReturn;
    }

    private function _recursivelyGetResourceUrlsForTheme($themeName, $key)
    {
        $toReturn = $this->_getResourceUrlsForTheme($themeName, $key);

        if ($themeName === self::$_DEFAULT_THEME_NAME) {

            return $toReturn;
        }

        $parent = $this->_themeMap[$themeName][tubepress_addons_core_impl_ioc_compiler_ThemesPrimerPass::ATTRIBUTE_PARENT];

        if (!$parent) {

            return $toReturn;
        }

        return array_merge($this->_recursivelyGetResourceUrlsForTheme($parent, $key), $toReturn);
    }

    private function _getResourceUrlsForTheme($themeName, $key)
    {
        $toReturn            = array();
        $themeAbsPath        = $this->_themeMap[$themeName][tubepress_addons_core_impl_ioc_compiler_ThemesPrimerPass::ATTRIBUTE_THEME_ROOT];
        $themeBaseName       = basename($themeAbsPath);
        $resources           = $this->_themeMap[$themeName][$key];
        $isSystemTheme       = $this->_themeMap[$themeName][tubepress_addons_core_impl_ioc_compiler_ThemesPrimerPass::ATTRIBUTE_IS_SYSTEM];

        if (tubepress_impl_util_LangUtils::isAssociativeArray($resources)) {

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

            $prefix = $this->_environment->getBaseUrl()->toString() . '/src/main/web/themes/';

        } else {

            $prefix = $this->_environment->getUserContentUrl()->toString() . '/themes/';
        }

        return $prefix . $themeBaseName . '/' . $url;
    }

    /**
     * Returns the name of the current TubePress theme in use.
     *
     * @return string The current theme name, or 'tubepress/default' if the default theme is in use.
     */
    private function _calculateCurrentThemeName()
    {
        $currentTheme = $this->_context->get(tubepress_api_const_options_names_Thumbs::THEME);

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

    private function _getFilePath($currentTheme, $pathToTemplate, $fallBackDirectory)
    {
        /**
         * First try to load the template from the requested theme.
         */
        if (in_array($pathToTemplate, $this->_themeMap[$currentTheme][tubepress_addons_core_impl_ioc_compiler_ThemesPrimerPass::ATTRIBUTE_TEMPLATES])) {

            if ($this->_shouldLog()) {

                $this->_logger->debug(sprintf('Found direct hit for %s in "%s" theme.', $pathToTemplate, $currentTheme));
            }

            return $this->_toAbsPath($currentTheme, $pathToTemplate);
        }

        if ($this->_shouldLog()) {

            $this->_logger->debug(sprintf('No direct hit for %s in "%s" theme. Checking hierarchy', $pathToTemplate, $currentTheme));
        }

        while (true) {

            if (!isset($this->_themeMap[$currentTheme][tubepress_addons_core_impl_ioc_compiler_ThemesPrimerPass::ATTRIBUTE_PARENT])) {

                break;
            }

            /**
             * Next try the parent.
             */
            $parent = $this->_themeMap[$currentTheme][tubepress_addons_core_impl_ioc_compiler_ThemesPrimerPass::ATTRIBUTE_PARENT];

            if (!$parent) {

                break;
            }

            if ($this->_shouldLog()) {

                $this->_logger->debug(sprintf('Parent of "%s" theme is "%s".', $currentTheme, $parent));
            }

            $currentTheme = $parent;

            if (in_array($pathToTemplate, $this->_themeMap[$currentTheme][tubepress_addons_core_impl_ioc_compiler_ThemesPrimerPass::ATTRIBUTE_TEMPLATES])) {

                if ($this->_shouldLog()) {

                    $this->_logger->debug(sprintf('Found %s in theme "%s".', $pathToTemplate, $currentTheme));
                }

                return $this->_toAbsPath($currentTheme, $pathToTemplate);
            }
        }

        if ($this->_shouldLog()) {

            $this->_logger->debug("Didn't find $pathToTemplate in theme hierarchy. Falling back to $fallBackDirectory");
        }

        /**
         * Finally, load the template from the fallback directory.
         */
        return $fallBackDirectory . DIRECTORY_SEPARATOR . $pathToTemplate;
    }

    /**
     * @return string[] An associative array of strings, which may be empty but never null, of all known theme
     *                  names to their untranslated titles.
     */
    public function getMapOfAllThemeNamesToTitles()
    {
        $toReturn = array();

        foreach ($this->_themeMap as $themeName => $data) {

            $themeTitle           = $data[tubepress_addons_core_impl_ioc_compiler_ThemesPrimerPass::ATTRIBUTE_TITLE];
            $toReturn[$themeName] = $themeTitle;
        }

        return $toReturn;
    }

    private function _toAbsPath($themeName, $relativeTemplatePath)
    {
        $themeAbsPath = $this->_themeMap[$themeName][tubepress_addons_core_impl_ioc_compiler_ThemesPrimerPass::ATTRIBUTE_THEME_ROOT];
        $themeAbsPath = rtrim($themeAbsPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        return $themeAbsPath . $relativeTemplatePath;
    }

    private function _shouldLog()
    {
        if (!isset($this->_shouldLog)) {

            $this->_shouldLog = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);
        }

        return $this->_shouldLog;
    }
}