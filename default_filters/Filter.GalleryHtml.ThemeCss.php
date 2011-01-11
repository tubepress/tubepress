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

/**
 * Injects theme CSS to the gallery's HTML, if necessary.
*/
class org_tubepress_impl_filter_GalleryHtml_ThemeCss
{
    const LOG_PREFIX = 'Theme CSS Filter';

    public function filter($html)
    {
        if (!is_string($html)) {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Filter invoked with a non-string :(');
            return $html;
        }

        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $themeHandler = $ioc->get('org_tubepress_theme_ThemeHandler');
        $currentTheme = $themeHandler->calculateCurrentThemeName($ioc);

        if ($currentTheme === 'default') {
            org_tubepress_log_Log::log(self::LOG_PREFIX, 'Default theme is in use. No need to inject extra CSS.');
            return $html;
        }

        /* get the CSS file's path on the filesystem */
        $cssPath = $themeHandler->getCssPath($currentTheme);

        if (!is_readable($cssPath) || strpos($cssPath, 'themes' . DIRECTORY_SEPARATOR . 'default') !== false) {
            org_tubepress_log_Log::log(self::LOG_PREFIX, 'No theme CSS found.');
            return $html;
        } else {
            org_tubepress_log_Log::log(self::LOG_PREFIX, 'Theme CSS found at <tt>%s</tt>', $cssPath);
        }

        return _injectCss($html, $themeHandler, $currentTheme);
    }

    private function _injectCss($html, $themeHandler, $currentTheme, $cssPath)
    {
        global $tubepress_base_url;

        $cssRelativePath      = $themeHandler->getCssPath($currentTheme, true);
        $baseInstallationPath = org_tubepress_util_FilesystemUtils::getTubePressBaseInstallationPath();
        $cssUrl               = "$tubepress_base_url/$cssRelativePath";

        org_tubepress_log_Log::log(self::LOG_PREFIX, 'Will inject CSS from <tt>%s</tt>', $cssUrl);
        
        $template = new org_tubepress_template_SimpleTemplate();
        $template->setPath("$baseInstallationPath/ui/lib/gallery_html_snippets/theme_loader.tpl.php");
        $template->setVariable(org_tubepress_template_Template::THEME_CSS, $cssUrl);

        return $html . $template->toString();
    }
}
