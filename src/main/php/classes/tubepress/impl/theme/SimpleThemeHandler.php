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
class tubepress_impl_theme_SimpleThemeHandler implements tubepress_spi_theme_ThemeHandler
{
    private static $_ARRAY_KEY_TEMPLATES = 'templates';
    private static $_ARRAY_KEY_PARENT    = 'parent';
    private static $_ARRAY_KEY_TITLE     = 'title';
    private static $_ARRAY_KEY_ABSPATH   = 'manifestPath';
    private static $_ARRAY_KEY_STYLES    = 'styles';
    private static $_ARRAY_KEY_SCRIPTS   = 'scripts';
    private static $_ARRAY_KEY_IS_SYS    = 'isSystemTheme';

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
     * @var array
     */
    private $_themeMap;

    public function __construct(array $themeMap)
    {
        $this->_logger   = ehough_epilog_LoggerFactory::getLogger('Theme Handler');
        $this->_themeMap = $themeMap;
    }

    /**
     * @param string|null $themeName The theme name. If null, TubePress will use the theme stored in the DB.
     *
     * @throws RuntimeException If there is a problem building this theme instance.
     *
     * @return tubepress_spi_theme_ThemeInterface The theme, never null.
     */
    public function getThemeInstance($themeName = null)
    {
        // TODO: Implement getThemeInstance() method.
    }

    /**
     * Gets an instance of a template appropriate for the current theme.
     *
     * @param string $pathToTemplate    The relative path (from the root of the user's theme directory,
     *                                  or the fallback directory) to the template.
     * @param string $fallBackDirectory The absolute path to a directory where this template (defined by the relative
     *                                  path, can be found). You should make sure that the template will *always* exist
     *                                  here.
     *
     * @throws RuntimeException If the template could not be found.
     *
     * @return ehough_contemplate_api_Template The template instance.
     */
    public function getTemplateInstance($pathToTemplate, $fallBackDirectory)
    {
        $pathToTemplate = ltrim($pathToTemplate, DIRECTORY_SEPARATOR);

        if ($this->_shouldLog()) {

            $this->_logger->debug(sprintf('Attempting to load template instance for "%s" with fallback directory at %s',
                $pathToTemplate, $fallBackDirectory));
        }

        $currentTheme = $this->_calculateCurrentThemeName();

        if ($this->_shouldLog()) {

            $this->_logger->debug("Using theme '$currentTheme'");
        }

        $templateBuilder = tubepress_impl_patterns_sl_ServiceLocator::getTemplateBuilder();
        $filePath        = $this->_getFilePath($currentTheme, $pathToTemplate, $fallBackDirectory);
        $template        = $templateBuilder->getNewTemplateInstance($filePath);

        if ($this->_shouldLog()) {

            $this->_logger->debug("Successfully loaded template from $filePath");
        }

        return $template;
    }

    /**
     * @return string[] URLs of CSS stylesheets required for the current theme. May be empty but never null.
     */
    public function getStyles()
    {
        $themeName = $this->_calculateCurrentThemeName();

        return $this->_recursivelyGetResourceUrlsForTheme($themeName, self::$_ARRAY_KEY_STYLES);
    }

    /**
     * @return string[] URLs of JS scripts required for the current theme. May be empty but never null.
     */
    public function getScripts()
    {
        $themeName = $this->_calculateCurrentThemeName();

        return $this->_recursivelyGetResourceUrlsForTheme($themeName, self::$_ARRAY_KEY_SCRIPTS);
    }

    private function _recursivelyGetResourceUrlsForTheme($themeName, $key)
    {
        $toReturn = $this->_getResourceUrlsForTheme($themeName, $key);

        if ($themeName === self::$_DEFAULT_THEME_NAME) {

            return $toReturn;
        }

        $parent = $this->_getParentThemeName($themeName);

        if ($parent === null) {

            return $toReturn;
        }

        return array_merge($this->_recursivelyGetResourceUrlsForTheme($parent, $key), $toReturn);
    }

    private function _getResourceUrlsForTheme($themeName, $key)
    {
        $toReturn            = array();
        $environmentDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();
        $themeAbsPath        = dirname($this->_themeMap[$themeName][self::$_ARRAY_KEY_ABSPATH]);
        $themeBaseName       = basename($themeAbsPath);

        foreach ($this->_themeMap[$themeName][$key] as $relativeResourcePath) {

            if ($this->_themeMap[$themeName][self::$_ARRAY_KEY_IS_SYS]) {

                $prefix = $environmentDetector->getBaseUrl() . '/src/main/resources/default-themes/';

            } else {

                $prefix = $environmentDetector->getUserContentUrl() . '/themes/';
            }

            $toReturn[] = $prefix . $themeBaseName . '/' . $relativeResourcePath;
        }

        return $toReturn;
    }

    /**
     * Returns the name of the current TubePress theme in use.
     *
     * @return string The current theme name, or 'tubepress/default' if the default theme is in use.
     */
    private function _calculateCurrentThemeName()
    {
        $executionContext = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $currentTheme     = $executionContext->get(tubepress_api_const_options_names_Thumbs::THEME);

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
        if (in_array($pathToTemplate, $this->_themeMap[$currentTheme][self::$_ARRAY_KEY_TEMPLATES])) {

            if ($this->_shouldLog()) {

                $this->_logger->debug(sprintf('Found direct hit for %s in "%s" theme.', $pathToTemplate, $currentTheme));
            }

            return $this->_toAbsPath($currentTheme, $pathToTemplate);
        }

        if ($this->_shouldLog()) {

            $this->_logger->debug(sprintf('No direct hit for %s in "%s" theme. Checking hierarchy', $pathToTemplate, $currentTheme));
        }

        while ($currentTheme !== self::$_DEFAULT_THEME_NAME) {

            /**
             * Next try the parent.
             */
            $parent = $this->_getParentThemeName($currentTheme);

            if ($parent === null) {

                break;
            }

            if ($this->_shouldLog()) {

                $this->_logger->debug(sprintf('Parent of "%s" theme is "%s".', $currentTheme, $parent));
            }

            $currentTheme = $parent;

            if (in_array($pathToTemplate, $this->_themeMap[$currentTheme][self::$_ARRAY_KEY_TEMPLATES])) {

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
        return "$fallBackDirectory/$pathToTemplate";
    }

    /**
     * @return string[] An associative array of strings, which may be empty but never null, of all known theme
     *                  names to their untranslated titles.
     */
    public function getMapOfAllThemeNamesToTitles()
    {
        $toReturn = array();

        foreach ($this->_themeMap as $themeName => $data) {

            $toReturn[$themeName] = $data[self::$_ARRAY_KEY_TITLE];
        }

        return $toReturn;
    }

    private function _toAbsPath($themeName, $relativeTemplatePath)
    {
        $themeAbsPath = dirname($this->_themeMap[$themeName][self::$_ARRAY_KEY_ABSPATH]);
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

    private function _getParentThemeName($themeName)
    {
        if (isset($this->_themeMap[$themeName][self::$_ARRAY_KEY_PARENT])
            && $this->_themeMap[$themeName][self::$_ARRAY_KEY_PARENT]
            && isset($this->_themeMap[$this->_themeMap[$themeName][self::$_ARRAY_KEY_PARENT]])) {

            return $this->_themeMap[$themeName][self::$_ARRAY_KEY_PARENT];

        }

        return null;
    }
}