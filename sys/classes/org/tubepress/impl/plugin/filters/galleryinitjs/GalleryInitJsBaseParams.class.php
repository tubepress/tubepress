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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_js_TubePressGalleryInitInit',
    'org_tubepress_api_const_options_names_Thumbs',
    'org_tubepress_api_const_options_names_Embedded',
    'org_tubepress_api_exec_ExecutionContext',
    'org_tubepress_api_filesystem_Explorer',
    'org_tubepress_api_theme_ThemeHandler',
    'org_tubepress_impl_log_Log'
));

/**
 * Sets some base parameters to send to TubePressGallery.init().
 */
class org_tubepress_impl_plugin_filters_galleryinitjs_GalleryInitJsBaseParams
{
    private static $_logPrefix = 'Base Init Params Filter';

    /**
     * Modify the name-value pairs sent to TubePressGallery.init().
     *
     * @param array $args An associative array (name => value) of args to send to TubePressGallery.init();
     *
     * @return array The (possibly modified) array. Never null.
     *
     */
    public function alter_galleryInitJavaScript($args)
    {
        if (!is_array($args)) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Filter invoked with a non-array argument :(');
            return $args;
        }

        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();
        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);

        $args[org_tubepress_api_const_js_TubePressGalleryInit::NAME_PARAM_AJAXPAGINATION] =
            $context->get(org_tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION) ? 'true' : 'false';

        $args[org_tubepress_api_const_js_TubePressGalleryInit::NAME_PARAM_EMBEDDEDHEIGHT] =
            '"' . $context->get(org_tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT) . '"';

        $args[org_tubepress_api_const_js_TubePressGalleryInit::NAME_PARAM_EMBEDDEDWIDTH] =
            '"' . $context->get(org_tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH) . '"';

        $args[org_tubepress_api_const_js_TubePressGalleryInit::NAME_PARAM_FLUIDTHUMBS] =
            $context->get(org_tubepress_api_const_options_names_Thumbs::FLUID_THUMBS) ? 'true' : 'false';

        $args[org_tubepress_api_const_js_TubePressGalleryInit::NAME_PARAM_PLAYERLOC] =
            '"' . $context->get(org_tubepress_api_const_options_names_Embedded::PLAYER_LOCATION) . '"';

        $args[org_tubepress_api_const_js_TubePressGalleryInit::NAME_PARAM_SHORTCODE] =
            '"' . rawurlencode($context->toShortcode()) . '"';

        $args[org_tubepress_api_const_js_TubePressGalleryInit::NAME_PARAM_THEME] =
            '"' . rawurlencode($this->_getThemeName($ioc)) . '"';

        return $args;
    }

    private function _getThemeName($ioc)
    {
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $themeHandler = $ioc->get(org_tubepress_api_theme_ThemeHandler::_);
        $fe           = $ioc->get(org_tubepress_api_filesystem_Explorer::_);
        $currentTheme = $themeHandler->calculateCurrentThemeName();
        $basePath     = $fe->getTubePressBaseInstallationPath();

        if ($currentTheme === 'default') {

            return '';
        }

        /* get the CSS file's path on the filesystem */
        $cssPath = $themeHandler->getCssPath($currentTheme);

        if (!is_readable($cssPath) || strpos($cssPath, 'themes' . DIRECTORY_SEPARATOR . 'default') !== false) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'No theme CSS found.');
            return '';

        } else {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Theme CSS found at <tt>%s</tt>', $cssPath);
        }

        return str_replace($basePath, '', $cssPath);
    }
}