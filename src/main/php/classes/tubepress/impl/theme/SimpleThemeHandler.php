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
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Theme Handler');
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
    function getTemplateInstance($pathToTemplate, $fallBackDirectory)
    {
        $debugEnabled = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        if ($debugEnabled) {

            $this->_logger->debug("Attempting to load template instance from $pathToTemplate with fallback directory "
                . " at $fallBackDirectory");
        }

        $currentTheme = $this->calculateCurrentThemeName();

        if ($debugEnabled) {

            $this->_logger->debug("Requested theme is '$currentTheme'");
        }

        $templateBuilder = tubepress_impl_patterns_sl_ServiceLocator::getTemplateBuilder();
        $filePath        = $this->_getFilePath($currentTheme, $pathToTemplate, $fallBackDirectory, $debugEnabled);
        $template        = $templateBuilder->getNewTemplateInstance($filePath);

        if ($debugEnabled) {

            $this->_logger->debug("Successfully loaded template from $filePath");
        }

        return $template;
    }

    /**
    * Returns the name of the current TubePress theme in use.
    *
    * @return string The current theme name, or 'default' if the default theme is in use or if there was a problem.
    */
    public function calculateCurrentThemeName()
    {
        $executionContext = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $currentTheme     = $executionContext->get(tubepress_api_const_options_names_Thumbs::THEME);

        if ($currentTheme == '') {

            $currentTheme = 'default';
        }

        return $currentTheme;
    }

    private function _getFilePath($currentTheme, $pathToTemplate, $fallBackDirectory, $debugEnabled)
    {
        $environmentDetector  = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();
        $userContentDirectory = $environmentDetector->getUserContentDirectory();

        /**
         * First try to load the template from system themes.
         */
        $filePath = TUBEPRESS_ROOT . "/src/main/resources/default-themes/$currentTheme/$pathToTemplate";

        if (is_readable($filePath)) {

            if ($debugEnabled) {

                $this->_logger->debug("Found $pathToTemplate first try at $filePath");
            }

            return $filePath;
        }

        if ($debugEnabled) {

            $this->_logger->debug("Didn't find $pathToTemplate at $filePath. Trying user theme directory next.");
        }

        /**
         * Next try to load the template from the user's theme directory.
         */
        $filePath = "$userContentDirectory/themes/$currentTheme/$pathToTemplate";

        if (is_readable($filePath)) {

            if ($debugEnabled) {

                $this->_logger->debug("Found $pathToTemplate in user's theme directory at $filePath");
            }

            return $filePath;
        }

        if ($debugEnabled) {

            $this->_logger->debug("Didn't find $pathToTemplate in system or user's theme directories. Falling back to $fallBackDirectory");
        }

        /**
         * Finally, load the template from the fallback directory.
         */
        return "$fallBackDirectory/$pathToTemplate";
    }
}