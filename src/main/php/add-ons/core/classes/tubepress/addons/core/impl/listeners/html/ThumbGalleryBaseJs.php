<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Injects Ajax pagination code into the gallery's HTML.
 */
class tubepress_addons_core_impl_listeners_html_ThumbGalleryBaseJs
{
    public function onGalleryHtml(tubepress_api_event_EventInterface $event)
    {
        $context         = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $galleryId       = $context->get(tubepress_api_const_options_names_Advanced::GALLERY_ID);

        $jsEvent = new tubepress_spi_event_EventBase(array());

        $eventDispatcher->dispatch(tubepress_api_const_event_EventNames::CSS_JS_GALLERY_INIT, $jsEvent);

        $args   = $jsEvent->getSubject();
        $asJson = json_encode($args);
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
