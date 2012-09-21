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
 * Sets some base parameters to send to TubePressGallery.init().
 */
class tubepress_plugins_core_filters_galleryinitjs_GalleryInitJsBaseParams
{
    public function onGalleryInitJs(tubepress_api_event_TubePressEvent $event)
    {
        $context = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();

        $args = $event->getSubject();

        $args[tubepress_spi_const_js_TubePressGalleryInit::NAME_PARAM_AJAXPAGINATION] =
            $context->get(tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION) ? 'true' : 'false';

        $args[tubepress_spi_const_js_TubePressGalleryInit::NAME_PARAM_EMBEDDEDHEIGHT] =
            '"' . $context->get(tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT) . '"';

        $args[tubepress_spi_const_js_TubePressGalleryInit::NAME_PARAM_EMBEDDEDWIDTH] =
            '"' . $context->get(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH) . '"';

        $args[tubepress_spi_const_js_TubePressGalleryInit::NAME_PARAM_FLUIDTHUMBS] =
            $context->get(tubepress_api_const_options_names_Thumbs::FLUID_THUMBS) ? 'true' : 'false';

        $args[tubepress_spi_const_js_TubePressGalleryInit::NAME_PARAM_PLAYERLOC] =
            '"' . $context->get(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION) . '"';

        $args[tubepress_spi_const_js_TubePressGalleryInit::NAME_PARAM_SHORTCODE] =
            '"' . rawurlencode($context->toShortcode()) . '"';

        $event->setSubject($args);
    }
}