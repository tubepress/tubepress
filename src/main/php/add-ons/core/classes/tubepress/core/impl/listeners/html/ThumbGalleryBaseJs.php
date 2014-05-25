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
class tubepress_core_impl_listeners_html_ThumbGalleryBaseJs
{
    /**
     * @var tubepress_core_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(tubepress_core_api_options_ContextInterface $context,
                                tubepress_core_api_event_EventDispatcherInterface $eventDispatcher)
    {
        $this->_context         = $context;
        $this->_eventDispatcher = $eventDispatcher;
    }

    public function onGalleryHtml(tubepress_core_api_event_EventInterface $event)
    {
        $galleryId = $this->_context->get(tubepress_core_api_const_options_Names::GALLERY_ID);
        $jsEvent   = $this->_eventDispatcher->newEventInstance(array());

        $this->_eventDispatcher->dispatch(tubepress_core_api_const_event_EventNames::CSS_JS_GALLERY_INIT, $jsEvent);

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
