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

tubepress_load_classes(array(
    'org_tubepress_api_filesystem_Explorer',
    'org_tubepress_api_const_filters_ExecutionPoint'
));

/**
 * Injects Ajax pagination code into the gallery's HTML, if necessary.
*/
class org_tubepress_impl_filters_html_GalleryJs
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
    public function filter($html, $galleryId)
    {
        if (!is_string($html)) {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Filter invoked with a non-string argument :(');
            return $html;
        }
        
        $ioc  = org_tubepress_impl_ioc_IocContainer::getInstance();
        $tpom = $ioc->get('org_tubepress_api_options_OptionsManager');
        
        $ajaxPagination   = $tpom->get(org_tubepress_api_const_options_names_Display::AJAX_PAGINATION) ? 'true' : 'false';
        $playerName       = $tpom->get(org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME);
        $shortcode        = rawurlencode($tpom->getShortcode());
        $fluidThumbs      = $tpom->get(org_tubepress_api_const_options_names_Display::FLUID_THUMBS) ? 'true' : 'false';
        
        return $html . <<<EOT
<script type="text/javascript">
	var TubePressGallery$galleryId = {
		ajaxPagination: $ajaxPagination,
		fluidThumbs: $fluidThumbs,
		shortcode: "$shortcode",
		playerLocationName: "$playerName"
    };
</script>';
EOT;
    }
}

$ioc      = org_tubepress_impl_ioc_IocContainer::getInstance();
$fm       = $ioc->get('org_tubepress_api_patterns_FilterManager');
$instance = $ioc->get('org_tubepress_impl_filters_html_GalleryJs');

$fm->registerFilter(org_tubepress_api_const_filters_ExecutionPoint::GALLERY_HTML, array($instance, 'filter'));
