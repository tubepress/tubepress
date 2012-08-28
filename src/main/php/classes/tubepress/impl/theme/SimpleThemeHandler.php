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

    private $_templateBuilder;

    private $_context;

    private $_environmentDetector;

    public function __construct(
        ehough_contemplate_api_TemplateBuilder $builder,
        tubepress_spi_context_ExecutionContext $context,
        tubepress_spi_environment_EnvironmentDetector $environmentDetector
    )
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Theme Handler');

        $this->_templateBuilder     = $builder;
        $this->_context             = $context;
        $this->_environmentDetector = $environmentDetector;
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

        $filePath     = $this->_getFilePath($currentTheme, $pathToTemplate, $debugEnabled);
        $template     = $this->_templateBuilder->getNewTemplateInstance($filePath);

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
        $currentTheme = $this->_context->get(tubepress_api_const_options_names_Thumbs::THEME);

        if ($currentTheme == '') {

            $currentTheme = 'default';
        }

        return $currentTheme;
    }

    private function _getFilePath($currentTheme, $pathToTemplate, $debugEnabled)
    {
        $tubepressInstallationPath = $this->_environmentDetector->getTubePressBaseInstallationPath();

        /* first try to load the theme from sys/ui */
        $filePath = "$tubepressInstallationPath/src/main/resources/default-themes/$currentTheme/$pathToTemplate";

        /* if that fails, try to load it from the user's theme directory */
        if (! is_readable($filePath)) {

            if ($debugEnabled) {

                $this->_logger->debug("Could not read file at $filePath");
            }

            $filePath = $this->_environmentDetector->getUserContentDirectory() . "/themes/$currentTheme/$pathToTemplate";

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