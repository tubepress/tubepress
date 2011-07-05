<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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
    'org_tubepress_api_const_options_names_Display',
    'org_tubepress_api_filesystem_Explorer',
    'org_tubepress_api_exec_ExecutionContext',
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

    public function getTemplateInstance($pathToTemplate)
    {
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Attempting to load template instance from %s', $pathToTemplate);

        $currentTheme = $this->calculateCurrentThemeName();
        $filePath     = self::_getFilePath($currentTheme, $pathToTemplate);

        if (!is_readable($filePath)) {
            throw new Exception("Cannot read file at $filePath");
        }

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Successfully loaded template from %s', $filePath);

        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $tb = $ioc->get('org_tubepress_api_template_TemplateBuilder');
        return $tb->getNewTemplateInstance($filePath);
    }

    public function getCssPath($currentTheme, $relative = false)
    {
        return self::_getFilePath($currentTheme, 'style.css', $relative);
    }

    public function calculateCurrentThemeName()
    {
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $execContext  = $ioc->get('org_tubepress_api_exec_ExecutionContext');
        $currentTheme = $execContext->get(org_tubepress_api_const_options_names_Display::THEME);
        if ($currentTheme == '') {
            $currentTheme = 'default';
        }
        return $currentTheme;
    }

    private static function _getFilePath($currentTheme, $pathToTemplate, $relative = false)
    {
        $ioc                       = org_tubepress_impl_ioc_IocContainer::getInstance();
        $fs                        = $ioc->get('org_tubepress_api_filesystem_Explorer');
        $tubepressInstallationPath = $fs->getTubePressBaseInstallationPath();
        $filePath                  = "$tubepressInstallationPath/sys/ui/themes/$currentTheme/$pathToTemplate";

        if ($currentTheme === 'default' || !is_readable($filePath)) {
            $filePath = "$tubepressInstallationPath/content/themes/$currentTheme/$pathToTemplate";
        }
        if ($currentTheme === 'default' || !is_readable($filePath)) {
            $filePath = "$tubepressInstallationPath/sys/ui/themes/default/$pathToTemplate";
        }

        if ($relative) {
            return str_replace("$tubepressInstallationPath/", '', $filePath);
        }

        return $filePath;
    }
}

