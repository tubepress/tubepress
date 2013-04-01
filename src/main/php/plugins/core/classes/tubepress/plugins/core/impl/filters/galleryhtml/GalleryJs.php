<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
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
        $context       = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $filterManager = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $encoder       = tubepress_impl_patterns_sl_ServiceLocator::getJsonEncoder();
        $galleryId     = $context->get(tubepress_api_const_options_names_Advanced::GALLERY_ID);

        $jsEvent = new tubepress_api_event_TubePressEvent(array());

        $filterManager->dispatch(tubepress_api_const_event_CoreEventNames::GALLERY_INIT_JS_CONSTRUCTION, $jsEvent);

        $args   = $jsEvent->getSubject();
        $asJson = $encoder->encode($args);
        $html   = $event->getSubject();

        $toReturn = $html . <<<EOT
<script type="text/javascript">
   var tubePressDomInjector = tubePressDomInjector || [], tubePressGalleryRegistrar = tubePressGalleryRegistrar || [];
       tubePressDomInjector.push(['loadGalleryJs']);
       tubePressGalleryRegistrar.push(['register', '$galleryId', $asJson ]);
</script>
EOT;

        $event->setSubject($toReturn);
    }
}
