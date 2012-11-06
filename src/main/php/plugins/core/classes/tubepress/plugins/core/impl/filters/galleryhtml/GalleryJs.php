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
 * Injects Ajax pagination code into the gallery's HTML.
 */
class tubepress_plugins_core_impl_filters_galleryhtml_GalleryJs
{
    private static $_NAME_CLASS = 'TubePressGallery';

    private static $_NAME_INIT_FUNCTION = 'init';

    public function onGalleryHtml(tubepress_api_event_TubePressEvent $event)
    {
        $context       = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $filterManager = tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher();
        $encoder       = tubepress_impl_patterns_ioc_KernelServiceLocator::getJsonEncoder();
        $galleryId     = $context->get(tubepress_api_const_options_names_Advanced::GALLERY_ID);

        $jsEvent = new tubepress_api_event_TubePressEvent(array());

        $filterManager->dispatch(tubepress_api_const_event_CoreEventNames::GALLERY_INIT_JS_CONSTRUCTION, $jsEvent);

        $args    = $jsEvent->getSubject();
        $asJson  = $encoder->encode($args);
        $html = $event->getSubject();

        $toReturn = $html
            . "\n"
            . '<script type="text/javascript">'
            . "\n\t"
            . self::$_NAME_CLASS
            . '.'
            . self::$_NAME_INIT_FUNCTION
            . "($galleryId, $asJson);\n</script>";

        $event->setSubject($toReturn);
    }
}
