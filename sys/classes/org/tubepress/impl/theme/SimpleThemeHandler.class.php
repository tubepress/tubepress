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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_names_Thumbs',
    'org_tubepress_api_filesystem_Explorer',
    'org_tubepress_api_exec_ExecutionContext',
    'org_tubepress_api_template_TemplateBuilder',
    'org_tubepress_api_theme_ThemeHandler',
    'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_impl_log_Log',
    'org_tubepress_impl_template_SimpleTemplate',
));

/**
 * A TubePress theme handler
 */
class org_tubepress_impl_theme_SimpleThemeHandler implements org_tubepress_api_theme_ThemeHandler
{
    const LOG_PREFIX = 'Theme Handler';

    /**
    * Gets an instance of a template appropriate for the current theme.
    *
    * @param string $pathToTemplate The relative path (from the root of the theme directory) to the template.
    *
    * @throws Exception If there was a problem.
    *
    * @return org_tubepress_api_template_Template The template instance.
    */
    public function getTemplateInstance($pathToTemplate)
    {
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Attempting to load template instance from %s', $pathToTemplate);

        $currentTheme = $this->calculateCurrentThemeName();
        $filePath     = $this->_getFilePath($currentTheme, $pathToTemplate);

        if (!is_readable($filePath)) {

            throw new Exception("Cannot read file at $filePath");
        }

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Successfully loaded template from %s', $filePath);

        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $tb  = $ioc->get(org_tubepress_api_template_TemplateBuilder::_);

        return $tb->getNewTemplateInstance($filePath);
    }

    /**
    * Returns the URL of the CSS stylesheet for the given theme.
    *
    * @param string  $currentTheme The name of the theme.
    * @param boolean $relative     Whether or not to include the full URL or just the portion relative to $tubepress_base_url
    *
    * @return string The URl of the CSS stylesheet.
    */
    public function getCssPath($currentTheme, $relative = false)
    {
        return $this->_getFilePath($currentTheme, 'style.css');
    }

    /**
    * Returns the name of the current TubePress theme in use.
    *
    * @return string The current theme name, or 'default' if the default theme is in use or if there was a problem.
    */
    public function calculateCurrentThemeName()
    {
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $execContext  = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $currentTheme = $execContext->get(org_tubepress_api_const_options_names_Thumbs::THEME);

        if ($currentTheme == '') {

            $currentTheme = 'default';
        }

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Requested theme is \'%s\'', $currentTheme);

        return $currentTheme;
    }

    /**
    * Find the absolute path of the user's content directory. In WordPress, this will be
    * wp-content/tubepress. In standalone PHP, this will be tubepress/content. Confusing, I know.
    *
    * @return string The absolute path of the user's content directory.
    */
    function getUserContentDirectory()
    {
        $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();
        $envDetector = $ioc->get(org_tubepress_api_environment_Detector::_);

        if ($envDetector->isWordPress()) {

            if (! defined('WP_CONTENT_DIR')) {

                define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
            }

            return WP_CONTENT_DIR . '/tubepress-content';

        } else {

            $fs = $ioc->get(org_tubepress_api_filesystem_Explorer::_);

            return $fs->getTubePressBaseInstallationPath() . '/tubepress-content';
        }
    }

    private function _getFilePath($currentTheme, $pathToTemplate)
    {
        $ioc                       = org_tubepress_impl_ioc_IocContainer::getInstance();
        $fs                        = $ioc->get(org_tubepress_api_filesystem_Explorer::_);
        $tubepressInstallationPath = $fs->getTubePressBaseInstallationPath();

        /* first try to load the theme from sys/ui */
        $filePath = "$tubepressInstallationPath/sys/ui/themes/$currentTheme/$pathToTemplate";

        /* if that fails, try to load it from the user's theme directory */
        if (!is_readable($filePath)) {

            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Could not read file at %s', $filePath);

            $filePath = $this->getUserContentDirectory() . "/themes/$currentTheme/$pathToTemplate";

            /* finally, just fall back to the default theme. */
            if (!is_readable($filePath)) {

                org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Could not read file at %s. Falling back to default.', $filePath);

                $filePath = "$tubepressInstallationPath/sys/ui/themes/default/$pathToTemplate";
            }
        }

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Loaded file at %s', $filePath);

        return $filePath;
    }
}