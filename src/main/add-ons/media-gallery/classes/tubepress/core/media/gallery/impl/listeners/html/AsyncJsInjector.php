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
class tubepress_core_media_gallery_impl_listeners_html_AsyncJsInjector
{
    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_event_api_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(tubepress_core_options_api_ContextInterface       $context,
                                tubepress_core_event_api_EventDispatcherInterface $eventDispatcher)
    {
        $this->_context         = $context;
        $this->_eventDispatcher = $eventDispatcher;
    }

    public function onGalleryHtml(tubepress_core_event_api_EventInterface $event)
    {
        $galleryId = $this->_context->get(tubepress_core_html_api_Constants::OPTION_GALLERY_ID);
        $jsEvent   = $this->_eventDispatcher->newEventInstance(array());

        $this->_eventDispatcher->dispatch(tubepress_core_media_gallery_api_Constants::EVENT_GALLERY_INIT_JS, $jsEvent);

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