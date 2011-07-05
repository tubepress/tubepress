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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_names_Advanced',
    'org_tubepress_api_const_options_names_Display',
    'org_tubepress_api_const_options_names_Embedded',
    'org_tubepress_api_provider_ProviderResult',
    'org_tubepress_api_theme_ThemeHandler',
    'org_tubepress_impl_log_Log'
));

/**
 * Injects Ajax pagination code into the gallery's HTML, if necessary.
 */
class org_tubepress_impl_plugin_filters_galleryhtml_GalleryJs
{
    const LOG_PREFIX = 'Gallery JS Filter';

    /**
     * Filters the HTML for the gallery.
     *
     * @param string $html      The gallery HTML.
     * @param string $galleryId The current gallery ID
     *
     * @return string The modified HTML
     */
    public function alter_galleryHtml($html, org_tubepress_api_provider_ProviderResult $providerResult, $page, $providerName)
    {
        if (!is_string($html)) {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Filter invoked with a non-string argument :(');
            return $html;
        }

        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();
        $context = $ioc->get('org_tubepress_api_exec_ExecutionContext');

        $ajaxPagination   = $context->get(org_tubepress_api_const_options_names_Display::AJAX_PAGINATION) ? 'true' : 'false';
        $playerName       = $context->get(org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME);
        $shortcode        = rawurlencode($context->toShortcode());
        $fluidThumbs      = $context->get(org_tubepress_api_const_options_names_Display::FLUID_THUMBS) ? 'true' : 'false';
        $height           = $context->get(org_tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT);
        $width            = $context->get(org_tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH);
        $theme            = rawurlencode($this->_getThemeName($ioc));
        $galleryId        = $context->get(org_tubepress_api_const_options_names_Advanced::GALLERY_ID);

        return $html . <<<EOT
<script type="text/javascript">
	TubePressGallery.init($galleryId, {
		ajaxPagination: $ajaxPagination,
		fluidThumbs: $fluidThumbs,
		shortcode: "$shortcode",
		playerLocationName: "$playerName",
		embeddedHeight: "$height",
		embeddedWidth: "$width",
		themeCSS: "$theme"
    });
</script>
EOT;
    }

    private function _getThemeName($ioc)
    {
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $themeHandler = $ioc->get('org_tubepress_api_theme_ThemeHandler');
        $currentTheme = $themeHandler->calculateCurrentThemeName();
        $fe           = $ioc->get('org_tubepress_api_filesystem_Explorer');
        $basePath     = $fe->getTubePressBaseInstallationPath();

        if ($currentTheme === 'default') {
            return '';
        }

        /* get the CSS file's path on the filesystem */
        $cssPath = $themeHandler->getCssPath($currentTheme);

        if (!is_readable($cssPath) || strpos($cssPath, 'themes' . DIRECTORY_SEPARATOR . 'default') !== false) {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'No theme CSS found.');
            return '';
        } else {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Theme CSS found at <tt>%s</tt>', $cssPath);
        }

        return str_replace($basePath, '', $cssPath);
    }
}
