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
    'org_tubepress_api_const_js_TubePressGalleryInit',
    'org_tubepress_api_const_options_names_Advanced',
    'org_tubepress_api_const_options_names_Display',
    'org_tubepress_api_const_options_names_Embedded',
    'org_tubepress_api_provider_ProviderResult',
    'org_tubepress_api_theme_ThemeHandler',
    'org_tubepress_impl_log_Log'
));

/**
 * Injects Ajax pagination code into the gallery's HTML.
 */
class org_tubepress_impl_plugin_filters_galleryhtml_GalleryJs
{
    private static $_logPrefix = 'Gallery JS Filter';

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

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Filter invoked with a non-string argument :(');
            return $html;
        }

        $ioc           = org_tubepress_impl_ioc_IocContainer::getInstance();
        $context       = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $filterManager = $ioc->get(org_tubepress_api_plugin_PluginManager::_);
        $galleryId     = $context->get(org_tubepress_api_const_options_names_Advanced::GALLERY_ID);
        $args          = $filterManager->runFilters(org_tubepress_api_const_plugin_FilterPoint::JAVASCRIPT_GALLERYINIT, array());
        $argCount      = count($args);

        $toReturn = $html
            . "\n"
            . '<script type="text/javascript">'
            . "\n\t"
            . org_tubepress_api_const_js_TubePressGalleryInit::NAME_CLASS
            . '.'
            . org_tubepress_api_const_js_TubePressGalleryInit::NAME_INIT_FUNCTION
            . "($galleryId, {\n";

        $x = 0;
        foreach ($args as $name => $value) {

            $toReturn .= "\t\t$name : $value";

            if (($x + 1) < $argCount) {

                $toReturn .= ",\n";
            }
            $x++;
        }

        return $toReturn . "\n\t});\n</script>";

    }
}
