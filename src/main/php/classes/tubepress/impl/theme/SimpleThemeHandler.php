<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * A TubePress theme handler
 */
class tubepress_impl_theme_SimpleThemeHandler implements tubepress_spi_theme_ThemeHandler
{
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Theme Handler');
    }

    /**
     * Gets an instance of a template appropriate for the current theme.
     *
     * @param string $pathToTemplate The relative path (from the root of the theme directory) to the template.
     *
     * @return ehough_contemplate_api_Template The template instance.
     */
    public function getTemplateInstance($pathToTemplate)
    {
        $debugEnabled = $this->_logger->isDebugEnabled();

        if ($debugEnabled) {

            $this->_logger->debug("Attempting to load template instance from $pathToTemplate");
        }

        $currentTheme = $this->calculateCurrentThemeName();

        if ($debugEnabled) {

            $this->_logger->debug("Requested theme is '$currentTheme'");
        }

        $templateBuilder = tubepress_impl_patterns_ioc_KernelServiceLocator::getTemplateBuilder();
        $filePath        = $this->_getFilePath($currentTheme, $pathToTemplate, $debugEnabled);
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
        $executionContext = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $currentTheme     = $executionContext->get(tubepress_api_const_options_names_Thumbs::THEME);

        if ($currentTheme == '') {

            $currentTheme = 'default';
        }

        return $currentTheme;
    }

    private function _getFilePath($currentTheme, $pathToTemplate, $debugEnabled)
    {
        $environmentDetector = tubepress_impl_patterns_ioc_KernelServiceLocator::getEnvironmentDetector();

        $tubepressInstallationPath = $environmentDetector->getTubePressBaseInstallationPath();

        /* first try to load the theme from sys/ui */
        $filePath = "$tubepressInstallationPath/src/main/resources/default-themes/$currentTheme/$pathToTemplate";

        /* if that fails, try to load it from the user's theme directory */
        if (! is_readable($filePath)) {

            if ($debugEnabled) {

                $this->_logger->debug("Could not read file at $filePath");
            }

            $filePath = $environmentDetector->getUserContentDirectory() . "/themes/$currentTheme/$pathToTemplate";

            /* finally, just fall back to the default theme. */
            if (! is_readable($filePath)) {

                if ($debugEnabled) {

                    $this->_logger->debug("Could not read file at $filePath. Falling back to default.");
                }

                $filePath = "$tubepressInstallationPath/src/main/resources/default-themes/default/$pathToTemplate";
            }
        }

        return $filePath;
    }
}